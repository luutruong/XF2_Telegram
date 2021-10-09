<?php

namespace Truonglv\TelegramBot\Command;

use Truonglv\TelegramBot\App;

class Help extends AbstractHandler
{
    public function handle(): void
    {
        $responses = [];
        $commands = $this->telegram->getCommands();

        foreach ($commands as $command => $handler) {
            $cmd = App::command($handler);
            $responses[] = sprintf('/%s - %s', $command, $cmd->description());
        }

        if (count($responses) > 0) {
            $this->telegram->sendMessage(implode("\n", $responses));
        }
    }

    public function description(): string
    {
        return 'Print all available commands.';
    }
}
