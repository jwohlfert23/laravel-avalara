
# Simple Avalara Client for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jwohlfert23/laravel-avalara.svg?style=flat-square)](https://packagist.org/packages/jwohlfert23/laravel-avalara)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/jwohlfert23/laravel-avalara/run-tests?label=tests)](https://github.com/jwohlfert23/laravel-avalara/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/jwohlfert23/laravel-avalara/Check%20&%20fix%20styling?label=code%20style)](https://github.com/jwohlfert23/laravel-avalara/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jwohlfert23/laravel-avalara.svg?style=flat-square)](https://packagist.org/packages/jwohlfert23/laravel-avalara)

We built this client because of problems with the existing SDK provided by Avalara:

1. Lack of support for PHP8.
2. No transformation of responses to PHP models/objects.
3. Avalara only has a 99.5% SLA, and we thought built-in retries for certain endpoints would be helpful.

## Installation

You can install the package via composer:

```bash
composer require jwohlfert23/laravel-avalara
```

You should publish the config file:

```bash
php artisan vendor:publish --tag="avalara-config"
```

## Usage

```php
$transaction = new CreateTransaction();

$transaction->date = now();
$transaction->type = AvalaraDocType::SALES_ORDER;
$transaction->customerCode = 'jack@gmail.com';

$transaction->addresses['ShipFrom'] = new \App\Services\Avalara\Models\AddressModel();
$transaction->addresses['ShipTo'] = new \App\Services\Avalara\Models\AddressModel();
  
$transaction->lines[] = new CreateLineItem(
    number: 0,
    amount: 50.00
    quantity: 2,
    taxCode: 'P0000000'
);

return AvalaraClient::createTransaction($transaction);
// or
return $transaction->create();
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
