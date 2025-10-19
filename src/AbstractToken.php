<?php

declare(strict_types=1);

namespace Antikirra;

use DateTimeImmutable;
use DateTimeInterface;
use RuntimeException;

abstract class AbstractToken
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

        if (!in_array(static::algorithm(), hash_algos(), true)) {
            throw new RuntimeException('algorithm is not supported');
        }

        if ($type < 1 || $type > 255) {
            throw new RuntimeException('$type < 1 || $type > 255');
        }

        if ($identity < 1 || $identity > 18446744073709551615) {
            throw new RuntimeException('$identity < 1 || $identity > 18446744073709551615');
        }

        if ($nonce < 268435456 || $nonce > 4294967295) {
            throw new RuntimeException('$nonce < 268435456 || $nonce > 4294967295');
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

    final public static function create(int $identity, DateTimeInterface $expiredAt, ?int $type = null): static
    {
        $expiredAt = DateTimeImmutable::createFromInterface($expiredAt);

        $type ??= static::type();
        $nonce = random_int(268435456, 4294967295);
        $sign = static::sign($type, $identity, $expiredAt->getTimestamp(), $nonce);

        return new static($type, $identity, $expiredAt, $nonce, $sign);
    }

    final protected static function sign(int $type, int $identity, int $expiredAt, int $nonce): string
    {
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
        $data = unpack('vtype/Pidentity/VexpiredAt/Vnonce/a*sign', base64url_decode($raw));

        if (false === $data) {
            throw new RuntimeException('false === $data');
        }

        if (!is_array($data)) {
            throw new RuntimeException('!is_array($data)');
        }

        if (!isset($data['type'])) {
            throw new RuntimeException('!isset($data[\'type\'])');
        }

        if (!is_int($data['type'])) {
            throw new RuntimeException('!is_int($data[\'type\'])');
        }

        if ($data['type'] < 1 || $data['type'] > 255) {
            throw new RuntimeException('$data[\'type\'] < 1 || $data[\'type\'] > 255');
        }

        if (!isset($data['identity'])) {
            throw new RuntimeException('!isset($data[\'identity\'])');
        }

        if (!is_int($data['identity']) && !is_float($data['identity'])) {
            throw new RuntimeException('!is_int($data[\'identity\']) && !is_float($data[\'identity\'])');
        }

        if ($data['identity'] < 1 || $data['identity'] > 18446744073709551615) {
            throw new RuntimeException('$data[\'identity\'] < 1 || $data[\'identity\'] > 18446744073709551615');
        }

        if (!isset($data['expiredAt'])) {
            throw new RuntimeException('!isset($data[\'expiredAt\'])');
        }

        if (!is_int($data['expiredAt'])) {
            throw new RuntimeException('!is_int($data[\'expiredAt\'])');
        }

        if ($data['expiredAt'] < 0 || $data['expiredAt'] > 4294967295) {
            throw new RuntimeException('$data[\'expiredAt\'] < 0 || $data[\'expiredAt\'] > 4294967295');
        }

        if (!isset($data['nonce'])) {
            throw new RuntimeException('!isset($data[\'nonce\'])');
        }

        if (!is_int($data['nonce'])) {
            throw new RuntimeException('!is_int($data[\'nonce\'])');
        }

        if ($data['nonce'] < 268435456 || $data['nonce'] > 4294967295) {
            throw new RuntimeException('$data[\'nonce\'] < 268435456 || $data[\'nonce\'] > 4294967295');
        }

        if (!isset($data['sign'])) {
            throw new RuntimeException('!isset($data[\'sign\'])');
        }

        if (!is_string($data['sign'])) {
            throw new RuntimeException('!is_string($data[\'sign\'])');
        }

        $sign = static::sign($data['type'], $data['identity'], $data['expiredAt'], $data['nonce']);

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
