<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Realex Void Request
 */
class VoidRequest extends RemoteAbstractRequest
{
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
        // No amount, currency or card number for rebate requests but still needs to be in hash
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
        $root->setAttribute('type', 'void');
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

        // pasref returned for original transaction
        $pasRefEl = $domTree->createElement('pasref');
        $pasRefEl->appendChild($domTree->createTextNode($this->getTransactionReference()));
        $root->appendChild($pasRefEl);

        // authcode returned for original transaction
        $authCodeEl = $domTree->createElement('authcode');
        $authCodeEl->appendChild($domTree->createTextNode($this->getAuthCode()));
        $root->appendChild($authCodeEl);

        $sha1El = $domTree->createElement('sha1hash');
        $sha1El->appendChild($domTree->createTextNode($sha1hash));
        $root->appendChild($sha1El);

        $xmlString = $domTree->saveXML($root);

        return $xmlString;
    }

    protected function createResponse($data)
    {
        return $this->response = new VoidResponse($this, $data);
    }

    public function getEndpoint()
    {
        return $this->getParameter('SecureDataVaultEndpoint');
    }

    public function setAuthEndpoint($value)
    {
        return $this->setParameter('SecureDataVaultEndpoint', $value);
    }
}
