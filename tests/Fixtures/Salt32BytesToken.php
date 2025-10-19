<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Antikirra\Token;

class Salt32BytesToken extends Token
{
    protected static function type(): int
    {
        return 1;
    }

    protected static function salt(): string
    {
        return '12345678901234567890123456789012'; // Exactly 32 bytes (minimum valid)
    }

    protected static function algorithm(): string
    {
        return 'sha256';
    }
}
