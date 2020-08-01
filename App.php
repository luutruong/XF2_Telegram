<?php

namespace Truonglv\TelegramBot;

class App
{
    /**
     * @return Telegram|null
     */
    public static function getTelegramApi()
    {
        /** @var Telegram|null $api */
        $api = \XF::app()->container('telegramBot');

        return $api;
    }
}
