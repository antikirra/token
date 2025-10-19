<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Antikirra\AbstractToken;

class ZeroTypeToken extends AbstractToken
{
    protected static function type(): int
    {
        return 0;
    }

    protected static function salt(): string
    {
        return 'zero_type_salt_at_least_32_bytes_long_for_security_';
    }

    protected static function algorithm(): string
    {
        return 'sha256';
    }
}
