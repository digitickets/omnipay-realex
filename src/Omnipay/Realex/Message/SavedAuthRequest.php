<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Realex Auth Request
 */
class SavedAuthRequest extends RemoteAbstractRequest
{
    public function getCavv()
    {
        return $this->getParameter('cavv');
    }

    public function setCavv($value)
    {
        return $this->setParameter('cavv', $value);
    }

    public function getEci()
    {
        return $this->getParameter('eci');
    }

    public function setEci($value)
    {
        return $this->setParameter('eci', $value);
    }

    public function getXid()
    {
        return $this->getParameter('xid');
    }

    public function setXid($value)
    {
        return $this->setParameter('xid', $value);
    }

    public function getCustomerRef()
    {
        return $this->getParameter('customerRef');
    }

    public function setCustomerRef($customerRef)
    {
        $this->setParameter('customerRef', $customerRef);
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
        $secret = $this->getSecret();
        $tmp = "$timestamp.$merchantId.$orderId.$amount.$currency.{$this->getCustomerRef()}";
        $sha1hash = sha1($tmp);
        $tmp2 = "$sha1hash.$secret";
        $sha1hash = sha1($tmp2);

        /**
         * @var \Omnipay\Common\CreditCard $card
         */
        $card = $this->getCard();

        $domTree = new \DOMDocument('1.0', 'UTF-8');

        // root element
        $root = $domTree->createElement('request');
        $root->setAttribute('type', 'receipt-in');
        $root->setAttribute('timestamp', $timestamp);
        $root = $domTree->appendChild($root);

        // merchant ID
        $merchantEl = $domTree->createElement('merchantid');
        $merchantEl->appendChild($domTree->createTextNode($merchantId));
        $root->appendChild($merchantEl);

        // account
        $merchantEl = $domTree->createElement('account');
        $merchantEl->appendChild($domTree->createTextNode($this->getAccount()));
        $root->appendChild($merchantEl);

        // order ID
        $merchantEl = $domTree->createElement('orderid');
        $merchantEl->appendChild($domTree->createTextNode($orderId));
        $root->appendChild($merchantEl);

        // amount
        $amountEl = $domTree->createElement('amount');
        $amountEl->setAttribute('currency', $this->getCurrency());
        $amountEl->appendChild($domTree->createTextNode($amount));
        $root->appendChild($amountEl);

        $paymentDataEl = $domTree->createElement('paymentdata');
        $cvnEl = $domTree->createElement('cvn');
        $numberEl = $domTree->createElement('number');
        $numberEl->appendChild($domTree->createTextNode($card->getCvv()));

        $cvnEl->appendChild($numberEl);
        $paymentDataEl->appendChild($cvnEl);
        $root->appendChild($paymentDataEl);

        $payerRefEl = $domTree->createElement('payerref');
        $payerRefEl->appendChild($domTree->createTextNode($this->getCustomerRef()));
        $root->appendChild($payerRefEl);

        $paymentMethodEl = $domTree->createElement('paymentmethod');
        $paymentMethodEl->appendChild($domTree->createTextNode($this->getCardReference()));
        $root->appendChild($paymentMethodEl);

        $settleEl = $domTree->createElement('autosettle');
        $settleEl->setAttribute('flag', 1);
        $root->appendChild($settleEl);

        // 3D Secure section
        $mpiEl = $domTree->createElement('mpi');
        $cavvEl = $domTree->createElement('cavv');
        $cavvEl->appendChild($domTree->createTextNode($this->getCavv()));
        $xidEl = $domTree->createElement('xid');
        $xidEl->appendChild($domTree->createTextNode($this->getXid()));
        $eciEl = $domTree->createElement('eci');
        $eciEl->appendChild($domTree->createTextNode($this->getEci()));
        $mpiEl->appendChild($cavvEl);
        $mpiEl->appendChild($xidEl);
        $mpiEl->appendChild($eciEl);
        $root->appendChild($mpiEl);

        $sha1El = $domTree->createElement('sha1hash');
        $sha1El->appendChild($domTree->createTextNode($sha1hash));
        $root->appendChild($sha1El);

        $tssEl = $domTree->createElement('tssinfo');
        $addressEl = $domTree->createElement('address');
        $addressEl->setAttribute('type', 'billing');
        $countryEl = $domTree->createElement('country');
        $countryEl->appendChild($domTree->createTextNode($card->getBillingCountry()));
        $addressEl->appendChild($countryEl);
        $tssEl->appendChild($addressEl);
        $root->appendChild($tssEl);

        $xmlString = $domTree->saveXML($root);

        return $xmlString;
    }

    protected function createResponse($data)
    {
        return $this->response = new AuthResponse($this, $data);
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
