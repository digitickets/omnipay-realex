<?php

namespace Omnipay\Realex\Message\Redirect;

use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Realex Redirect Complete Authorize Request
 */
class RedirectCompleteStoreCardRequest extends AbstractRequest
{
    public function getData()
    {
        $data;
        foreach (json_decode($this->httpRequest->request->get('hppResponse')) as $key => $value) {
            $data[$key] = base64_decode($value);
        }

        // Build initial hash
        $hash = sha1(implode('.', array(
            $data['TIMESTAMP'],
            $data['MERCHANT_ID'],
            $data['ORDER_ID'],
            $data['RESULT'],
            $data['MESSAGE'],
            $data['PASREF'],
            $data['AUTHCODE'],
        )));

        // Validate signature
        if ($data['SHA1HASH'] !== sha1($hash . '.' . $this->getSecret())) {
            throw new InvalidResponseException;
        }

        return $data;
    }

    public function sendData($data)
    {
        return $this->response = new dataVaultResponse($this, $data);
    }
}
