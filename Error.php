<?php

namespace Truonglv\TeleErrorLog;

use Truonglv\TeleErrorLog\Util\Telegram;

class Error extends \XF\Error
{
    /**
     * @var bool
     */
    protected $telEnableLogs = true;

    /**
     * @param bool $telEnableLogs
     * @return void
     */
    public function setTelEnableLogs(bool $telEnableLogs)
    {
        $this->telEnableLogs = $telEnableLogs;
    }

    /**
     * @param mixed $e
     * @param mixed $rollback
     * @param mixed $messagePrefix
     * @param mixed $forceLog
     * @return bool
     */
    public function logException($e, $rollback = false, $messagePrefix = '', $forceLog = false)
    {
        if ($this->telEnableLogs) {
            $hasRollback = (bool) $rollback;
            if (\XF::app()->options()->tel_takeover == 1
                && !$hasRollback
            ) {
                return $this->telLogException($e, $messagePrefix);
            } else {
                $this->telLogException($e, $messagePrefix);
            }
        }

        return parent::logException($e, $rollback, $messagePrefix, $forceLog);
    }

    /**
     * @param mixed $e
     * @param string $messagePrefix
     * @return bool
     */
    protected function telLogException($e, $messagePrefix)
    {
        $isValidArg = ($e instanceof \Exception || $e instanceof \Throwable);
        if (!$isValidArg) {
            $e = new \ErrorException('Non-exception passed to logException. See trace for details.');
        }

        $rootDir = \XF::getRootDirectory() . \XF::$DS;
        $file = \str_replace($rootDir, '', $e->getFile());

        $requestInfo = \XF::dumpSimple($this->getRequestDataForExceptionLog(), true);
        if (\strlen($messagePrefix) > 0) {
            $messagePrefix = \trim($messagePrefix) . ' ';
        }

        $trace = $this->getTraceStringFromThrowable($e);

        $traceExtras = $this->addExtrasToTrace($e);
        if ($traceExtras !== '') {
            $trace = $traceExtras . "\n------------\n\n" . $trace;
        }

        $exceptionMessage = $this->adjustExceptionMessage($e->getMessage(), $e);
        $fileName = \utf8_substr($file, 0, 255);
        $line = $e->getLine();

        $title = \utf8_substr($messagePrefix . $exceptionMessage, 0, 500);

        $message = <<<EOT
{$title}
{$fileName}:{$line}

Stack trace
<pre>
    <code class="language-php">
        {$trace}
    </code>
</pre>

Request state
<pre>
    {$requestInfo}
</pre>
EOT;

        return Telegram::sendMessage($message);
    }
}
