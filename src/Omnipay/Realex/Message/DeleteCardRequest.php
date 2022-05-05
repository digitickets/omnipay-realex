<?php

/**
 * @author Philip Wright- Christie <pwrightchristie.sfp@gmail.com>
 * Date: 04/08/15
 */

namespace Omnipay\Realex\Message;

class DeleteCardRequest extends RemoteAbstractRequest
{
    public function getCustomerRef()
    {
        return $this->getParameter('customerRef');
    }

    public function setCustomerRef($customerRef)
    {
        $this->setParameter('customerRef', $customerRef);
    }

    public function getData()
    {
        // Create the hash
        $timestamp = strftime("%Y%m%d%H%M%S");
        $merchantId = $this->getMerchantId();
        $secret = $this->getSecret();
        $payerRef = $this->getCustomerRef();

        $tmp = "$timestamp.$merchantId.$payerRef.{$this->getCardReference()}";
        $sha1hash = sha1($tmp);
        $tmp2 = "$sha1hash.$secret";
        $sha1hash = sha1($tmp2);

        $domTree = new \DOMDocument('1.0', 'UTF-8');

        // root element
        $root = $domTree->createElement('request');
        $root->setAttribute('type', 'card-cancel-card');
        $root->setAttribute('timestamp', $timestamp);
        $root = $domTree->appendChild($root);

        // merchant ID
        $merchantEl = $domTree->createElement('merchantid');
        $merchantEl->appendChild($domTree->createTextNode($merchantId));
        $root->appendChild($merchantEl);

        $cardEl = $domTree->createElement('card');

        $cardRefEl = $domTree->createElement('ref');
        $cardRefEl->appendChild($domTree->createTextNode($this->getCardReference()));
        $cardEl->appendChild($cardRefEl);

        $payerRefEl = $domTree->createElement('payerref');
        $payerRefEl->appendChild($domTree->createTextNode($this->getCustomerRef()));
        $cardEl->appendChild($payerRefEl);

        $root->appendChild($cardEl);

        $sha1El = $domTree->createElement('sha1hash');
        $sha1El->appendChild($domTree->createTextNode($sha1hash));
        $root->appendChild($sha1El);

        $xmlString = $domTree->saveXML($root);

        return $xmlString;
    }

    protected function createResponse($data)
    {
        return $this->response = new DeleteCardResponse($this, $data);
    }

    public function getEndpoint()
    {
        return $this->getParameter('AuthEndpoint');
    }

    public function setAuthEndpoint($value)
    {
        return $this->setParameter('AuthEndpoint', $value);
    }
}
