<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Realex Purchase Request
 */
abstract class RemoteAbstractRequest extends AbstractRequest
{
    protected $cardBrandMap = array(
        'mastercard'  => 'mc',
        'diners_club' => 'diners',
    );

    public function getCode()
    {
        return $this->getParameter('code');
    }

    public function setCode($postcode, $billingAddressLine1)
    {
        $postcode            = $this->stripNonNumeric($postcode);
        $billingAddressLine1 = $this->stripNonNumeric($billingAddressLine1);

        return $this->setParameter('code', $postcode . '|' . $billingAddressLine1);
    }

    public function stripNonNumeric($value)
    {
        return preg_replace("/[^0-9]/", "", $value);
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getAccount()
    {
        return $this->getParameter('account');
    }

    public function setAccount($value)
    {
        return $this->setParameter('account', $value);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    public function getReturnUrl()
    {
        return $this->getParameter('returnUrl');
    }

    public function setReturnUrl($value)
    {
        return $this->setParameter('returnUrl', $value);
    }

    public function sendData($data)
    {
        // register the payment
        $headers = array(
            'curl' => array(
                CURLOPT_SSLVERSION     => 1,
                CURLOPT_SSL_VERIFYPEER => false,
            ),
        );

        if (is_array($data)) {
            $data = http_build_query($data);
        }

        $httpResponse = $this->httpClient->request('POST', $this->getEndpoint(), $headers, $data);

        return $this->createResponse($httpResponse->getBody()->getContents());
    }

    abstract public function getEndpoint();

    abstract protected function createResponse($data);

    /**
     * Override some of the default Omnipay card brand names
     *
     * @return mixed
     */
    protected function getCardBrand()
    {
        $brand = $this->getCard()->getBrand();

        if (isset($this->cardBrandMap[ $brand ])) {
            $brand = $this->cardBrandMap[ $brand ];
        }

        return strtoupper($brand);
    }
}
