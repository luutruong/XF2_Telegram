<?php

namespace Truonglv\TelegramBot;

use Truonglv\TelegramBot\Command\AbstractHandler;

class App
{
    public static function getTelegramApi(): ?Telegram
    {
        /** @var Telegram|null $api */
        $api = \XF::app()->container('telegramBot');

        return $api;
    }

    public static function command(string $class): AbstractHandler
    {
        $telegram = static::getTelegramApi();
        if ($telegram === null) {
            throw new \InvalidArgumentException('Telegram was not setup');
        }

        $class = \XF::app()->extendClass($class);
        /** @var AbstractHandler $obj */
        $obj = new $class(\XF::app(), $telegram);

        return $obj;
    }
}
