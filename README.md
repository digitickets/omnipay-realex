# Omnipay: Realex

**Realex driver for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/coatesap/omnipay-realex.png?branch=master)](https://travis-ci.org/coatesap/omnipay-realex)
[![Latest Stable Version](https://poser.pugx.org/coatesap/omnipay-realex/version.png)](https://packagist.org/packages/coatesap/omnipay-realex)
[![Total Downloads](https://poser.pugx.org/coatesap/omnipay-realex/d/total.png)](https://packagist.org/packages/coatesap/omnipay-realex)

[Omnipay](https://github.com/omnipay/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements Realex (Remote MPI) support for Omnipay.

## Installation

The realex driver is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "coatesap/omnipay-realex": "~2.0"
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