<?php

namespace Omnipay\Realex\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Realex Abstract Response
 */
abstract class RemoteAbstractResponse extends AbstractResponse implements RedirectResponseInterface
{
    protected $xml;

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $this->xml = $this->parseXml($data);
    }

    /**
     * Turn the raw XML response string into a SimpleXMLElement
     *
     * @param string $data
     * @return \SimpleXMLElement
     */
    public function parseXml($data)
    {
        $data = str_replace('  ', ' ', $data);
        $data = str_replace("\n", '', $data);
        $data = str_replace("\r", '', $data);

        return new \SimpleXMLElement($data);
    }
}
