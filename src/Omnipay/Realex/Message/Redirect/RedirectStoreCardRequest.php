<?php

namespace Omnipay\Realex\Message\Redirect;

/**
 * Realex Redirect Purchase Request
 */
class RedirectStoreCardRequest extends RedirectDataVaultRequest
{
    public function getCustomerRef()
    {
        return $this->getParameter('customerRef');
    }

    public function setCustomerRef($data)
    {
        $this->setParameter('customerRef', $data);
    }

    public function getCustomerExists()
    {
        return $this->getParameter('customerExists');
    }

    public function setCustomerExists($data)
    {
        $this->setParameter('customerExists', $data);
    }

    public function getValidateCardOnly()
    {
        return $this->getParameter('validateCardOnly');
    }

    public function setValidateCardOnly($data)
    {
        $this->setParameter('validateCardOnly', $data);
    }

    public function getOfferSaveCard()
    {
        return $this->getParameter('offerSaveCard');
    }

    public function setOfferSaveCard($data)
    {
        $this->setParameter('offerSaveCard', $data);
    }

    public function getCardPaymentButton()
    {
        return $this->getParameter('cardPaymentButton');
    }

    public function setCardPaymentButton($data)
    {
        $this->setParameter('cardPaymentButton', $data);
    }

    public function getHPPPostDimensions()
    {
        return $this->getParameter('HPPPostDimensions');
    }

    public function setHPPPostDimensions($data)
    {
        $this->setParameter('HPPPostDimensions', $data);
    }

    public function getHPPPostResponse()
    {
        return $this->getParameter('HPPPostResponse');
    }

    public function setHPPPostResponse($data)
    {
        $this->setParameter('HPPPostResponse', $data);
    }

    public function getData()
    {
        $data['extraData'] = [
            'PAYER_REF' => $this->getCustomerRef(),
            'PAYER_EXIST' => $this->getCustomerExists(),
            'VALIDATE_CARD_ONLY' => $this->getValidateCardOnly(),
            'OFFER_SAVE_CARD' => $this->getOfferSaveCard(),
            'CARD_STORAGE_ENABLE' => '1',
            'CARD_PAYMENT_BUTTON' => $this->getCardPaymentButton(),
            'HPP_POST_DIMENSIONS' => $this->getHPPPostDimensions(),
            'HPP_POST_RESPONSE' => $this->getHPPPostResponse(),
        ];

        return $data;
    }

    public function sendData($data)
    {
        return $this->response = new RedirectStoreCardResponse($this, $data);
    }
}
