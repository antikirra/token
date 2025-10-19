<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Antikirra\AbstractToken;

class MaxValidTypeToken extends AbstractToken
{
    protected static function type(): int
    {
        return 255;
    }

    protected static function salt(): string
    {
        return 'max_valid_type_salt_at_least_32_bytes_long_secure';
    }

    protected static function algorithm(): string
    {
        return 'sha256';
    }
}
