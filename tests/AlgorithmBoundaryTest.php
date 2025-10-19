<?php

declare(strict_types=1);

use Tests\Fixtures\InvalidAlgorithmToken;
use Tests\Fixtures\EmptyAlgorithmToken;
use Tests\Fixtures\NumericAlgorithmToken;
use Tests\Fixtures\SpecialCharsAlgorithmToken;
use Tests\Fixtures\ObsoleteAlgorithmToken;
use Tests\Fixtures\Md5AlgorithmToken;
use Tests\Fixtures\Sha1AlgorithmToken;
use Tests\Fixtures\Sha224AlgorithmToken;
use Tests\Fixtures\Sha512AlgorithmToken;
use Tests\Fixtures\Sha3_256AlgorithmToken;
use Tests\Fixtures\Sha3_512AlgorithmToken;
use Tests\Fixtures\Ripemd160AlgorithmToken;
use Tests\Fixtures\WhirlpoolAlgorithmToken;
use Tests\Fixtures\Tiger192AlgorithmToken;
use Tests\Fixtures\Xxh128AlgorithmToken;
use Tests\Fixtures\Haval256AlgorithmToken;
use Tests\Fixtures\Adler32AlgorithmToken;
use Tests\Fixtures\Crc32AlgorithmToken;
use Tests\Fixtures\ConfigurableAlgorithmToken;

beforeEach(function () {
    $this->expiredAt = (new DateTimeImmutable())->modify('+1 hour');
    $this->identity = 12345;
});

describe('Algorithm Boundary Values - Invalid Cases', function () {
    test('throws exception when creating token with invalid algorithm', function () {
        InvalidAlgorithmToken::create($this->identity, $this->expiredAt);
    })->throws(ValueError::class);

    test('throws exception when creating token with empty algorithm', function () {
        EmptyAlgorithmToken::create($this->identity, $this->expiredAt);
    })->throws(ValueError::class);

    test('throws exception when creating token with numeric algorithm', function () {
        NumericAlgorithmToken::create($this->identity, $this->expiredAt);
    })->throws(ValueError::class);

    test('throws exception when creating token with special chars algorithm', function () {
        SpecialCharsAlgorithmToken::create($this->identity, $this->expiredAt);
    })->throws(ValueError::class);

    test('throws exception when creating token with obsolete algorithm (md6)', function () {
        ObsoleteAlgorithmToken::create($this->identity, $this->expiredAt);
    })->throws(ValueError::class);

    test('throws exception when encoding invalid algorithm token', function () {
        $token = InvalidAlgorithmToken::create($this->identity, $this->expiredAt);
        $token->encode();
    })->throws(ValueError::class);

    test('throws exception when encoding empty algorithm token', function () {
        $token = EmptyAlgorithmToken::create($this->identity, $this->expiredAt);
        $token->encode();
    })->throws(ValueError::class);
});

