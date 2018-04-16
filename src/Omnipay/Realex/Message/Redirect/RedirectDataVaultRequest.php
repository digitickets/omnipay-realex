<?php

namespace Omnipay\Realex\Message\Redirect;

/**
 * Realex Redirect Authorize Request
 */
class RedirectDataVaultRequest extends AbstractRequest
{
    public function getData()
    {
        return null;
    }

    public function getBaseData($autoSettle = '1', $card = null)
    {
        $data = array(
            'ACCOUNT' => $this->getAccount(),
            'MERCHANT_ID' => $this->getMerchantId(),
            'ORDER_ID' => $this->getTransactionId(),
            'CURRENCY' => $this->getCurrency(),
            'MERCHANT_RESPONSE_URL' => $this->getReturnUrl(),
            'AMOUNT' => round($this->getAmount() * 100),
            'TIMESTAMP' => gmdate('YmdHis'),
            'AUTO_SETTLE_FLAG' => $autoSettle,
            'HPP_VERSION' => 2
        );

        if (isset($this->getData()['extraData']) && is_array($this->getData()['extraData'])) {
            $data = array_merge($data, $this->getData()['extraData']);
        }
        $data['SHA1HASH'] = $this->createSignature($data, 'sha1', $card);
        return $data;
    }

    /*
     * "timestamp.merchantid.orderid.amount.currency.payerref.paymentmethod"
     */

    public function createSignature($data, $method = 'sha1', $card = null)
    {
        $hash = $method(implode('.', array(
            $data['TIMESTAMP'],
            $data['MERCHANT_ID'],
            $data['ORDER_ID'],
            $data['AMOUNT'],
            $data['CURRENCY'],
            $data['PAYER_REF'],
            ''
        )));

        return $method($hash . '.' . $this->getSecret());
    }

    public function getRedirectData()
    {
        return $this->getBaseData();
    }

    public function sendData($data)
    {
        return $this->response = new RedirectDataVaultResponse($this, $data);
    }
}
