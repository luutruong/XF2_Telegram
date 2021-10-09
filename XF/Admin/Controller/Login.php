<?php

namespace Truonglv\TelegramBot\XF\Admin\Controller;

use Truonglv\TelegramBot\App;

class Login extends XFCP_Login
{
    public function actionLogin()
    {
        $api = App::getTelegramApi();
        if ($api !== null && $this->options()->telegramBot_notifyAdminAccess > 0) {
            $messages = [];

            $messages[] = 'New login to ACP!';
            $messages[] = 'Account: ' . $this->filter('login', 'str');
            $messages[] = 'IP: ' . $this->request()->getIp();
            $messages[] = 'Browser: ' . $this->request()->getUserAgent();

            $api->sendMessage(\implode("\n", $messages));
        }

        return parent::actionLogin();
    }
}
