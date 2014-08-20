<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Realex Verify 3D Secure signature Response
 */
class VerifySigResponse extends RemoteAbstractResponse implements RedirectResponseInterface
{
    public function isSuccessful()
    {
        return ($this->xml->result == '00');
    }

    /**
     * Find the value of a named XML element in the response
     *
     * @param string $key
     * @return string
     */
    public function getParam($key)
    {
        $matches = $this->xml->xpath('//' . $key);
        if (!empty($matches)) {
            $value = (string)$matches[0];
        } else {
            $value = '';
        }

        return $value;
    }

    public function getMessage()
    {
        return (string)$this->xml->message;
    }

    public function getTransactionReference()
    {
        return ($this->xml->pasref) ? $this->xml->pasref : null;
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
