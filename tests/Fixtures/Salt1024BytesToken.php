<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Antikirra\AbstractToken;

class Salt1024BytesToken extends AbstractToken
{
    protected static function type(): int
    {
        return 1;
    }

    protected static function salt(): string
    {
        // Exactly 1024 bytes (large valid value)
        return str_repeat('a', 1024);
    }

    protected static function algorithm(): string
    {
        return 'sha256';
    }
}
