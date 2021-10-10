<?php

namespace Truonglv\Telegram\Command;

use Truonglv\Telegram\Telegram;

abstract class AbstractHandler
{
    protected \XF\App $app;
    protected Telegram $telegram;
    protected string $command;

    abstract public function handle(): void;
    abstract public function description(): string;

    public function __construct(\XF\App $app, Telegram $telegram, string $command)
    {
        $this->app = $app;
        $this->telegram = $telegram;
        $this->command = $command;
    }
}
