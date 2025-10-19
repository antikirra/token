<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Antikirra\AbstractToken;

class NegativeTypeToken extends AbstractToken
{
    protected static function type(): int
    {
        return -1;
    }

    protected static function salt(): string
    {
        return 'negative_type_salt_at_least_32_bytes_long_for_security';
    }

    protected static function algorithm(): string
    {
        return 'sha256';
    }
}
