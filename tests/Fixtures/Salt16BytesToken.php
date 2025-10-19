<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Antikirra\Token;

class Salt16BytesToken extends Token
{
    protected static function type(): int
    {
        return 1;
    }

    protected static function salt(): string
    {
        return '1234567890123456'; // Exactly 16 bytes
    }

    protected static function algorithm(): string
    {
        return 'sha256';
    }
}