describe('Algorithm Boundary Values - Valid Cases', function () {
    test('can create token with md5 algorithm', function () {
        $token = Md5AlgorithmToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(Md5AlgorithmToken::class)
            ->and($token->getIdentity())->toBe($this->identity)
            ->and($token->getExpiredAt()->getTimestamp())->toBe($this->expiredAt->getTimestamp());
    });

    test('can create token with sha1 algorithm', function () {
        $token = Sha1AlgorithmToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(Sha1AlgorithmToken::class)
            ->and($token->getIdentity())->toBe($this->identity)
            ->and($token->getExpiredAt()->getTimestamp())->toBe($this->expiredAt->getTimestamp());
    });

    test('can create token with sha224 algorithm', function () {
        $token = Sha224AlgorithmToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(Sha224AlgorithmToken::class)
            ->and($token->getIdentity())->toBe($this->identity);
    });

    test('can create token with sha512 algorithm', function () {
        $token = Sha512AlgorithmToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(Sha512AlgorithmToken::class)
            ->and($token->getIdentity())->toBe($this->identity);
    });

    test('can create token with sha3-256 algorithm', function () {
        $token = Sha3_256AlgorithmToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(Sha3_256AlgorithmToken::class)
            ->and($token->getIdentity())->toBe($this->identity);
    });

    test('can create token with sha3-512 algorithm', function () {
        $token = Sha3_512AlgorithmToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(Sha3_512AlgorithmToken::class)
            ->and($token->getIdentity())->toBe($this->identity);
    });

    test('can create token with ripemd160 algorithm', function () {
        $token = Ripemd160AlgorithmToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(Ripemd160AlgorithmToken::class)
            ->and($token->getIdentity())->toBe($this->identity);
    });

    test('can create token with whirlpool algorithm', function () {
        $token = WhirlpoolAlgorithmToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(WhirlpoolAlgorithmToken::class)
            ->and($token->getIdentity())->toBe($this->identity);
    });

    test('can create token with tiger192,3 algorithm', function () {
        $token = Tiger192AlgorithmToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(Tiger192AlgorithmToken::class)
            ->and($token->getIdentity())->toBe($this->identity);
    });

    test('can create token with xxh128 algorithm', function () {
        $token = Xxh128AlgorithmToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(Xxh128AlgorithmToken::class)
            ->and($token->getIdentity())->toBe($this->identity);
    });

    test('can create token with haval256,5 algorithm', function () {
        $token = Haval256AlgorithmToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(Haval256AlgorithmToken::class)
            ->and($token->getIdentity())->toBe($this->identity);
    });

    test('can create token with adler32 algorithm', function () {
        $token = Adler32AlgorithmToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(Adler32AlgorithmToken::class)
            ->and($token->getIdentity())->toBe($this->identity);
    });

    test('can create token with crc32 algorithm', function () {
        $token = Crc32AlgorithmToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(Crc32AlgorithmToken::class)
            ->and($token->getIdentity())->toBe($this->identity);
    });

    test('all valid algorithm tokens are not expired when created with future date', function () {
        $tokens = [
            Md5AlgorithmToken::create($this->identity, $this->expiredAt),
            Sha1AlgorithmToken::create($this->identity, $this->expiredAt),
            Sha512AlgorithmToken::create($this->identity, $this->expiredAt),
            Sha3_256AlgorithmToken::create($this->identity, $this->expiredAt),
            WhirlpoolAlgorithmToken::create($this->identity, $this->expiredAt),
        ];

        foreach ($tokens as $token) {
            expect($token->isExpired())->toBeFalse();
        }
    });
});

describe('Algorithm Boundary Values - Encode/Decode', function () {
    test('can encode and decode token with md5 algorithm', function () {
        $token = Md5AlgorithmToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        expect($encoded)->toBeString()->not->toBeEmpty();

        $decoded = Md5AlgorithmToken::decode($encoded);

        expect($decoded)->toBeInstanceOf(Md5AlgorithmToken::class)
            ->and($decoded->getIdentity())->toBe($this->identity)
            ->and($decoded->getExpiredAt()->getTimestamp())->toBe($this->expiredAt->getTimestamp());
    });

    test('can encode and decode token with sha1 algorithm', function () {
        $token = Sha1AlgorithmToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        expect($encoded)->toBeString()->not->toBeEmpty();

        $decoded = Sha1AlgorithmToken::decode($encoded);

        expect($decoded)->toBeInstanceOf(Sha1AlgorithmToken::class)
            ->and($decoded->getIdentity())->toBe($this->identity);
    });

    test('can encode and decode token with sha512 algorithm', function () {
        $token = Sha512AlgorithmToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        $decoded = Sha512AlgorithmToken::decode($encoded);

        expect($decoded)->toBeInstanceOf(Sha512AlgorithmToken::class)
            ->and($decoded->getIdentity())->toBe($this->identity);
    });

    test('can encode and decode token with sha3-256 algorithm', function () {
        $token = Sha3_256AlgorithmToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        $decoded = Sha3_256AlgorithmToken::decode($encoded);

        expect($decoded)->toBeInstanceOf(Sha3_256AlgorithmToken::class)
            ->and($decoded->getIdentity())->toBe($this->identity);
    });

    test('can encode and decode token with sha3-512 algorithm', function () {
        $token = Sha3_512AlgorithmToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        $decoded = Sha3_512AlgorithmToken::decode($encoded);

        expect($decoded)->toBeInstanceOf(Sha3_512AlgorithmToken::class)
            ->and($decoded->getIdentity())->toBe($this->identity);
    });

    test('can encode and decode token with ripemd160 algorithm', function () {
        $token = Ripemd160AlgorithmToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        $decoded = Ripemd160AlgorithmToken::decode($encoded);

        expect($decoded)->toBeInstanceOf(Ripemd160AlgorithmToken::class)
            ->and($decoded->getIdentity())->toBe($this->identity);
    });

    test('can encode and decode token with whirlpool algorithm', function () {
        $token = WhirlpoolAlgorithmToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        $decoded = WhirlpoolAlgorithmToken::decode($encoded);

        expect($decoded)->toBeInstanceOf(WhirlpoolAlgorithmToken::class)
            ->and($decoded->getIdentity())->toBe($this->identity);
    });

    test('can encode and decode token with tiger192,3 algorithm', function () {
        $token = Tiger192AlgorithmToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        $decoded = Tiger192AlgorithmToken::decode($encoded);

        expect($decoded)->toBeInstanceOf(Tiger192AlgorithmToken::class)
            ->and($decoded->getIdentity())->toBe($this->identity);
    });

    test('can encode and decode token with xxh128 algorithm', function () {
        $token = Xxh128AlgorithmToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        $decoded = Xxh128AlgorithmToken::decode($encoded);

        expect($decoded)->toBeInstanceOf(Xxh128AlgorithmToken::class)
            ->and($decoded->getIdentity())->toBe($this->identity);
    });

    test('can encode and decode token with haval256,5 algorithm', function () {
        $token = Haval256AlgorithmToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        $decoded = Haval256AlgorithmToken::decode($encoded);

        expect($decoded)->toBeInstanceOf(Haval256AlgorithmToken::class)
            ->and($decoded->getIdentity())->toBe($this->identity);
    });

    test('can encode and decode token with adler32 algorithm', function () {
        $token = Adler32AlgorithmToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        $decoded = Adler32AlgorithmToken::decode($encoded);

        expect($decoded)->toBeInstanceOf(Adler32AlgorithmToken::class)
            ->and($decoded->getIdentity())->toBe($this->identity);
    });

    test('can encode and decode token with crc32 algorithm', function () {
        $token = Crc32AlgorithmToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        $decoded = Crc32AlgorithmToken::decode($encoded);

        expect($decoded)->toBeInstanceOf(Crc32AlgorithmToken::class)
            ->and($decoded->getIdentity())->toBe($this->identity);
    });
});

