<?php

namespace Omnipay\Realex\Message\Redirect;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Realex Redirect Purchase Response
 */
class RedirectStoreCardResponse extends RedirectDataVaultResponse
{
    public function getRedirectData()
    {
        $data = $this->getRequest()->getRedirectData();

        return $data;
    }
}
