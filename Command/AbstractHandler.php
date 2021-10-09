<?php

namespace Truonglv\TelegramBot\Command;

use Truonglv\TelegramBot\Telegram;

abstract class AbstractHandler
{
    protected \XF\App $app;
    protected Telegram $telegram;

    abstract public function handle(): void;
    abstract public function description(): string;

    public function __construct(\XF\App $app, Telegram $telegram)
    {
        $this->app = $app;
        $this->telegram = $telegram;
    }
}
