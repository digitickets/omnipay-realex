<?php

namespace Omnipay\Realex;

use Omnipay\Common\AbstractGateway;
use Omnipay\Realex\Message\AuthRequest;
use Omnipay\Realex\Message\AuthResponse;
use Omnipay\Realex\Message\RemoteAbstractResponse;
use Omnipay\Realex\Message\VerifySigRequest;
use Omnipay\Realex\Message\VerifySigResponse;

/**
 * Realex Remote Gateway
 */
class RemoteGateway extends AbstractGateway
{
    public function getName()
    {
        return 'Realex Remote';
    }

    public function getDefaultParameters()
    {
        return array(
            'merchantId' => '',
            'account' => '',
            'secret' => '',
            '3dSecure' => 0,
            'authEndpoint' => 'https://epage.payandshop.com/epage-remote.cgi',
            'secureDataVaultEndpoint' => 'https://epage.payandshop.com/epage-remote-plugins.cgi',
            '3dSecureEndpoint' => 'https://epage.payandshop.com/epage-3dsecure.cgi'
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

    public function getRefundPassword()
    {
        return $this->getParameter('refundPassword');
    }

    /**
     * Although Omnipay terminology deals with 'refunds', you need
     * to actually supply the 'rebate' password that Realex gives you
     * in order for this to work.
     *
     * @param string $value The 'rebate' password supplied by Realex
     *
     * @return $this
     */
    public function setRefundPassword($value)
    {
        return $this->setParameter('refundPassword', $value);
    }

    public function get3dSecure()
    {
        return $this->getParameter('3dSecure');
    }

    public function set3dSecure($value)
    {
        return $this->setParameter('3dSecure', $value);
    }

    public function purchase(array $parameters = array())
    {

        if (array_key_exists('mobileType', $parameters)) {
            return $this->createRequest('\Omnipay\Realex\Message\AuthMobileRequest', $parameters);
        } elseif (array_key_exists('cardReference', $parameters)) {
            return $this->createRequest('\Omnipay\Realex\Message\SavedAuthRequest', $parameters);
        } elseif ($this->get3dSecure()) {
            return $this->createRequest('\Omnipay\Realex\Message\EnrolmentRequest', $parameters);
        } else {
            return $this->createRequest('\Omnipay\Realex\Message\AuthRequest', $parameters);
        }
    }

    /**
     * This will always be called as the result of returning from 3D Secure.
     * Verify that the 3D Secure message we've received is legit
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\VerifySigRequest', $parameters);
    }

    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\RefundRequest', $parameters);
    }

    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\VoidRequest', $parameters);
    }

    public function fetchTransaction(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\FetchTransactionRequest', $parameters);
    }

    /**
     * Create/update/delete card/customer details
     */
    public function createCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\CreateCardRequest', $parameters);
    }

    public function updateCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\UpdateCardRequest', $parameters);
    }

    public function deleteCard(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\DeleteCardRequest', $parameters);
    }

    public function createCustomer(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\CreateCustomerRequest', $parameters);
    }

    public function updateCustomer(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Realex\Message\UpdateCustomerRequest', $parameters);
    }

    /**
     * Setup service endpoint links for different realex service providers
     */
    public function setAuthEndpoint($value)
    {
        return $this->setParameter('authEndpoint', $value);
    }

    public function getAuthEndpoint()
    {
        return $this->getParameter('authEndpoint');
    }

    public function setSecureDataVaultEndpoint($value)
    {
        return $this->setParameter('secureDataVaultEndpoint', $value);
    }

    public function getSecureDataVaultEndpoint()
    {
        return $this->getParameter('secureDataVaultEndpoint');
    }

    public function set3DSecureEndpoint($value)
    {
        return $this->setParameter('3dSecureEndpoint', $value);
    }

    public function get3DSecureEndpoint()
    {
        return $this->getParameter('3dSecureEndpoint');
    }
}