describe('Algorithm Boundary Values - Signature Validation', function () {
    test('different algorithms produce different signatures', function () {
        $md5Token = Md5AlgorithmToken::create($this->identity, $this->expiredAt);
        $sha1Token = Sha1AlgorithmToken::create($this->identity, $this->expiredAt);
        $sha256Token = Sha512AlgorithmToken::create($this->identity, $this->expiredAt);

        $md5Encoded = $md5Token->encode();
        $sha1Encoded = $sha1Token->encode();
        $sha256Encoded = $sha256Token->encode();

        expect($md5Encoded)->not->toBe($sha1Encoded)
            ->and($sha1Encoded)->not->toBe($sha256Encoded)
            ->and($md5Encoded)->not->toBe($sha256Encoded);
    });

    test('throws exception for tampered md5 token signature', function () {
        $token = Md5AlgorithmToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        $tampered = substr($encoded, 0, -5) . 'XXXXX';

        Md5AlgorithmToken::decode($tampered);
    })->throws(RuntimeException::class);

    test('throws exception for tampered sha1 token signature', function () {
        $token = Sha1AlgorithmToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        $tampered = substr($encoded, 0, -5) . 'XXXXX';

        Sha1AlgorithmToken::decode($tampered);
    })->throws(RuntimeException::class);

    test('throws exception for tampered sha512 token signature', function () {
        $token = Sha512AlgorithmToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        $tampered = substr($encoded, 0, -5) . 'XXXXX';

        Sha512AlgorithmToken::decode($tampered);
    })->throws(RuntimeException::class);

    test('throws exception for tampered whirlpool token signature', function () {
        $token = WhirlpoolAlgorithmToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        $tampered = substr($encoded, 0, -5) . 'XXXXX';

        WhirlpoolAlgorithmToken::decode($tampered);
    })->throws(RuntimeException::class);
});

