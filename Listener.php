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
            return new Error($app);
        };
    }
}
