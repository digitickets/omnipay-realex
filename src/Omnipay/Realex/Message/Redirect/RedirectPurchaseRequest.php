<?php

namespace Omnipay\Realex\Message\Redirect;

/**
 * Realex Redirect Purchase Request
 */
class RedirectPurchaseRequest extends RedirectAuthorizeRequest
{
    public function sendData($data)
    {
        return $this->response = new RedirectPurchaseResponse($this, $data);
    }
}
