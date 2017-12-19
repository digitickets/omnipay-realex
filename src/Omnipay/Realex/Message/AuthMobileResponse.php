<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Realex Auth-Mobile Response
 */
class AuthMobileResponse extends RemoteAbstractResponse implements RedirectResponseInterface
{
    public function isSuccessful()
    {
        return ($this->xml->result == '00');
    }

    public function isDecline()
    {
        return (substr($this->xml->result, 0, 1) == '1');
    }

    public function isBankSystemError()
    {
        return (substr($this->xml->result, 0, 1) == '2');
    }

    public function isRealexSystemError()
    {
        return (substr($this->xml->result, 0, 1) == '3');
    }

    public function getMessage()
    {
        return (string)$this->xml->message;
    }

    public function getTransactionId()
    {
        return ($this->xml->orderid) ? (string)$this->xml->orderid : null;
    }

    public function getTransactionReference()
    {
        return ($this->xml->pasref) ? (string)$this->xml->pasref : null;
    }

    public function getAuthCode()
    {
        return ($this->xml->authcode) ? (string)$this->xml->authcode : null;
    }

    public function getBatchId()
    {
        return ($this->xml->batchid) ? (string)$this->xml->batchid : null;
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

    /**
     * Gets the redirect target url.
     */
    public function getRedirectUrl()
    {
        return '';
    }
}
