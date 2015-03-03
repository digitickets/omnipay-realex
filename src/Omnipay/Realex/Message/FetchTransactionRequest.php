<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Realex Query Request
 */
class FetchTransactionRequest extends RemoteAbstractRequest
{
    protected $endpoint = 'https://epage.payandshop.com/epage-remote.cgi';

    /**
     * Get the XML registration string to be sent to the gateway
     *
     * @return string
     */
    public function getData()
    {
        $this->validate('transactionId');

        // Create the hash
        $timestamp = strftime("%Y%m%d%H%M%S");
        $merchantId = $this->getMerchantId();
        $orderId = $this->getTransactionId();
        // No amount, currency or card number for query requests but still needs to be in hash
        $amount = '';
        $currency = '';
        $cardNumber = '';
        $secret = $this->getSecret();
        $tmp = "$timestamp.$merchantId.$orderId.$amount.$currency.$cardNumber";
        $sha1hash = sha1($tmp);
        $tmp2 = "$sha1hash.$secret";
        $sha1hash = sha1($tmp2);

        $domTree = new \DOMDocument('1.0', 'UTF-8');

        // root element
        $root = $domTree->createElement('request');
        $root->setAttribute('type', 'query');
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

        $sha1El = $domTree->createElement('sha1hash', $sha1hash);
        $root->appendChild($sha1El);

        $xmlString = $domTree->saveXML($root);

        return $xmlString;
    }

    protected function createResponse($data)
    {
        return $this->response = new FetchTransactionResponse($this, $data);
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
