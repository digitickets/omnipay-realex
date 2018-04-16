<?php

namespace Omnipay\Realex\Message\Redirect;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Realex Response
 */
class DataVaultResponse extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data = array())
    {
        $this->request = $request;
        $this->data = $data;
    }

    public function isSuccessful()
    {
        if (!$this->isPayerSetupSuccessful()) {
            return false;
        } elseif (!$this->isPayerCardAddSuccessful()) {
            return false;
        } else {
            return isset($this->data['RESULT']) && '00' === $this->data['RESULT'];
        }
    }

    public function getMessage()
    {
        $message;
        if (isset($this->data['PMT_SETUP_MSG'])) {
            $message .= $this->data['PMT_SETUP_MSG'] . ' ';
        }
        if (isset($this->data['PAYER_SETUP_MSG'])) {
            $message .= $this->data['PAYER_SETUP_MSG'] . ' ';
        }
        if (isset($this->data['MESSAGE'])) {
            $message .= $this->data['MESSAGE'] . ' ';
        }

        return $message;
    }

    public function getTransactionReference()
    {
        return isset($this->data['PASREF']) ? $this->data['PASREF'] : null;
    }

    public function isPayerSetupSuccessful()
    {
        return !isset($this->data['PAYER_SETUP']) ||
            (isset($this->data['PAYER_SETUP']) && '00' === $this->data['PAYER_SETUP']);
    }

    public function isPayerCardAddSuccessful()
    {
        return !isset($this->data['PMT_SETUP']) ||
            (isset($this->data['PMT_SETUP']) && '00' === $this->data['PMT_SETUP']);
    }

    public function getCardToken()
    {
        return isset($this->data['SAVED_PMT_REF']) ? $this->data['SAVED_PMT_REF'] : null;
    }
}
