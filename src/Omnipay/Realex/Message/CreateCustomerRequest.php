<?php

/**
 * @author Philip Wright- Christie <pwrightchristie.sfp@gmail.com>
 * Date: 04/08/15
 */

namespace Omnipay\Realex\Message;

class CreateCustomerRequest extends RemoteAbstractRequest
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
        $orderId = $this->getTransactionId();
        $secret = $this->getSecret();
        $payerRef = $this->getCustomerRef();

        //$tmp = "$timestamp.$merchantId.$orderId.$amount.$currency.$payerRef";
        $tmp = "$timestamp.$merchantId.$orderId...$payerRef";
        $sha1hash = sha1($tmp);
        $tmp2 = "$sha1hash.$secret";
        $sha1hash = sha1($tmp2);

        $domTree = new \DOMDocument('1.0', 'UTF-8');

        // root element
        $root = $domTree->createElement('request');
        $root->setAttribute('type', 'payer-new');
        $root->setAttribute('timestamp', $timestamp);
        $root = $domTree->appendChild($root);

        // merchant ID
        $merchantEl = $domTree->createElement('merchantid');
        $merchantEl->appendChild($domTree->createTextNode($merchantId));
        $root->appendChild($merchantEl);

        // order ID
        $merchantEl = $domTree->createElement('orderid');
        $merchantEl->appendChild($domTree->createTextNode($orderId));
        $root->appendChild($merchantEl);

        $payerEl = $domTree->createElement('payer');
        $payerEl->setAttribute('type', 'Business');
        $payerEl->setAttribute('ref', $payerRef);

        /**
         * @var \Omnipay\Common\CreditCard $card
         */
        $card = $this->getCard();

        $titleEl = $domTree->createElement('title');
        $titleEl->appendChild($domTree->createTextNode($card->getBillingTitle()));
        $payerEl->appendChild($titleEl);

        $firstnameEl = $domTree->createElement('firstname');
        $firstnameEl->appendChild($domTree->createTextNode($card->getBillingFirstName()));
        $payerEl->appendChild($firstnameEl);

        $lastnameEl = $domTree->createElement('surname');
        $lastnameEl->appendChild($domTree->createTextNode($card->getBillingLastName()));
        $payerEl->appendChild($lastnameEl);

        $companyEl = $domTree->createElement('company');
        $companyEl->appendChild($domTree->createTextNode($card->getBillingCompany()));
        $payerEl->appendChild($companyEl);

        $addressEl = $domTree->createElement('address');

        $line1El = $domTree->createElement('line1');
        $line1El->appendChild($domTree->createTextNode($card->getBillingAddress1()));
        $addressEl->appendChild($line1El);

        $line2El = $domTree->createElement('line2');
        $line2El->appendChild($domTree->createTextNode($card->getBillingAddress2()));
        $addressEl->appendChild($line2El);

        $cityEl = $domTree->createElement('city');
        $cityEl->appendChild($domTree->createTextNode($card->getBillingCity()));
        $addressEl->appendChild($cityEl);

        $countyEl = $domTree->createElement('county');
        $countyEl->appendChild($domTree->createTextNode($card->getBillingState()));
        $addressEl->appendChild($countyEl);

        $postcodeEl = $domTree->createElement('postcode');
        $postcodeEl->appendChild($domTree->createTextNode($card->getBillingPostcode()));
        $addressEl->appendChild($postcodeEl);

        $countryEl = $domTree->createElement('country');
        $countryEl->appendChild($domTree->createTextNode($card->getBillingCountry()));
        $addressEl->appendChild($countryEl);

        $payerEl->appendChild($addressEl);

        $phonenumbersEl = $domTree->createElement('phonenumbers');
        $homeEl = $domTree->createElement('home');
        $homeEl->appendChild($domTree->createTextNode($card->getBillingPhone()));
        $phonenumbersEl->appendChild($homeEl);
        $payerEl->appendChild($phonenumbersEl);

        $emailEl = $domTree->createElement('email');
        $emailEl->appendChild($domTree->createTextNode($card->getEmail()));
        $payerEl->appendChild($emailEl);

        $root->appendChild($payerEl);

        $sha1El = $domTree->createElement('sha1hash');
        $sha1El->appendChild($domTree->createTextNode($sha1hash));
        $root->appendChild($sha1El);

        $xmlString = $domTree->saveXML($root);

        return $xmlString;
    }

    protected function createResponse($data)
    {
        return $this->response = new CreateCustomerResponse($this, $data);
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
