# B2BinPay API v2 client for PHP

## Requirements

+ [B2BinPay](https://b2binpay.com) account
+ PHP >= 7.4
+ PHP extensions enabled: cURL, JSON

## Composer Installation

The easiest way to install the B2BinPay API client is to require it
with [Composer](http://getcomposer.org/doc/00-intro.md) through command-line:

```shell
composer require basbit/b2binpay
```

or by editing `composer.json`:

```json
    {
  "require": {
    "basbit/b2binpay": "^1"
  }
}
```

## Local installation

```bash
composer install --no-dev
cp .env.example .env
```

## Getting started

### Create Provider instance

Use the API key and secret to access your B2BinPay account:

```php
$provider = new B2Binpay\Provider(
    'API_KEY',
    'API_SECRET',
    'https://calback.com/callback'
);
``` 

#### Test Mode

In order to use testing sandbox, pass `true` as a third parameter for `B2Binpay\Provider`:

```php
$provider = new B2Binpay\Provider(
    'API_KEY',
    'API_SECRET',
    'https://calback.com/callback',
    true
);
``` 

**Warning:** Sandbox and main gateway have their own pairs of key and secret!

## License

B2BinPay\API-PHP is licensed under the [MIT License](https://github.com/b2binpay/api-php/blob/master/LICENSE).
