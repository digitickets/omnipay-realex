<?php

namespace Omnipay\Realex\Message\Redirect;

/**
 * Realex Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
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

    public function setStore($value)
    {
        return $this->setParameter('store', $value);
    }

    public function getStore()
    {
        return $this->getParameters('store');
    }

    public function getPares()
    {
        return $this->getParameter('pares');
    }

    public function setPares($value)
    {
        return $this->setParameter('pares', $value);
    }

    public function setCavv($value)
    {
        return $this->setParameter('cavv', $value);
    }

    public function getCavv()
    {
        return $this->getParameter('cavv');
    }

    public function setXid($value)
    {
        return $this->setParameter('xid', $value);
    }

    public function getXid()
    {
        return $this->getParameter('xid');
    }

    public function setEci($value)
    {
        return $this->setParameter('eci', $value);
    }

    public function getEci()
    {
        return $this->getParameter('eci');
    }

    public function setNotifyUrl($value)
    {
        return $this->setParameter('notifyUrl', $value);
    }

    public function getNotifyUrl()
    {
        return $this->getParameter('notifyUrl');
    }

    public function setExtraData($value)
    {
        return $this->setParameter('extraData', $value);
    }

    public function getExtraData()
    {
        return $this->getParameter('extraData');
    }

    public function getBaseData($autoSettle = true, $card = null)
    {
        $data = array(
            'EXTRA' => $this->getExtraData(),
            'MERCHANT_ID' => $this->getMerchantId(),
            'ORDER_ID' => $this->getTransactionId(),
            'CURRENCY' => $this->getCurrency(),
            'MERCHANT_RESPONSE_URL' => $this->getReturnUrl(),
            'AMOUNT' => round($this->getAmount() * 100),
            'TIMESTAMP' => gmdate('YmdHis'),
            'AUTO_SETTLE_FLAG' => $autoSettle
        );
        $data['SHA1HASH'] = $this->createSignature($data, 'sha1', $card);
        return $data;
    }

    public function createSignature($data, $method = 'sha1', $card = null)
    {
        $hash = $method(rtrim(implode('.', array(
            $data['TIMESTAMP'],
            $data['MERCHANT_ID'],
            $data['ORDER_ID'],
            $data['AMOUNT'],
            $data['CURRENCY'],
            $card !== null ? $card->getNumber() : null
                )), '.'));

        return $method($hash . '.' . $this->getSecret());
    }

    public function getRequestXML(
        $card,
        $autoSettle = true,
        $extraData = array(),
        $addressData = true,
        $cardData = true
    ) {
        $data = $this->getBaseData($autoSettle, $card);
        $brand = (strcasecmp($card->getBrand(), "mastercard") == 0) ? "mc" : $card->getBrand();
        $request = new \SimpleXMLElement('<request />');
        $request['timestamp'] = $data['TIMESTAMP'];
        $request['type'] = $this->getType();
        $request->merchantid = $this->getMerchantId();
        $request->account = $this->getAccount();
        $request->orderid = $data['ORDER_ID'];
        //$request->md5hash            = $this->createSignature($data, 'md5', $card);
        $request->custipaddress = $this->getClientIp();
        $request->amount = $data['AMOUNT'];
        $request->amount['currency'] = $data['CURRENCY'];
        $request->autosettle['flag'] = (int) $data['AUTO_SETTLE_FLAG'];
        // Flesh out the XML structure
        $request->addChild('card');
        $request->card->addChild('cvn');
        $request->card->number = $card->getNumber();
        $request->card->expdate = $card->getExpiryDate('my');
        $request->card->type = strtoupper($brand);
        $request->card->chname = $card->getName();
        // Not all request want this data
        if ($cardData) {
            $request->card->issueno = $card->getIssueNumber();
            $request->card->addChild('cvn');
            $request->card->cvn->addChild('number', $card->getCvv());
            $request->card->cvn->addChild('presind', 1);
        }
        // not all requests want this data
        if ($addressData) {
            $request->address['type'] = 'billing';
            $request->address->code = $card->getBillingPostcode();
            $request->address->country = strtoupper($card->getBillingCountry());
        }
        // Add in extra array data for any obscure fields
        if (!empty($extraData)) {
            foreach ($extraData as $key => $value) {
                $request->$key = $value;
            }
        }
        $request->sha1hash = $data['SHA1HASH'];
        return $request->asXML();
    }

    protected function getType()
    {
        return 'auth';
    }

    public function getCheckoutEndpoint()
    {
        return $this->getParameter('checkoutEndpoint');
    }

    public function setCheckoutEndpoint($value)
    {
        return $this->setParameter('checkoutEndpoint', $value);
    }
}
