<?php

namespace Omnipay\Realex\Message\Redirect;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Realex Response
 */
class Response extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data = array())
    {
        $this->request = $request;
        $this->data = $data;
    }

    public function isSuccessful()
    {
        return isset($this->data['RESULT']) && '00' === $this->data['RESULT'];
    }

    public function getMessage()
    {
        return isset($this->data['MESSAGE']) ? $this->data['MESSAGE'] : null;
    }

    public function getTransactionReference()
    {
        return isset($this->data['PASREF']) ? $this->data['PASREF'] : null;
    }
}
