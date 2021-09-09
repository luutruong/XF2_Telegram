<?php

namespace Truonglv\TelegramBot;

class App
{
    public static function getTelegramApi(): ?Telegram
    {
        /** @var Telegram|null $api */
        $api = \XF::app()->container('telegramBot');

        return $api;
    }
}
