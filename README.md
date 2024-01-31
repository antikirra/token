# [DRAFT] Abstract Token

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

class MySecretToken extends AbstractToken
{
    protected static function definedType(): int
    {
        // token type in the range from 1 to 255
        return 1;
    }

    protected static function definedSalt(): string
    {
        // random string of arbitrary length
        // !!! DO NOT MODIFY AFTER SETUP !!!
        return '@5dCG!sZP%bF6e38K+xaa~R!2pp2r+b+r=9RXS+aYf?xT2Dc_Pp5@3k775fyH76P';
    }
}

$token = MySecretToken::create(123456, new DateTimeImmutable('+1 day'));

var_dump((string)$token); // AQBA4gEAAAAAAAcBvGXmTCS9BhCtddOfctGTZQLT_Be1EQ

print_r($token);
// MySecretToken Object
// (
//     [type:protected] => 1
//     [userId] => 123456
//     [expiredAt:protected] => DateTimeImmutable Object
// (
//     [date] => 2024-01-01 00:00:00.000000
//             [timezone_type] => 3
//             [timezone] => UTC
//         )
//
//     [nonce:protected] => 4061102716
//     [signature:protected] => <binary>
// )
```
