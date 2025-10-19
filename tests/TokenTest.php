<?php

declare(strict_types=1);

use Tests\Fixtures\TestToken;

beforeEach(function () {
    $this->expiredAt = (new DateTimeImmutable())->modify('+1 hour');
    $this->identity = 12345;
});

test('can create token', function () {
    $token = TestToken::create($this->identity, $this->expiredAt);

    expect($token)->toBeInstanceOf(TestToken::class)
        ->and($token->getIdentity())->toBe($this->identity)
        ->and($token->getExpiredAt()->getTimestamp())->toBe($this->expiredAt->getTimestamp())
        ->and($token->typeOf(1))->toBeTrue();
});

test('can encode and decode token', function () {
    $token = TestToken::create($this->identity, $this->expiredAt);
    $encoded = $token->encode();

    expect($encoded)->toBeString()
        ->and($encoded)->not->toBeEmpty();

    $decoded = TestToken::decode($encoded);

    expect($decoded)->toBeInstanceOf(TestToken::class)
        ->and($decoded->getIdentity())->toBe($token->getIdentity())
        ->and($decoded->getExpiredAt()->getTimestamp())->toBe($token->getExpiredAt()->getTimestamp());
});

test('can convert token to string', function () {
    $token = TestToken::create($this->identity, $this->expiredAt);
    $string = (string) $token;

    expect($string)->toBeString()
        ->and($string)->toBe($token->encode());
});

test('can check if token is expired', function () {
    $futureExpiration = (new DateTimeImmutable())->modify('+1 hour');
    $token = TestToken::create($this->identity, $futureExpiration);

    expect($token->isExpired())->toBeFalse();
});

test('detects expired token', function () {
    $pastExpiration = (new DateTimeImmutable())->modify('-1 hour');
    $token = TestToken::create($this->identity, $pastExpiration);

    expect($token->isExpired())->toBeTrue();
});

test('can serialize and unserialize token', function () {
    $token = TestToken::create($this->identity, $this->expiredAt);
    $serialized = serialize($token);

    expect($serialized)->toBeString();

    $unserialized = unserialize($serialized);

    expect($unserialized)->toBeInstanceOf(TestToken::class)
        ->and($unserialized->getIdentity())->toBe($token->getIdentity())
        ->and($unserialized->getExpiredAt()->getTimestamp())->toBe($token->getExpiredAt()->getTimestamp());
});

test('throws exception for invalid identity min', function () {
    TestToken::create(0, $this->expiredAt);
})->throws(RuntimeException::class, '$identity < 1 || $identity > PHP_INT_MAX');

test('throws exception for invalid identity max', function () {
    TestToken::create(PHP_INT_MAX + 1, $this->expiredAt);
})->throws(RuntimeException::class, '$identity < 1 || $identity > PHP_INT_MAX');

test('throws exception for invalid type min', function () {
    TestToken::create($this->identity, $this->expiredAt, 0);
})->throws(RuntimeException::class, '$type < 1 || $type > 255');

test('throws exception for invalid type max', function () {
    TestToken::create($this->identity, $this->expiredAt, 256);
})->throws(RuntimeException::class, '$type < 1 || $type > 255');

test('throws exception for tampered signature', function () {
    $token = TestToken::create($this->identity, $this->expiredAt);
    $encoded = $token->encode();

    // Tamper with the encoded token
    $tampered = substr($encoded, 0, -5) . 'XXXXX';

    TestToken::decode($tampered);
})->throws(RuntimeException::class);

test('typeOf returns correct result', function () {
    $token = TestToken::create($this->identity, $this->expiredAt);

    expect($token->typeOf(1))->toBeTrue()
        ->and($token->typeOf(2))->toBeFalse();
});

test('can create token with custom type', function () {
    $customType = 42;
    $token = TestToken::create($this->identity, $this->expiredAt, $customType);

    expect($token->typeOf($customType))->toBeTrue()
        ->and($token->typeOf(1))->toBeFalse();
});