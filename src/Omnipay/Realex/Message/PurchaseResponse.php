<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Realex Purchase Response
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function isSuccessful()
    {
        $xml = $this->getXml();
        $success = ($xml->result == '00');

        return $success;
    }

    public function getMessage()
    {
        $xml = $this->getXml();
        $message = (string)$xml->message;

        return $message;
    }

    public function getTransactionReference()
    {
        $xml = $this->getXml();
        $transactionReference = !empty($xml->pasref) ? $xml->pasref : null;

        return $transactionReference;
    }

    public function isRedirect()
    {
        return false;
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return null;
    }

    public function getXml()
    {
        $rawData = $this->data;

        $rawData = str_replace('  ', ' ', $rawData);
        $rawData = str_replace("\n", '', $rawData);
        $rawData = str_replace("\r", '', $rawData);

        $xml = new \SimpleXMLElement($rawData);

        return $xml;
    }

    /**
     * Gets the redirect target url.
     */
    public function getRedirectUrl()
    {
        return '';
    }
}
