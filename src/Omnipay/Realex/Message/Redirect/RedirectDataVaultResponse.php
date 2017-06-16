<?php

namespace Omnipay\Realex\Message\Redirect;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Realex Redirect Authorize Response
 */
class RedirectDataVaultResponse extends Response implements RedirectResponseInterface
{
    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return true;
    }

    public function getRedirectUrl()
    {
        return $this->getRequest()->getCheckoutEndpoint();
    }

    public function getTransactionReference()
    {
        return $this->getRequest()->getTransactionId();
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        $data = $this->getRequest()->getBaseData(false);
        return $data;
    }
}
