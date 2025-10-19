<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Antikirra\AbstractToken;

class MinValidTypeToken extends AbstractToken
{
    protected static function type(): int
    {
        return 1;
    }

    protected static function salt(): string
    {
        return 'min_valid_type_salt_at_least_32_bytes_long_secure';
    }

    protected static function algorithm(): string
    {
        return 'sha256';
    }
}
