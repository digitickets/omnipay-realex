<?php

namespace Omnipay\Realex\Message\Redirect;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Realex Redirect Purchase Response
 */
class RedirectPurchaseResponse extends RedirectAuthorizeResponse
{
    public function getRedirectData()
    {
        return $this->getRequest()->getBaseData();
    }
}
