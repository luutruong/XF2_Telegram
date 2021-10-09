<?php

namespace Truonglv\Telegram;

use Truonglv\Telegram\Command\Help;

class Listener
{
    /**
     * @param \XF\App $app
     * @return void
     */
    public static function onAppSetup(\XF\App $app)
    {
        $container = $app->container();
        $container[App::KEY_CONTAINER_TELEGRAM] = function () {
            $token = \XF::app()->options()->telegramBot_botToken;
            if (\strlen($token) === 0) {
                return null;
            }

            $class = \XF::extendClass('Truonglv\\Telegram\\Telegram');

            /** @var Telegram $api */
            $api = new $class($token);
            $api->addCommand('help', Help::class);

            return $api;
        };
    }
}
