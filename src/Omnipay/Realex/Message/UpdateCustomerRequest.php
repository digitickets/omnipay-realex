<?php
/**
 * @author Philip Wright- Christie <pwrightchristie.sfp@gmail.com>
 * Date: 04/08/15
 */

namespace Omnipay\Realex\Message;

class UpdateCustomerRequest extends RemoteAbstractRequest
{
    protected $endpoint = 'https://epage.payandshop.com/epage-remote-plugins.cgi';

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
        $timestamp  = strftime("%Y%m%d%H%M%S");
        $merchantId = $this->getMerchantId();
        $orderId    = $this->getTransactionId();
        $secret     = $this->getSecret();
        $payerRef   = $this->getCustomerRef();

        //$tmp = "$timestamp.$merchantId.$orderId.$amount.$currency.$payerRef";
        $tmp      = "$timestamp.$merchantId.$orderId...$payerRef";
        $sha1hash = sha1($tmp);
        $tmp2     = "$sha1hash.$secret";
        $sha1hash = sha1($tmp2);

        $domTree = new \DOMDocument('1.0', 'UTF-8');

        // root element
        $root = $domTree->createElement('request');
        $root->setAttribute('type', 'payer-edit');
        $root->setAttribute('timestamp', $timestamp);
        $root = $domTree->appendChild($root);

        // merchant ID
        $merchantEl = $domTree->createElement('merchantid', $merchantId);
        $root->appendChild($merchantEl);

        // order ID
        $merchantEl = $domTree->createElement('orderid', $orderId);
        $root->appendChild($merchantEl);

        $payerEl = $domTree->createElement('payer');
        $payerEl->setAttribute('type', 'Business');
        $payerEl->setAttribute('ref', $payerRef);

        /**
         * @var \Omnipay\Common\CreditCard $card
         */
        $card = $this->getCard();

        $titleEl = $domTree->createElement('title', $card->getBillingTitle());
        $payerEl->appendChild($titleEl);

        $firstnameEl = $domTree->createElement('firstname', $card->getBillingFirstName());
        $payerEl->appendChild($firstnameEl);

        $lastnameEl = $domTree->createElement('surname', $card->getBillingLastName());
        $payerEl->appendChild($lastnameEl);

        $companyEl = $domTree->createElement('company', $card->getBillingCompany());
        $payerEl->appendChild($companyEl);

        $addressEl = $domTree->createElement('address');

        $line1El = $domTree->createElement('line1', $card->getBillingAddress1());
        $addressEl->appendChild($line1El);

        $line2El = $domTree->createElement('line2', $card->getBillingAddress2());
        $addressEl->appendChild($line2El);

        $cityEl = $domTree->createElement('city', $card->getBillingCity());
        $addressEl->appendChild($cityEl);

        $countyEl = $domTree->createElement('county', $card->getBillingState());
        $addressEl->appendChild($countyEl);

        $postcodeEl = $domTree->createElement('postcode', $card->getBillingPostcode());
        $addressEl->appendChild($postcodeEl);

        $countryEl = $domTree->createElement('country', $card->getBillingCountry());
        $addressEl->appendChild($countryEl);

        $payerEl->appendChild($addressEl);

        $phonenumbersEl = $domTree->createElement('phonenumbers');
        $homeEl         = $domTree->createElement('home', $card->getBillingPhone());
        $phonenumbersEl->appendChild($homeEl);
        $payerEl->appendChild($phonenumbersEl);

        $emailEl = $domTree->createElement('email', $card->getEmail());
        $payerEl->appendChild($emailEl);

        $root->appendChild($payerEl);

        $sha1El = $domTree->createElement('sha1hash', $sha1hash);
        $root->appendChild($sha1El);

        $xmlString = $domTree->saveXML($root);

        return $xmlString;
    }

    protected function createResponse($data)
    {
        return $this->response = new UpdateCustomerResponse($this, $data);
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
