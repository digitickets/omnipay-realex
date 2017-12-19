<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Realex Auth-Mobile Request
 *
 * Example:
 *
 * ```
 * $response = $gateway->purchase([
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

    /**
     * Get the XML registration string to be sent to the gateway
     *
     * @return string
     */
    public function getData()
    {
        $this->validate('mobileType', 'token', 'transactionId');

        // Create the hash
        $timestamp = strftime("%Y%m%d%H%M%S");
        $merchantId = $this->getMerchantId();
        $orderId = $this->getTransactionId();
        $token = $this->getToken();
        $secret = $this->getSecret();
        $tmp = "$timestamp.$merchantId.$orderId...$token";
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
