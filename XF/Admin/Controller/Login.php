<?php

namespace Truonglv\Telegram\XF\Admin\Controller;

use Truonglv\Telegram\App;

class Login extends XFCP_Login
{
    public function actionLogin()
    {
        $api = App::getTelegram();
        if ($api !== null && $this->options()->telegram_notifyAdminAccess > 0) {
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
