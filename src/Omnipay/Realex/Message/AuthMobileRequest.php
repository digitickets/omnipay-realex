<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Realex Auth-Mobile Request
 *
 * If the `mobileType` parameter is set to `pay-with-google`,
 * the `amount` and `currency` parameters must be included in
 * the request as they are not a part of the `token` value
 * as they are with the `apple-pay` mobile type.
 *
 * Example:
 *
 * ```
 * $response = $gateway->purchase([
 *     'transactionId' => 'abc123',
 *     'mobileType' => 'apple-pay',
 *     // payload from mobile transaction
 *     // example below has been truncated and formatted
 *     'token' => '{
 *         "version":"EC_v1",
 *         "data":"Ft+dvmdfgnsdfnbg+zerKtkh/RW[...]d/QAAAAAAAA==",
 *         "header": {
 *             "ephemeralPublicKey":"MFkwEwYHKoZIzj0CA[...]Z+telY/G1+YSoaCbR57bdGA==",
 *             "transactionId":"fd88874954acdb29976g[....]G3fd4ebc22a864398684198644c3",
 *             "publicKeyHash":"h7njghUJVz2gmpTSkHqETOWsskhsdfjj4mgf3sPTS2cBxgrk="
 *         }
 *     }',
 * ])->send();
 * ```
 */
class AuthMobileRequest extends RemoteAbstractRequest
{
    protected $endpoint = 'https://epage.payandshop.com/epage-remote.cgi';

    public function getMobileType()
    {
        return $this->getParameter('mobileType');
    }

    public function setMobileType($value)
    {
        return $this->setParameter('mobileType', $value);
    }

    public function getToken()
    {
        return $this->getParameter('token');
    }

    public function setToken($value)
    {
        return $this->setParameter('token', $value);
    }

    public function getAmount()
    {
        return $this->getParameter('amount');
    }

    public function setAmount($value)
    {
        return $this->setParameter('amount', $value);
    }

    public function getCurrency()
    {
        return $this->getParameter('currency');
    }

    public function setCurrency($value)
    {
        return $this->setParameter('currency', $value);
    }

    /**
     * Get the XML registration string to be sent to the gateway
     *
     * @return string
     */
    public function getData()
    {
        $this->validate('mobileType', 'token', 'transactionId');

        if ($this->getMobileType() === 'pay-with-google') {
            $this->validate('amount', 'currency');
        }

        // Create the hash
        $timestamp = strftime("%Y%m%d%H%M%S");
        $merchantId = $this->getMerchantId();
        $orderId = $this->getTransactionId();
        $token = $this->getToken();
        $secret = $this->getSecret();
        $amount = $this->getAmount();
        $currency = $this->getCurrency();
        $tmp = "$timestamp.$merchantId.$orderId.$amount.$currency.$token";
        $sha1hash = sha1($tmp);
        $tmp2 = "$sha1hash.$secret";
        $sha1hash = sha1($tmp2);

        $domTree = new \DOMDocument('1.0', 'UTF-8');

        // root element
        $root = $domTree->createElement('request');
        $root->setAttribute('type', 'auth-mobile');
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

        // Amount isn't required for Apple Pay transaction since it's bundled in the
        // encrypted payload (token), but for Google Pay, the amount is required to
        // be sent in the request as a separate XML field.
        if ($this->getAmount()) {
            $amountEl = $domTree->createElement('amount', $this->getAmount());
            $amountEl->setAttribute('currency', $this->getCurrency());
            $root->appendChild($amountEl);
        }

        $settleEl = $domTree->createElement('autosettle');
        $settleEl->setAttribute('flag', 1);
        $root->appendChild($settleEl);

        $mobileEl = $domTree->createElement('mobile', $this->getMobileType());
        $root->appendChild($mobileEl);

        $tokenEl = $domTree->createElement('token', $token);
        $root->appendChild($tokenEl);

        // TODO: add comments

        $sha1El = $domTree->createElement('sha1hash', $sha1hash);
        $root->appendChild($sha1El);

        $xmlString = $domTree->saveXML($root);

        return $xmlString;
    }

    protected function createResponse($data)
    {
        return $this->response = new AuthMobileResponse($this, $data);
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
