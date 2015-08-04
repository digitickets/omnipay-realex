<?php
/**
 * @author Philip Wright- Christie <pwrightchristie.sfp@gmail.com>
 * Date: 04/08/15
 */

namespace Omnipay\Realex\Message;

class UpdateCustomerResponse extends RemoteAbstractResponse
{
    public function isSuccessful()
    {
        return ($this->xml->result == '00');
    }

    public function getMessage()
    {
        return (string) $this->xml->message;
    }

    public function isRedirect()
    {
        return false;
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return null;
    }

    /**
     * Gets the redirect target url.
     */
    public function getRedirectUrl()
    {
        return '';
    }
}
