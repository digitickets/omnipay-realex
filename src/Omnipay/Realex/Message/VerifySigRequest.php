<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Realex Complete Auth Request
 */
class VerifySigRequest extends RemoteAbstractRequest
{

    /**
     * Decode our previously-encoded Merchant Data
     *
     * @param string $data
     * @return array
     */
    protected function decodeMerchantData($data)
    {
        $json = base64_decode($data);
        $cardData = (array) json_decode($json);

        return $cardData;
    }

    /**
     * Get the XML registration string to be sent to the gateway
     *
     * @return string
     */
    public function getData()
    {
        /**
         * Data will be sent from the 3D Secure provider in two fields: MD and ParRes.
         * MD contains our original data (encoded by us) and PaRes will be sent to the gateway.
         */
        $returnedData = $this->decodeMerchantData($this->httpRequest->request->get('MD', ''));

        $this->setTransactionId($returnedData['transactionId']);
        $this->setAmount($returnedData['amount']);
        $this->setCurrency($returnedData['currency']);
        $this->setCard(new CreditCard($returnedData));

        $paRes = $this->httpRequest->request->get('PaRes', '');

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
        $root->setAttribute('type', '3ds-verifysig');
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
        $amountEl->appendChild($domTree->createTextNode($amount));
        $amountEl->setAttribute('currency', $this->getCurrency());
        $root->appendChild($amountEl);

        /**
         * @var \Omnipay\Common\CreditCard $card
         */
        $card = $this->getCard();

        // Card details
        $cardEl = $domTree->createElement('card');

        $cardNumberEl = $domTree->createElement('number');
        $cardNumberEl->appendChild($domTree->createTextNode($card->getNumber()));
        $cardEl->appendChild($cardNumberEl);

        $expiryEl = $domTree->createElement('expdate'); // mmyy
        $expiryEl->appendChild($domTree->createTextNode($card->getExpiryDate("my")));
        $cardEl->appendChild($expiryEl);

        $cardTypeEl = $domTree->createElement('type');
        $cardTypeEl->appendChild($domTree->createTextNode($this->getCardBrand()));
        $cardEl->appendChild($cardTypeEl);

        $cardNameEl = $domTree->createElement('chname');
        $cardNameEl->appendChild($domTree->createTextNode($card->getBillingName()));
        $cardEl->appendChild($cardNameEl);

        $root->appendChild($cardEl);

        $paResEl = $domTree->createElement('pares');
        $paResEl->appendChild($domTree->createTextNode($paRes));
        $root->appendChild($paResEl);

        $sha1El = $domTree->createElement('sha1hash');
        $sha1El->appendChild($domTree->createTextNode($sha1hash));
        $root->appendChild($sha1El);

        $xmlString = $domTree->saveXML($root);

        return $xmlString;
    }

    protected function createResponse($data)
    {
        return $this->response = new VerifySigResponse($this, $data);
    }

    public function getEndpoint()
    {
        return $this->getParameter('authEndpoint');
    }

    public function setAuthEndpoint($value)
    {
        return $this->setParameter('authEndpoint', $value);
    }

    /**
     * @param mixed $parameters
     *
     * @return AuthResponse|VerifySigResponse
     */
    public function sendData($parameters)
    {
        /**
         * @var VerifySigResponse $response
         */
        $response = parent::sendData($parameters);

        if ($response->isSuccessful()) {
            // a few additional parameters that need to be passed for 3D-Secure transactions
            $parameters = $this->getParameters();
            $parameters['cavv'] = $response->getParam('cavv');
            $parameters['eci'] = $response->getParam('eci');
            $parameters['xid'] = $response->getParam('xid');

            /**
             * Now finally, do our authorisation
             *
             * @var AuthRequest $request
             * @var AuthResponse $response
             */
            $request = new AuthRequest($this->httpClient, $this->httpRequest);
            $request->initialize($parameters);

            $response = $request->send();
        }

        return $response;
    }
}
