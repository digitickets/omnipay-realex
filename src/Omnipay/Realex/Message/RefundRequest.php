<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Realex Refund Request
 */
class RefundRequest extends RemoteAbstractRequest
{
    protected $endpoint = 'https://epage.payandshop.com/epage-remote.cgi';

    public function getTransactionReference()
    {
        return $this->getParameter('pasRef');
    }

    public function setTransactionReference($value)
    {
        return $this->setParameter('pasRef', $value);
    }

    public function getAuthCode()
    {
        return $this->getParameter('authCode');
    }

    public function setAuthCode($value)
    {
        return $this->setParameter('authCode', $value);
    }

    public function getRefundPassword()
    {
        return $this->getParameter('refundPassword');
    }

    public function setRefundPassword($value)
    {
        return $this->setParameter('refundPassword', $value);
    }

    /**
     * Get the XML registration string to be sent to the gateway
     *
     * @return string
     */
    public function getData()
    {
        $this->validate('amount', 'currency', 'transactionId');

        // Create the hash
        $timestamp = strftime("%Y%m%d%H%M%S");
        $merchantId = $this->getMerchantId();
        $orderId = $this->getTransactionId();
        $amount = $this->getAmountInteger();
        $currency = $this->getCurrency();
        // No card number for rebate requests but still needs to be in hash
        $cardNumber = '';
        $secret = $this->getSecret();
        $tmp = "$timestamp.$merchantId.$orderId.$amount.$currency.$cardNumber";
        $sha1hash = sha1($tmp);
        $tmp2 = "$sha1hash.$secret";
        $sha1hash = sha1($tmp2);

        $domTree = new \DOMDocument('1.0', 'UTF-8');

        // root element
        $root = $domTree->createElement('request');
        $root->setAttribute('type', 'rebate');
        $root->setAttribute('timestamp', $timestamp);
        $root = $domTree->appendChild($root);

        // merchant ID
        $merchantEl = $domTree->createElement('merchantid', $merchantId);
        $root->appendChild($merchantEl);

        // account
        $accountEl = $domTree->createElement('account', $this->getAccount());
        $root->appendChild($accountEl);

        // original order ID
        $orderIdEl = $domTree->createElement('orderid', $orderId);
        $root->appendChild($orderIdEl);

        // pasref returned for original transaction
        $pasRefEl = $domTree->createElement('pasref', $this->getTransactionReference());
        $root->appendChild($pasRefEl);

        // authcode returned for original transaction
        $authCodeEl = $domTree->createElement('authcode', $this->getAuthCode());
        $root->appendChild($authCodeEl);

        // amount
        $amountEl = $domTree->createElement('amount', $amount);
        $amountEl->setAttribute('currency', $this->getCurrency());
        $root->appendChild($amountEl);

        // refund hash
        $refundHash = sha1($this->getRefundPassword());
        $refundHashEl = $domTree->createElement('refundhash', $refundHash);
        $root->appendChild($refundHashEl);

        $sha1El = $domTree->createElement('sha1hash', $sha1hash);
        $root->appendChild($sha1El);

        $xmlString = $domTree->saveXML($root);

        return $xmlString;
    }

    protected function createResponse($data)
    {
        return $this->response = new RefundResponse($this, $data);
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
