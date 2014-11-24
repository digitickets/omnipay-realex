<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Realex 3D Secure enrolment request
 */
class EnrolmentRequest extends RemoteAbstractRequest
{
    protected $endpoint = 'https://epage.payandshop.com/epage-3dsecure.cgi';

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
        $cardNumber = $this->getCard()->getNumber();
        $secret = $this->getSecret();
        $tmp = "$timestamp.$merchantId.$orderId.$amount.$currency.$cardNumber";
        $sha1hash = sha1($tmp);
        $tmp2 = "$sha1hash.$secret";
        $sha1hash = sha1($tmp2);

        $domTree = new \DOMDocument('1.0', 'UTF-8');

        // root element
        $root = $domTree->createElement('request');
        $root->setAttribute('type', '3ds-verifyenrolled');
        $root->setAttribute('timestamp', $timestamp);
        $root = $domTree->appendChild($root);

        // merchant ID
        $merchantEl = $domTree->createElement('merchantid', $merchantId);
        $root->appendChild($merchantEl);

        // account
        $merchantEl = $domTree->createElement('account', $this->getAccount());
        $root->appendChild($merchantEl);

        // order ID
        $merchantEl = $domTree->createElement('orderid', $orderId);
        $root->appendChild($merchantEl);

        // amount
        $amountEl = $domTree->createElement('amount', $amount);
        $amountEl->setAttribute('currency', $this->getCurrency());
        $root->appendChild($amountEl);

        /**
         * @var \Omnipay\Common\CreditCard $card
         */
        $card = $this->getCard();

        // Card details
        $cardEl = $domTree->createElement('card');

        $cardNumberEl = $domTree->createElement('number', $card->getNumber());
        $cardEl->appendChild($cardNumberEl);

        $expiryEl = $domTree->createElement('expdate', $card->getExpiryDate("my")); // mmyy
        $cardEl->appendChild($expiryEl);

        $cardTypeEl = $domTree->createElement('type', $this->getCardBrand());
        $cardEl->appendChild($cardTypeEl);

        $cardNameEl = $domTree->createElement('chname', $card->getBillingName());
        $cardEl->appendChild($cardNameEl);

        $cvnEl = $domTree->createElement('cvn');

        $cvnNumberEl = $domTree->createElement('number', $card->getCvv());
        $cvnEl->appendChild($cvnNumberEl);

        $presIndEl = $domTree->createElement('presind', 1);
        $cvnEl->appendChild($presIndEl);

        $cardEl->appendChild($cvnEl);

        $issueEl = $domTree->createElement('issueno', $card->getIssueNumber());
        $cardEl->appendChild($issueEl);

        $root->appendChild($cardEl);

        $sha1El = $domTree->createElement('sha1hash', $sha1hash);
        $root->appendChild($sha1El);

        $xmlString = $domTree->saveXML($root);

        return $xmlString;
    }

    protected function createResponse($data)
    {
        /**
         * We need to inspect this response to see if the customer is actually
         * enrolled in the 3D Secure program. If they're not, we can go ahead
         * and do a normal auth instead.
         */
        $response = $this->response = new EnrolmentResponse($this, $data);

        if (!$response->isEnrolled()) {
            $request = new AuthRequest($this->httpClient, $this->httpRequest);
            $request->initialize($this->getParameters());

            $response = $request->send();
        }

        return $response;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
