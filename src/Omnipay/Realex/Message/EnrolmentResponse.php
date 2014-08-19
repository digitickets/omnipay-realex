<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Realex Enrolment Response
 */
class EnrolmentResponse extends RemoteAbstractResponse implements RedirectResponseInterface
{
    public function isSuccessful()
    {
        $success = ($this->xml->result == '00');

        return $success;
    }

    public function getMessage()
    {
        $message = (string)$this->xml->message;

        return $message;
    }

    public function getTransactionReference()
    {
        $transactionReference = !empty($this->xml->pasref) ? $this->xml->pasref : null;

        return $transactionReference;
    }

    public function isRedirect()
    {
        if ($this->xml->result == '00' && $this->xml->enrolled == 'Y') {
            return true;
        } else {
            return false;
        }
    }

    public function getRedirectUrl()
    {
        return $this->xml->url;
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return null;
    }

}
