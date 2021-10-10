<?php

namespace Truonglv\Telegram;

use Truonglv\Telegram\Command\Help;
use Truonglv\Telegram\Command\Thread;

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
            $token = \XF::app()->options()->telegram_botToken;
            if (\strlen($token) === 0) {
                return null;
            }

            $class = \XF::extendClass('Truonglv\\Telegram\\Telegram');

            /** @var Telegram $api */
            $api = new $class($token);
            $api->addCommand('help', Help::class);

            // threads
            $api->addCommand('most_viewed_threads', Thread::class);
            $api->addCommand('most_replied_threads', Thread::class);
            $api->addCommand('recent_threads', Thread::class);

            return $api;
        };
    }
}
