<?php
/**
 * @author Philip Wright- Christie <pwrightchristie.sfp@gmail.com>
 * Date: 04/08/15
 */

namespace Omnipay\Realex\Message;


class CreateCardRequest extends RemoteAbstractRequest
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

        /**
         * @var \Omnipay\Common\CreditCard $card
         */
        $card = $this->getCard();

        //$tmp = "$timestamp.$merchantId.$orderId.$amount.$currency.$payerRef";
        $tmp      = "$timestamp.$merchantId.$orderId...$payerRef.{$card->getBillingName()}.{$card->getNumber()}";
        $sha1hash = sha1($tmp);
        $tmp2     = "$sha1hash.$secret";
        $sha1hash = sha1($tmp2);

        $domTree = new \DOMDocument('1.0', 'UTF-8');

        // root element
        $root = $domTree->createElement('request');
        $root->setAttribute('type', 'card-new');
        $root->setAttribute('timestamp', $timestamp);
        $root = $domTree->appendChild($root);

        // merchant ID
        $merchantEl = $domTree->createElement('merchantid', $merchantId);
        $root->appendChild($merchantEl);

        // order ID
        $merchantEl = $domTree->createElement('orderid', $orderId);
        $root->appendChild($merchantEl);

        $cardEl = $domTree->createElement('card');

        $cardRefEl = $domTree->createElement('ref', $this->getCardReference());
        $cardEl->appendChild($cardRefEl);

        $payerRefEl = $domTree->createElement('payerref', $this->getCustomerRef());
        $cardEl->appendChild($payerRefEl);

        $numberEl = $domTree->createElement('number', $card->getNumber());
        $cardEl->appendChild($numberEl);

        $expDateEl = $domTree->createElement('expdate', $card->getExpiryDate("my"));
        $cardEl->appendChild($expDateEl);

        $chNameEl = $domTree->createElement('chname', $card->getBillingName());
        $cardEl->appendChild($chNameEl);

        $typeEl = $domTree->createElement('type', $this->getCardBrand());
        $cardEl->appendChild($typeEl);

        $issueNoEl = $domTree->createElement('issueno', $card->getIssueNumber());
        $cardEl->appendChild($issueNoEl);

        $root->appendChild($cardEl);

        $sha1El = $domTree->createElement('sha1hash', $sha1hash);
        $root->appendChild($sha1El);

        $xmlString = $domTree->saveXML($root);

        return $xmlString;
    }

    protected function createResponse($data)
    {
        return $this->response = new CreateCardResponse($this, $data);
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
