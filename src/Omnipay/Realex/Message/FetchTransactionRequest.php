<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Realex Query Request
 */
class FetchTransactionRequest extends RemoteAbstractRequest
{

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
        $merchantEl = $domTree->createElement('merchantid');
        $merchantEl->appendChild($domTree->createTextNode($merchantId));
        $root->appendChild($merchantEl);

        // account
        $accountEl = $domTree->createElement('account');
        $accountEl->appendChild($domTree->createTextNode($this->getAccount()));
        $root->appendChild($accountEl);

        // original order ID
        $orderIdEl = $domTree->createElement('orderid');
        $orderIdEl->appendChild($domTree->createTextNode($orderId));
        $root->appendChild($orderIdEl);

        $sha1El = $domTree->createElement('sha1hash');
        $sha1El->appendChild($domTree->createTextNode($sha1hash));
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
        return $this->getParameter('3dSecureEndpoint');
    }

    public function setAuthEndpoint($value)
    {
        return $this->setParameter('3dSecureEndpoint', $value);
    }
}
