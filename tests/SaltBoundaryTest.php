<?php

declare(strict_types=1);

use Tests\Fixtures\EmptySaltToken;
use Tests\Fixtures\Salt16BytesToken;
use Tests\Fixtures\Salt31BytesToken;
use Tests\Fixtures\Salt32BytesToken;
use Tests\Fixtures\Salt1024BytesToken;

beforeEach(function () {
    $this->expiredAt = (new DateTimeImmutable())->modify('+1 hour');
    $this->identity = 12345;
});

describe('Salt Boundary Values - Invalid Cases', function () {
    test('throws exception when creating token with empty salt ("")', function () {
        EmptySaltToken::create($this->identity, $this->expiredAt);
    })->throws(RuntimeException::class, 'salt cannot be less than 32 bytes');

    test('throws exception when creating token with 16 bytes salt', function () {
        Salt16BytesToken::create($this->identity, $this->expiredAt);
    })->throws(RuntimeException::class, 'salt cannot be less than 32 bytes');

    test('throws exception when creating token with 31 bytes salt', function () {
        Salt31BytesToken::create($this->identity, $this->expiredAt);
    })->throws(RuntimeException::class, 'salt cannot be less than 32 bytes');

    test('throws exception when encoding empty salt token', function () {
        $token = EmptySaltToken::create($this->identity, $this->expiredAt);
        $token->encode();
    })->throws(RuntimeException::class, 'salt cannot be less than 32 bytes');

    test('throws exception when encoding 16 bytes salt token', function () {
        $token = Salt16BytesToken::create($this->identity, $this->expiredAt);
        $token->encode();
    })->throws(RuntimeException::class, 'salt cannot be less than 32 bytes');

    test('throws exception when encoding 31 bytes salt token', function () {
        $token = Salt31BytesToken::create($this->identity, $this->expiredAt);
        $token->encode();
    })->throws(RuntimeException::class, 'salt cannot be less than 32 bytes');
});

describe('Salt Boundary Values - Valid Cases', function () {
    test('can create token with minimum valid salt (32 bytes)', function () {
        $token = Salt32BytesToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(Salt32BytesToken::class)
            ->and($token->getIdentity())->toBe($this->identity)
            ->and($token->getExpiredAt()->getTimestamp())->toBe($this->expiredAt->getTimestamp());
    });

    test('can create token with large salt (1024 bytes)', function () {
        $token = Salt1024BytesToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(Salt1024BytesToken::class)
            ->and($token->getIdentity())->toBe($this->identity)
            ->and($token->getExpiredAt()->getTimestamp())->toBe($this->expiredAt->getTimestamp());
    });

    test('can convert 32 bytes salt token to string', function () {
        $token = Salt32BytesToken::create($this->identity, $this->expiredAt);
        $string = (string) $token;

        expect($string)->toBeString()
            ->and($string)->not->toBeEmpty()
            ->and($string)->toBe($token->encode());
    });

    test('can convert 1024 bytes salt token to string', function () {
        $token = Salt1024BytesToken::create($this->identity, $this->expiredAt);
        $string = (string) $token;

        expect($string)->toBeString()
            ->and($string)->not->toBeEmpty()
            ->and($string)->toBe($token->encode());
    });

    test('32 bytes salt token is not expired when created with future date', function () {
        $token = Salt32BytesToken::create($this->identity, $this->expiredAt);

        expect($token->isExpired())->toBeFalse();
    });

    test('1024 bytes salt token is not expired when created with future date', function () {
        $token = Salt1024BytesToken::create($this->identity, $this->expiredAt);

        expect($token->isExpired())->toBeFalse();
    });
});

