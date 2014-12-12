<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Realex Enrolment Response
 *
 * @property EnrolmentRequest $request
 */
class EnrolmentResponse extends RemoteAbstractResponse implements RedirectResponseInterface
{
    public function isSuccessful()
    {
        return false;
    }

    public function isEnrolled()
    {
        return $this->xml->enrolled == 'Y';
    }

    public function getMessage()
    {
        $message = (string)$this->xml->message;

        return $message;
    }

    public function getTransactionReference()
    {
        $transactionReference = !empty($this->xml->pasref) ? $this->xml->pasref : null;

        return $transactionReference;
    }

    public function isRedirect()
    {
        if ($this->xml->result == '00' && $this->xml->enrolled == 'Y') {
            return true;
        } else {
            return false;
        }
    }

    public function getRedirectUrl()
    {
        return (string)$this->xml->url;
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    /**
     * Any encrypted data that we wish to have returned to us.
     * Basically, this is all the card data that we will have to
     * re-submit to to Realex in order to complete the authorisation.
     */
    protected function getMerchantData()
    {
        /**
         * @var CreditCard $card
         */
        $card = $this->request->getCard();
        $data = array(
            'transactionId' => $this->request->getTransactionId(),
            'currency'             => $this->request->getCurrency(),
            'amount'               => $this->request->getAmount(),
            'number'               => $card->getNumber(),
            'expiryMonth'          => $card->getExpiryMonth(),
            'expiryYear'           => $card->getExpiryYear(),
            'billingName'          => $card->getBillingName(),
            'cvv'                  => $card->getCvv(),
            'issueNumber'          => $card->getIssueNumber(),
            'billingCountry'       => $card->getBillingCountry()
        );
        $serialised = json_encode($data);
        $encoded = base64_encode($serialised);

        return $encoded;
    }

    public function getRedirectData()
    {
        return array(
            'PaReq'   => (string)$this->xml->pareq,
            'TermUrl' => $this->request->getReturnUrl(),
            'MD'      => $this->getMerchantData()
        );
    }
}
