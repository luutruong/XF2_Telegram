<?php

namespace Truonglv\Telegram;

class Telegram
{
    const API_ENDPOINT = 'https://api.telegram.org';

    protected string $token;

    protected array $commands = [];

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    public function addCommand(string $name, string $handlerClass): self
    {
        if (!class_exists($handlerClass)) {
            throw new \InvalidArgumentException("Handler class '$handlerClass' not exists");
        }

        $this->commands[strtolower($name)] = $handlerClass;

        return $this;
    }

    public function setWebhook(string $url): bool
    {
        $result = $this->sendRequest('POST', 'setWebhook', [
            'form_params' => [
                'url' => $url,
            ],
        ]);

        return isset($result['ok']) && $result['ok'] === true;
    }

    public function deleteWebhook(): bool
    {
        $info = $this->sendRequest('POST', 'deleteWebhook', []);

        return isset($info['ok']) && $info['ok'] === true;
    }

    public function handleWebhookEvent(string $inputRaw): void
    {
        $json = json_decode($inputRaw, true);
        if (!is_array($json) || !isset($json['message'])) {
            throw new \InvalidArgumentException('Invalid payload');
        }

        $sentFrom = $json['message']['from'] ?? null;
        if ($sentFrom === null) {
            throw new \InvalidArgumentException('Invalid payload');
        }

        $isBot = $sentFrom['is_bot'] ?? null;
        if ($isBot !== false) {
            throw new \InvalidArgumentException('Unknown message type');
        }

        $text = $json['message']['text'] ?? '';
        if (strlen($text) === 0) {
            throw new \InvalidArgumentException('Message is empty');
        }

        if (substr($text, 0, 1) !== '/') {
            // not a valid command
            return;
        }

        $command = ltrim($text, '/');
        $handler = $this->commands[$command] ?? null;

        if ($handler === null) {
            return;
        }

        $handler = App::command($handler);
        $handler->handle();
    }

    /**
     * @param string $message
     * @param array $params
     * @link https://core.telegram.org/bots/api#sendmessage
     * @return array|null
     */
    public function sendMessage(string $message, array $params = []): ?array
    {
        if (!isset($params['chat_id'])) {
            $chatId = \XF::app()->options()->telegramBot_chatId;
            if (\strlen($chatId) === 0) {
                return null;
            }

            $params['chat_id'] = $chatId;
        }

        if (isset($params['parse_mode']) && $params['parse_mode'] === 'MarkdownV2') {
            $params['text'] = \strtr($params['text'], [
                '_' => '\\_',
                '*' => '\\*',
                '[' => '\\[',
                ']' => '\\]',
                '(' => '\\(',
                ')' => '\\)',
                '~' => '\\~',
                '`' => '\\`',
                '>' => '\\>',
                '#' => '\\#',
                '+' => '\\+',
                '-' => '\\-',
                '=' => '\\=',
                '|' => '\\|',
                '{' => '\\{',
                '}' => '\\}',
                '.' => '\\.',
                '!' => '\\!'
            ]);
        }
        $params['text'] = \utf8_substr($message, 0, 4096);

        return $this->sendRequest('POST', 'sendMessage', [
            'form_params' => $params
        ]);
    }

    protected function sendRequest(string $method, string $endPoint, array $options): ?array
    {
        $client = \XF::app()->http()->client();
        $response = null;

        try {
            $response = $client->request($method, self::API_ENDPOINT . '/bot' . $this->token . '/' . $endPoint, $options);
        } catch (\Throwable $e) {
            \XF::logException($e, false, '[tl] Telegram Bot: ');
        }

        if ($response === null || $response->getStatusCode() !== 200) {
            return null;
        }

        return \json_decode($response->getBody()->getContents(), true);
    }
}
