<?php

namespace Truonglv\Telegram\XF\Entity;

use function floatval;
use function in_array;
use Truonglv\Telegram\App;
use XF\Entity\PurchaseRequest;

class PaymentProviderLog extends XFCP_PaymentProviderLog
{
    protected function _postSave()
    {
        parent::_postSave();

        $telegram = App::getTelegram();
        if ($this->isChanged('log_type')
            && in_array($this->log_type, ['payment', 'info'], true)
            && $telegram !== null
        ) {
            /** @var PurchaseRequest|null $purchaseRequest */
            $purchaseRequest = $this->PurchaseRequest;
            $currency = '';
            $costAmount = 0.0;

            if ($purchaseRequest !== null) {
                $currency = $purchaseRequest->cost_currency;
                $costAmount = $purchaseRequest->cost_amount;
            }

            if ($currency === '' && isset($this->log_details['mc_currency'])) {
                $currency = $this->log_details['mc_currency'];
            }
            if ($costAmount === 0.0 && isset($this->log_details['mc_gross'])) {
                $costAmount = floatval($this->log_details['mc_gross']);
            }

            $message = $this->log_type . ': ' . $this->log_message;
            $message .= "\n" . sprintf(
                '- %s (%s %.02f)',
                $this->provider_id,
                $currency,
                $costAmount
            );
            $message .= "\n- Purchaser: " . ($purchaseRequest->User->username ?? 'Unknown user');

            $telegram->sendMessage($message);
        }
    }
}
