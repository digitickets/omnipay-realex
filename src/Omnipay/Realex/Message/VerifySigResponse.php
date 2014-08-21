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
        /**
         * The 3D secure result is only counted as successful if is:
         *  - legitimate - (result = 00)
         *  - card holder correctly entered their password, or the 3DS systems are unavailable at the moment
         */
        return ($this->xml->result == '00' && $this->xml->threedsecure->status != 'N');
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
        /**
         * For some reason, the default message from the gateway
         * says "Authentication Successful", even if the customer
         * was shown to have used an incorrect password. For sane
         * front-end reporting, let's override this.
         */
        if ($this->xml->threedsecure->status == 'N') {
            $message = '3D Secure Authentication Unsuccessful';
        } else {
            $message = (string)$this->xml->message;
        }

        return $message;
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
