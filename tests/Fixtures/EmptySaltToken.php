<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Antikirra\AbstractToken;

class EmptySaltToken extends AbstractToken
{
    protected static function type(): int
    {
        return 1;
    }

    protected static function salt(): string
    {
        return '';
    }

    protected static function algorithm(): string
    {
        return 'sha256';
    }
}
