<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Antikirra\Token;

class NumericAlgorithmToken extends Token
{
    protected static function type(): int
    {
        return 1;
    }

    protected static function salt(): string
    {
        return 'test_salt_that_is_at_least_32_bytes_long_for_security';
    }

    protected static function algorithm(): string
    {
        return '12345';
    }
}
