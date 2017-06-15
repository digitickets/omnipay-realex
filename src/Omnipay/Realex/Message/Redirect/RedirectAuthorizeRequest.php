<?php

namespace Omnipay\Realex\Message\Redirect;

/**
 * Realex Redirect Authorize Request
 */
class RedirectAuthorizeRequest extends AbstractRequest
{
    public function getData()
    {
        return null;
    }

    public function getRedirectData()
    {
        return $this->getBaseData();
    }

    public function sendData($data)
    {
        return $this->response = new RedirectAuthorizeResponse($this, $data);
    }
}
