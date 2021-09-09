<?php

namespace Truonglv\TelegramBot;

class Telegram
{
    const API_ENDPOINT = 'https://api.telegram.org';

    /**
     * @var string
     */
    protected $token;

    public function __construct(string $token)
    {
        $this->token = $token;
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
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            \XF::logException($e, false, '[tl] Telegram Bot: ');
        }

        if ($response === null || $response->getStatusCode() !== 200) {
            return null;
        }

        return \json_decode($response->getBody()->getContents(), true);
    }
}