describe('Salt Boundary Values - Encode/Decode', function () {
    test('can encode and decode token with minimum valid salt (32 bytes)', function () {
        $token = Salt32BytesToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        expect($encoded)->toBeString()->not->toBeEmpty();

        $decoded = Salt32BytesToken::decode($encoded);

        expect($decoded)->toBeInstanceOf(Salt32BytesToken::class)
            ->and($decoded->getIdentity())->toBe($this->identity)
            ->and($decoded->getExpiredAt()->getTimestamp())->toBe($this->expiredAt->getTimestamp());
    });

    test('can encode and decode token with large salt (1024 bytes)', function () {
        $token = Salt1024BytesToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        expect($encoded)->toBeString()->not->toBeEmpty();

        $decoded = Salt1024BytesToken::decode($encoded);

        expect($decoded)->toBeInstanceOf(Salt1024BytesToken::class)
            ->and($decoded->getIdentity())->toBe($this->identity)
            ->and($decoded->getExpiredAt()->getTimestamp())->toBe($this->expiredAt->getTimestamp());
    });

    test('decoded 32 bytes salt token matches original', function () {
        $token = Salt32BytesToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();
        $decoded = Salt32BytesToken::decode($encoded);

        expect($decoded->getIdentity())->toBe($token->getIdentity())
            ->and($decoded->getExpiredAt()->getTimestamp())->toBe($token->getExpiredAt()->getTimestamp())
            ->and($decoded->typeOf(1))->toBeTrue();
    });

    test('decoded 1024 bytes salt token matches original', function () {
        $token = Salt1024BytesToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();
        $decoded = Salt1024BytesToken::decode($encoded);

        expect($decoded->getIdentity())->toBe($token->getIdentity())
            ->and($decoded->getExpiredAt()->getTimestamp())->toBe($token->getExpiredAt()->getTimestamp())
            ->and($decoded->typeOf(1))->toBeTrue();
    });

    test('throws exception for tampered 32 bytes salt token signature', function () {
        $token = Salt32BytesToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        // Tamper with the encoded token
        $tampered = substr($encoded, 0, -5) . 'XXXXX';

        Salt32BytesToken::decode($tampered);
    })->throws(RuntimeException::class);

    test('throws exception for tampered 1024 bytes salt token signature', function () {
        $token = Salt1024BytesToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        // Tamper with the encoded token
        $tampered = substr($encoded, 0, -5) . 'XXXXX';

        Salt1024BytesToken::decode($tampered);
    })->throws(RuntimeException::class);
});

describe('Salt Boundary Values - Serialization', function () {
    test('can serialize and unserialize token with 32 bytes salt', function () {
        $token = Salt32BytesToken::create($this->identity, $this->expiredAt);
        $serialized = serialize($token);

        expect($serialized)->toBeString();

        $unserialized = unserialize($serialized);

        expect($unserialized)->toBeInstanceOf(Salt32BytesToken::class)
            ->and($unserialized->getIdentity())->toBe($token->getIdentity())
            ->and($unserialized->getExpiredAt()->getTimestamp())->toBe($token->getExpiredAt()->getTimestamp());
    });

    test('can serialize and unserialize token with 1024 bytes salt', function () {
        $token = Salt1024BytesToken::create($this->identity, $this->expiredAt);
        $serialized = serialize($token);

        expect($serialized)->toBeString();

        $unserialized = unserialize($serialized);

        expect($unserialized)->toBeInstanceOf(Salt1024BytesToken::class)
            ->and($unserialized->getIdentity())->toBe($token->getIdentity())
            ->and($unserialized->getExpiredAt()->getTimestamp())->toBe($token->getExpiredAt()->getTimestamp());
    });
});

describe('Salt Boundary Values - TypeOf Method', function () {
    test('typeOf works correctly with 32 bytes salt token', function () {
        $token = Salt32BytesToken::create($this->identity, $this->expiredAt);

        expect($token->typeOf(1))->toBeTrue()
            ->and($token->typeOf(2))->toBeFalse();
    });

    test('typeOf works correctly with 1024 bytes salt token', function () {
        $token = Salt1024BytesToken::create($this->identity, $this->expiredAt);

        expect($token->typeOf(1))->toBeTrue()
            ->and($token->typeOf(2))->toBeFalse();
    });
});
