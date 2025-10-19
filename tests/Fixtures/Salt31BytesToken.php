<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Antikirra\Token;

class Salt31BytesToken extends Token
{
    protected static function type(): int
    {
        return 1;
    }

    protected static function salt(): string
    {
        return '1234567890123456789012345678901'; // Exactly 31 bytes
    }

    protected static function algorithm(): string
    {
        return 'sha256';
    }
}
