<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Antikirra\AbstractToken;

class HighOverflowTypeToken extends AbstractToken
{
    protected static function type(): int
    {
        return 1000;
    }

    protected static function salt(): string
    {
        return 'high_overflow_type_salt_at_least_32_bytes_secure_';
    }

    protected static function algorithm(): string
    {
        return 'sha256';
    }
}
