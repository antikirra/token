# [DRAFT] Abstract Token

![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/antikirra/token/php)
![Packagist Version](https://img.shields.io/packagist/v/antikirra/token)

## Install

```console
composer require antikirra/token
```

## Basic usage

```php
<?php

declare(strict_types=1);

use Antikirra\AbstractToken;

require __DIR__ . '/vendor/autoload.php';

final class MySecretToken extends AbstractToken
{
    protected static function type(): int
    {
        // token type in the range from 1 to 255
        return 1;
    }

    protected static function salt(): string
    {
        // !!! DO NOT MODIFY AFTER SETUP !!!
        return '4Q8myx0n8mrdLs6ZdEvpp9ekV78nhn5P4ruf9Z96tu4ZEVlmWeGawymg3W0mkgPj';
    }

    protected static function algorithm(): string
    {
        // hash()
        return 'xxh128';
    }
}

$token = MySecretToken::create(123456, new DateTimeImmutable('+1 day'));

var_dump((string)$token); // AQBA4gEAAAAAAPDcoGguuFT3rMY17QZy-gmNOs1dIQWcR

print_r($token);
//MySecretToken Object
//(
//    [type:protected] => 1
//    [identity:protected] => 123456
//    [expiredAt:protected] => DateTimeImmutable Object
//        (
//            [date] => 2025-08-16 19:33:33.355526
//            [timezone_type] => 3
//            [timezone] => UTC
//        )
//
//    [nonce:protected] => 2851849735
//    [signature:protected] => <bynary>
//)
```
