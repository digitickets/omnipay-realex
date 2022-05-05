<?php

namespace Omnipay\Realex\Message\Redirect;

use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Realex Redirect Complete Authorize Request
 */
class RedirectCompleteAuthorizeRequest extends AbstractRequest
{
    public function getData()
    {
        // Build initial hash
        $hash = sha1(implode('.', array(
            $this->httpRequest->request->get('TIMESTAMP'),
            $this->httpRequest->request->get('MERCHANT_ID'),
            $this->httpRequest->request->get('ORDER_ID'),
            $this->httpRequest->request->get('RESULT'),
            $this->httpRequest->request->get('MESSAGE'),
            $this->httpRequest->request->get('PASREF'),
            $this->httpRequest->request->get('AUTHCODE')
        )));

        // Validate signature
        if ($this->httpRequest->request->get('SHA1HASH') !== sha1($hash . '.' . $this->getSecret())) {
            throw new InvalidResponseException;
        }

        return $this->httpRequest->request->all();
    }

    public function sendData($data)
    {
        return $this->response = new Response($this, $data);
    }
}
