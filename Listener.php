<?php

namespace Truonglv\TelegramBot;

class Listener
{
    /**
     * @param \XF\App $app
     * @return void
     */
    public static function onAppSetup(\XF\App $app)
    {
        $container = $app->container();

        $container['error'] = function () use ($app) {
            return new XF\Error($app);
        };

        $container['telegramBot'] = function () {
            $token = \XF::app()->options()->telegramBot_botToken;
            if (\strlen($token) === 0) {
                return null;
            }

            $class = \XF::extendClass('Truonglv\\TelegramBot\\Telegram');

            return new $class($token);
        };
    }
}
