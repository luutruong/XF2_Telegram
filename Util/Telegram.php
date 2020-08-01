<?php

namespace Truonglv\TelegramBot\Util;

use Truonglv\TelegramBot\Error;

class Telegram
{
    /**
     * @param string $message
     * @return bool
     */
    public static function sendMessage($message)
    {
        $client = \XF::app()->http()->client();

        $token = \XF::app()->options()->telegramBot_botToken;
        $chatId = \XF::app()->options()->telegramBot_chatId;

        if (\strlen($token) === 0 || \strlen($chatId) === 0) {
            return false;
        }

        try {
            $client->post('https://api.telegram.org/bot' . $token . '/sendMessage', [
                'form_params' => [
                    'chat_id' => $chatId,
                    'text' => \utf8_substr($message, 0, 4096)
                ]
            ]);

            return true;
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            $logger = \XF::app()->error();

            if ($logger instanceof Error) {
                $logger->setTelEnableLogs(false);
                $logger->logError(
                    '[tl] TelErrorLog: Failed to log errors to Telegram. $error='
                    . $e->getMessage()
                );
                $logger->setTelEnableLogs(true);
            }
        }

        return false;
    }
}
