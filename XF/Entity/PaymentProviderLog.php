<?php

namespace Truonglv\Telegram\XF\Entity;

use Truonglv\Telegram\App;
use XF\Entity\PurchaseRequest;

class PaymentProviderLog extends XFCP_PaymentProviderLog
{
    protected function _postSave()
    {
        parent::_postSave();

        $telegram = App::getTelegram();
        if ($this->isChanged('log_type')
            && $this->log_type === 'payment'
            && $telegram !== null
        ) {
            /** @var PurchaseRequest|null $purchaseRequest */
            $purchaseRequest = $this->PurchaseRequest;
            if ($purchaseRequest === null) {
                return;
            }

            $message = $this->log_message;
            $message .= "\n" . sprintf(
                '%s (%s %f)',
                $this->provider_id,
                $purchaseRequest->cost_currency,
                $purchaseRequest->cost_amount
            );
            $message .= "\nPurchaser: " . ($purchaseRequest->User->username ?? '_');

            $telegram->sendMessage($message);
        }
    }
}
