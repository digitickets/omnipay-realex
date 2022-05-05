<?php

namespace Omnipay\Realex;

use Omnipay\Common\AbstractGateway;

/**
 * Realex Redirect Class
 */
class RedirectGateway extends AbstractGateway
{
    public function getName()
    {
        return 'Realex Redirect';
    }

    public function getDefaultParameters()
    {
        return array(
            'merchantId' => '',
            'secret' => '',
            'account' => 'internet',
            'testMode' => false,
            'checkoutEndpoint' => 'https://hpp.realexpayments.com/pay',
        );
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    public function getAccount()
    {
        return $this->getParameter('account');
    }

    public function setAccount($value)
    {
        return $this->setParameter('account', $value);
    }

    public function getCheckoutEndpoint()
    {
        return $this->getParameter('checkoutEndpoint');
    }

    public function setCheckoutEndpoint($value)
    {
        return $this->setParameter('checkoutEndpoint', $value);
    }

    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\Redirect\RedirectAuthorizeRequest', $parameters);
    }

    public function completeAuthorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\Redirect\RedirectCompleteAuthorizeRequest', $parameters);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\Redirect\RedirectPurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->completeAuthorize($parameters);
    }

    public function storeCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\Redirect\RedirectStoreCardRequest', $parameters);
    }

    public function completeStoreCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\Redirect\RedirectCompleteStoreCardRequest', $parameters);
    }
}
