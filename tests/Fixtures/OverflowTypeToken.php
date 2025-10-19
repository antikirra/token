<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Antikirra\Token;

class OverflowTypeToken extends Token
{
    protected static function type(): int
    {
        return 256;
    }

    protected static function salt(): string
    {
        return 'overflow_type_salt_at_least_32_bytes_long_secure_';
    }

    protected static function algorithm(): string
    {
        return 'sha256';
    }
}
