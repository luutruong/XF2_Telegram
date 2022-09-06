<?php

namespace Truonglv\Telegram\XF\Pub\Controller;

use Throwable;
use XF\Mvc\ParameterBag;
use Truonglv\Telegram\App;

class Misc extends XFCP_Misc
{
    public function actionTelegramWebhook()
    {
        $telegramApi = App::getTelegram();
        if ($telegramApi === null) {
            die('Telegram was not setup');
        }

        if ($this->filter('set', 'bool') === true) {
            $result = $telegramApi->setWebhook($this->buildLink('canonical:misc/telegram-webhook'));
            die('Set webhook -> ' . ($result ? 'ok' : 'failed'));
        } elseif ($this->filter('delete', 'bool') === true) {
            $result = $telegramApi->deleteWebhook();
            die('Delete webhook -> ' . ($result ? 'ok' : 'failed'));
        }

        try {
            $telegramApi->handleWebhookEvent($this->request()->getInputRaw());
        } catch (Throwable $e) {
            // keep silent
        }

        die('ok');
    }

    /**
     * @param mixed $action
     * @param ParameterBag $params
     * @return void
     */
    public function checkCsrfIfNeeded($action, ParameterBag $params)
    {
        if (strtolower($action) === 'telegram' . 'webhook') {
            return;
        }

        parent::checkCsrfIfNeeded($action, $params);
    }
}
