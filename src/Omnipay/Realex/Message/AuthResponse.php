<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Realex Auth Response
 */
class AuthResponse extends RemoteAbstractResponse implements RedirectResponseInterface
{
    public function isSuccessful()
    {
        return ($this->xml->result == '00');
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