describe('Algorithm Boundary Values - Dynamic Testing with ConfigurableAlgorithmToken', function () {
    test('can dynamically test multiple algorithms', function () {
        $algorithms = ['md5', 'sha1', 'sha256', 'sha512', 'whirlpool', 'ripemd160'];

        foreach ($algorithms as $algorithm) {
            ConfigurableAlgorithmToken::setTestAlgorithm($algorithm);
            $token = ConfigurableAlgorithmToken::create($this->identity, $this->expiredAt);

            expect($token)->toBeInstanceOf(ConfigurableAlgorithmToken::class)
                ->and($token->getIdentity())->toBe($this->identity)
                ->and($token->isExpired())->toBeFalse();

            $encoded = $token->encode();
            $decoded = ConfigurableAlgorithmToken::decode($encoded);

            expect($decoded->getIdentity())->toBe($this->identity);
        }
    });

    test('throws exception for invalid algorithm set dynamically', function () {
        ConfigurableAlgorithmToken::setTestAlgorithm('invalid_algo');
        ConfigurableAlgorithmToken::create($this->identity, $this->expiredAt);
    })->throws(ValueError::class);

    test('can test all available hash algorithms in PHP 8.1', function () {
        $availableAlgos = hash_algos();

        // Test a subset of common algorithms
        $algorithmsToTest = array_intersect($availableAlgos, [
            'md2', 'md4', 'md5',
            'sha1', 'sha224', 'sha256', 'sha384', 'sha512',
            'ripemd128', 'ripemd160', 'ripemd256', 'ripemd320',
            'whirlpool',
            'tiger128,3', 'tiger160,3', 'tiger192,3',
            'haval128,3', 'haval160,3', 'haval192,3', 'haval224,3', 'haval256,3',
            'sha3-224', 'sha3-256', 'sha3-384', 'sha3-512',
            'adler32', 'crc32', 'crc32b',
            'fnv132', 'fnv1a32', 'fnv164', 'fnv1a64',
            'joaat',
        ]);

        foreach ($algorithmsToTest as $algorithm) {
            ConfigurableAlgorithmToken::setTestAlgorithm($algorithm);
            $token = ConfigurableAlgorithmToken::create($this->identity, $this->expiredAt);

            expect($token)->toBeInstanceOf(ConfigurableAlgorithmToken::class);

            $encoded = $token->encode();
            expect($encoded)->toBeString()->not->toBeEmpty();

            $decoded = ConfigurableAlgorithmToken::decode($encoded);
            expect($decoded->getIdentity())->toBe($this->identity);
        }
    });
});

describe('Algorithm Boundary Values - TypeOf Method', function () {
    test('typeOf works correctly with md5 algorithm token', function () {
        $token = Md5AlgorithmToken::create($this->identity, $this->expiredAt);

        expect($token->typeOf(1))->toBeTrue()
            ->and($token->typeOf(2))->toBeFalse();
    });

    test('typeOf works correctly with sha512 algorithm token', function () {
        $token = Sha512AlgorithmToken::create($this->identity, $this->expiredAt);

        expect($token->typeOf(1))->toBeTrue()
            ->and($token->typeOf(2))->toBeFalse();
    });

    test('typeOf works correctly with whirlpool algorithm token', function () {
        $token = WhirlpoolAlgorithmToken::create($this->identity, $this->expiredAt);

        expect($token->typeOf(1))->toBeTrue()
            ->and($token->typeOf(2))->toBeFalse();
    });
});

describe('Algorithm Boundary Values - Serialization', function () {
    test('can serialize and unserialize token with md5 algorithm', function () {
        $token = Md5AlgorithmToken::create($this->identity, $this->expiredAt);
        $serialized = serialize($token);

        expect($serialized)->toBeString();

        $unserialized = unserialize($serialized);

        expect($unserialized)->toBeInstanceOf(Md5AlgorithmToken::class)
            ->and($unserialized->getIdentity())->toBe($token->getIdentity())
            ->and($unserialized->getExpiredAt()->getTimestamp())->toBe($token->getExpiredAt()->getTimestamp());
    });

    test('can serialize and unserialize token with sha512 algorithm', function () {
        $token = Sha512AlgorithmToken::create($this->identity, $this->expiredAt);
        $serialized = serialize($token);

        expect($serialized)->toBeString();

        $unserialized = unserialize($serialized);

        expect($unserialized)->toBeInstanceOf(Sha512AlgorithmToken::class)
            ->and($unserialized->getIdentity())->toBe($token->getIdentity())
            ->and($unserialized->getExpiredAt()->getTimestamp())->toBe($token->getExpiredAt()->getTimestamp());
    });

    test('can serialize and unserialize token with whirlpool algorithm', function () {
        $token = WhirlpoolAlgorithmToken::create($this->identity, $this->expiredAt);
        $serialized = serialize($token);

        expect($serialized)->toBeString();

        $unserialized = unserialize($serialized);

        expect($unserialized)->toBeInstanceOf(WhirlpoolAlgorithmToken::class)
            ->and($unserialized->getIdentity())->toBe($token->getIdentity())
            ->and($unserialized->getExpiredAt()->getTimestamp())->toBe($token->getExpiredAt()->getTimestamp());
    });
});
