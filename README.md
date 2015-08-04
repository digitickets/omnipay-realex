# Omnipay: Realex

**Realex driver with 3D Secure support for Omnipay payment processing library**

[![Build Status](https://travis-ci.org/coatesap/omnipay-realex.png?branch=master)](https://travis-ci.org/coatesap/omnipay-realex)
[![Latest Stable Version](https://poser.pugx.org/coatesap/omnipay-realex/version.png)](https://packagist.org/packages/coatesap/omnipay-realex)
[![Total Downloads](https://poser.pugx.org/coatesap/omnipay-realex/d/total.png)](https://packagist.org/packages/coatesap/omnipay-realex)

[Omnipay](https://github.com/omnipay/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements Realex (Remote MPI) integration for Omnipay, including optional 3D Secure support.

## Installation

The realex driver is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "coatesap/omnipay-realex": "~3.0"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* Realex_Remote

For general usage instructions, please see the main [Omnipay](https://github.com/omnipay/omnipay)
repository.

## 3D Secure

The Realex driver has 3D Secure checking turned off by default.
To enable 3D Secure, make sure you have received a 3D Secure account reference from Realex, then set the `3dSecure` parameter as '1' when you initialise the gateway.

## Refunds

In order to process a refund, you must configure the gateway with the `refundPassword` parameter set to the 'rebate' password that Realex provide you with. In addition, you will need to pass the following parameters, relating to the original transaction: `amount`, `transactionReference`, `transactionId`, `currency`, `authCode`.

## Saved Cards

To save a card, you need to supply the `customerRef` and `cardReference` parameters. If the customer reference doesn't exist on Realex (you can check this with `$response->customerDoesntExist()` ), you must create the customer using `$gateway->createCustomer()`. Once the customer & card is setup, you can authorise a payment by supplying the card reference & customer reference instead of the card details:

```php
$gateway->purchase(
    [
        'transactionId' => $transactionId,
        'customerRef'   => $customerRef,
        'amount'        => $amount,
        'currency'      => $currency,
        'cardReference' => $cardRef,
        'card'          => ['cvv' => $cvv]
    ]
);
```