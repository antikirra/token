<?php

declare(strict_types=1);

namespace Antikirra;

use DateTimeImmutable;
use RuntimeException;

abstract class AbstractToken
{
    private const DEFAULT_TYPE = 1;

    protected static function definedType(): int
    {
        return self::DEFAULT_TYPE;
    }

    abstract protected static function definedSalt(): string;

    final public function __construct(
        protected readonly int               $type,
        public readonly int|float            $userId,
        protected readonly DateTimeImmutable $expiredAt,
        protected readonly int               $nonce,
        protected readonly string            $signature
    )
    {
    }

    final public function isType(int $type): bool
    {
        return $this->type === $type;
    }

    final public function isExpired(): bool
    {
        return $this->expiredAt->getTimestamp() < time();
    }

    final public static function create(int $userId, DateTimeImmutable $expiredAt, ?int $type = null): static
    {
        $type ??= static::definedType();
        $nonce = random_int(268435456, 4294967295);
        $sign = self::sign($type, $userId, $expiredAt->getTimestamp(), $nonce);
        return new static($type, $userId, $expiredAt, $nonce, $sign);
    }

    private static function sign(int $type, int $userId, int $expiredAt, int $nonce): string
    {
        return hash('xxh128', static::definedSalt() . ">{$nonce}%{$expiredAt}#{$userId}%{$type}<", true);
    }

    final public function encode(): string
    {
        $expiredAt = $this->expiredAt->getTimestamp();
        $packed = pack('vPVVa*', $this->type, $this->userId, $expiredAt, $this->nonce, $this->signature);
        return base64url_encode($packed);
    }

    final public static function decode(string $raw): static
    {
        $data = unpack('vtype/PuserId/VexpiredAt/Vnonce/a*sign', base64url_decode($raw));

        if (false === $data) {
            throw new RuntimeException();
        }

        if (!is_array($data)) {
            throw new RuntimeException();
        }

        if (!isset($data['type'])) {
            throw new RuntimeException();
        }

        if (!is_int($data['type'])) {
            throw new RuntimeException();
        }

        if ($data['type'] < 0 || $data['type'] > 255) {
            throw new RuntimeException('>' . $data['type'] . "<");
        }

        if (!isset($data['userId'])) {
            throw new RuntimeException();
        }

        if (!is_int($data['userId']) && !is_double($data['userId'])) {
            throw new RuntimeException();
        }

        if ($data['userId'] < 0 || $data['userId'] > 18446744073709551615) {
            throw new RuntimeException();
        }

        if (!isset($data['expiredAt'])) {
            throw new RuntimeException();
        }

        if (!is_int($data['expiredAt'])) {
            throw new RuntimeException();
        }

        if ($data['expiredAt'] < 0 || $data['expiredAt'] > 4294967295) {
            throw new RuntimeException();
        }

        if (!isset($data['nonce'])) {
            throw new RuntimeException();
        }

        if (!is_int($data['nonce'])) {
            throw new RuntimeException();
        }

        if ($data['nonce'] < 268435456 || $data['nonce'] > 4294967295) {
            throw new RuntimeException();
        }

        if (!isset($data['sign'])) {
            throw new RuntimeException();
        }

        if (!is_string($data['sign'])) {
            throw new RuntimeException();
        }

        if (mb_strlen($data['sign'], '8bit') !== 16) {
            throw new RuntimeException();
        }

        $sign = self::sign($data['type'], $data['userId'], $data['expiredAt'], $data['nonce']);

        if (!hash_equals($sign, $data['sign'])) {
            throw new RuntimeException();
        }

        $expiredAt = DateTimeImmutable::createFromFormat('U', (string)$data['expiredAt']);

        return new static($data['type'], $data['userId'], $expiredAt, $data['nonce'], $data['sign']);
    }

    final public function __toString(): string
    {
        return $this->encode();
    }
}
