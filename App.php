<?php

namespace Truonglv\Telegram;

use Truonglv\Telegram\Command\AbstractHandler;

class App
{
    const KEY_CONTAINER_TELEGRAM = 'telegram';

    public static function getTelegram(): ?Telegram
    {
        /** @var Telegram|null $api */
        $api = \XF::app()->container(self::KEY_CONTAINER_TELEGRAM);

        return $api;
    }

    public static function command(string $class): AbstractHandler
    {
        $telegram = static::getTelegram();
        if ($telegram === null) {
            throw new \InvalidArgumentException('Telegram was not setup');
        }

        $class = \XF::app()->extendClass($class);
        /** @var AbstractHandler $obj */
        $obj = new $class(\XF::app(), $telegram);

        return $obj;
    }
}
