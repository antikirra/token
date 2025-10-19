<?php

declare(strict_types=1);

namespace Antikirra;

use DateTimeImmutable;
use DateTimeInterface;
use RuntimeException;

abstract class Token
{
    abstract protected static function type(): int;

    abstract protected static function salt(): string;

    abstract protected static function algorithm(): string;

    final private function __construct(
        protected readonly int               $type,
        protected readonly int|float         $identity,
        protected readonly DateTimeInterface $expiredAt,
        protected readonly int               $nonce,
        protected readonly string            $signature
    )
    {
        if (mb_strlen(static::salt(), '8bit') < 32) {
            throw new RuntimeException('salt cannot be less than 32 bytes');
        }

        if ($type < 1 || $type > 255) {
            throw new RuntimeException('$type < 1 || $type > 255');
        }

        // Validate identity boundaries
        // For floats with precision issues, use string comparison
        if (is_float($identity)) {
            $identityStr = sprintf('%.0f', $identity);
            $maxStr = (string)PHP_INT_MAX;
            if ($identity < 1 || strcmp($identityStr, $maxStr) > 0) {
                throw new RuntimeException('$identity < 1 || $identity > PHP_INT_MAX');
            }
        } else if ($identity < 1 || $identity > PHP_INT_MAX) {
            throw new RuntimeException('$identity < 1 || $identity > PHP_INT_MAX');
        }
    }

    final public function typeOf(int $type): bool
    {
        return $this->type === $type;
    }

    final public function isExpired(): bool
    {
        return $this->expiredAt->getTimestamp() < time();
    }

    final public function getIdentity(): int|float
    {
        return $this->identity;
    }

    final public function getExpiredAt(): DateTimeInterface
    {
        return $this->expiredAt;
    }

    final public static function create(int|float $identity, DateTimeInterface $expiredAt, ?int $type = null): static
    {
        $expiredAt = DateTimeImmutable::createFromInterface($expiredAt);

        $type ??= static::type();
        $nonce = random_int(268435456, 4294967295);
        $sign = static::sign($type, $identity, $expiredAt->getTimestamp(), $nonce);

        return new static($type, $identity, $expiredAt, $nonce, $sign);
    }

    final protected static function sign(int $type, int|float $identity, int $expiredAt, int $nonce): string
    {
        if (!in_array(static::algorithm(), hash_algos(), true)) {
            throw new \ValueError('algorithm is not supported');
        }

        return hash(static::algorithm(), static::salt() . ">{$nonce}%{$expiredAt}#{$identity}%{$type}<", true);
    }

    final public function encode(): string
    {
        $expiredAt = $this->expiredAt->getTimestamp();
        $packed = pack('vPVVa*', $this->type, $this->identity, $expiredAt, $this->nonce, $this->signature);

        return base64url_encode($packed);
    }

    final public static function decode(string $raw): static
    {
        $decoded = base64url_decode($raw);

        if (empty($decoded)) {
            throw new RuntimeException('base64url_decode failed');
        }

        // Minimum required bytes: v(2) + P(8) + V(4) + V(4) = 18 bytes
        if (mb_strlen($decoded, '8bit') < 18) {
            throw new RuntimeException('decoded data too short');
        }

        $data = unpack('vtype/Pidentity/VexpiredAt/Vnonce/a*sign', $decoded);

        if ($data['type'] < 1 || $data['type'] > 255) {
            throw new RuntimeException('$data[\'type\'] < 1 || $data[\'type\'] > 255');
        }

        if ($data['identity'] < 1 || $data['identity'] > PHP_INT_MAX) {
            throw new RuntimeException('$data[\'identity\'] < 1 || $data[\'identity\'] > PHP_INT_MAX');
        }

        if ($data['nonce'] < 268435456 || $data['nonce'] > 4294967295) {
            throw new RuntimeException('$data[\'nonce\'] < 268435456 || $data[\'nonce\'] > 4294967295');
        }

        $sign = static::sign($data['type'], (int)$data['identity'], $data['expiredAt'], $data['nonce']);

        if (!hash_equals($sign, $data['sign'])) {
            throw new RuntimeException('!hash_equals($sign, $data[\'sign\'])');
        }

        $expiredAt = DateTimeImmutable::createFromFormat('U', (string)$data['expiredAt']);

        return new static($data['type'], $data['identity'], $expiredAt, $data['nonce'], $data['sign']);
    }

    final public function __toString(): string
    {
        return $this->encode();
    }

    final public function __serialize(): array
    {
        return ['token' => $this->encode()];
    }

    final public function __unserialize(array $data): void
    {
        if (!isset($data['token']) || !is_string($data['token'])) {
            throw new RuntimeException('Invalid serialization data');
        }

        $decoded = static::decode($data['token']);

        $this->type = $decoded->type;
        $this->identity = $decoded->identity;
        $this->expiredAt = $decoded->expiredAt;
        $this->nonce = $decoded->nonce;
        $this->signature = $decoded->signature;
    }

    private function __clone(): void
    {
    }
}
