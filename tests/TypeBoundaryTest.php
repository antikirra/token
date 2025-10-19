<?php

declare(strict_types=1);

use Tests\Fixtures\NegativeTypeToken;
use Tests\Fixtures\ZeroTypeToken;
use Tests\Fixtures\MinValidTypeToken;
use Tests\Fixtures\MaxValidTypeToken;
use Tests\Fixtures\OverflowTypeToken;
use Tests\Fixtures\HighOverflowTypeToken;

beforeEach(function () {
    $this->expiredAt = (new DateTimeImmutable())->modify('+1 hour');
    $this->identity = 12345;
});

describe('Type Boundary Values - Invalid Cases', function () {
    test('throws exception when creating token with negative type (-1)', function () {
        NegativeTypeToken::create($this->identity, $this->expiredAt);
    })->throws(RuntimeException::class, '$type < 1 || $type > 255');

    test('throws exception when creating token with zero type (0)', function () {
        ZeroTypeToken::create($this->identity, $this->expiredAt);
    })->throws(RuntimeException::class, '$type < 1 || $type > 255');

    test('throws exception when creating token with overflow type (256)', function () {
        OverflowTypeToken::create($this->identity, $this->expiredAt);
    })->throws(RuntimeException::class, '$type < 1 || $type > 255');

    test('throws exception when creating token with high overflow type (1000)', function () {
        HighOverflowTypeToken::create($this->identity, $this->expiredAt);
    })->throws(RuntimeException::class, '$type < 1 || $type > 255');

    test('throws exception when creating token with custom negative type', function () {
        MinValidTypeToken::create($this->identity, $this->expiredAt, -5);
    })->throws(RuntimeException::class, '$type < 1 || $type > 255');

    test('throws exception when creating token with custom zero type', function () {
        MinValidTypeToken::create($this->identity, $this->expiredAt, 0);
    })->throws(RuntimeException::class, '$type < 1 || $type > 255');

    test('throws exception when creating token with custom overflow type', function () {
        MinValidTypeToken::create($this->identity, $this->expiredAt, 256);
    })->throws(RuntimeException::class, '$type < 1 || $type > 255');

    test('throws exception when creating token with custom high overflow type', function () {
        MinValidTypeToken::create($this->identity, $this->expiredAt, 500);
    })->throws(RuntimeException::class, '$type < 1 || $type > 255');
});

describe('Type Boundary Values - Valid Cases', function () {
    test('can create token with minimum valid type (1)', function () {
        $token = MinValidTypeToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(MinValidTypeToken::class)
            ->and($token->typeOf(1))->toBeTrue()
            ->and($token->typeOf(2))->toBeFalse();
    });

    test('can create token with maximum valid type (255)', function () {
        $token = MaxValidTypeToken::create($this->identity, $this->expiredAt);

        expect($token)->toBeInstanceOf(MaxValidTypeToken::class)
            ->and($token->typeOf(255))->toBeTrue()
            ->and($token->typeOf(254))->toBeFalse();
    });

    test('can create token with custom minimum valid type (1)', function () {
        $token = MinValidTypeToken::create($this->identity, $this->expiredAt, 1);

        expect($token->typeOf(1))->toBeTrue();
    });

    test('can create token with custom maximum valid type (255)', function () {
        $token = MaxValidTypeToken::create($this->identity, $this->expiredAt, 255);

        expect($token->typeOf(255))->toBeTrue();
    });

    test('can create token with custom middle range type (128)', function () {
        $token = MinValidTypeToken::create($this->identity, $this->expiredAt, 128);

        expect($token->typeOf(128))->toBeTrue()
            ->and($token->typeOf(127))->toBeFalse()
            ->and($token->typeOf(129))->toBeFalse();
    });
});

describe('Type Boundary Values - Encode/Decode', function () {
    test('can encode and decode token with minimum valid type (1)', function () {
        $token = MinValidTypeToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        expect($encoded)->toBeString()->not->toBeEmpty();

        $decoded = MinValidTypeToken::decode($encoded);

        expect($decoded)->toBeInstanceOf(MinValidTypeToken::class)
            ->and($decoded->typeOf(1))->toBeTrue()
            ->and($decoded->getIdentity())->toBe($this->identity);
    });

    test('can encode and decode token with maximum valid type (255)', function () {
        $token = MaxValidTypeToken::create($this->identity, $this->expiredAt);
        $encoded = $token->encode();

        expect($encoded)->toBeString()->not->toBeEmpty();

        $decoded = MaxValidTypeToken::decode($encoded);

        expect($decoded)->toBeInstanceOf(MaxValidTypeToken::class)
            ->and($decoded->typeOf(255))->toBeTrue()
            ->and($decoded->getIdentity())->toBe($this->identity);
    });

    test('can encode and decode token with custom type at boundaries', function () {
        // Test type = 1
        $token1 = MinValidTypeToken::create($this->identity, $this->expiredAt, 1);
        $decoded1 = MinValidTypeToken::decode($token1->encode());
        expect($decoded1->typeOf(1))->toBeTrue();

        // Test type = 255
        $token255 = MaxValidTypeToken::create($this->identity, $this->expiredAt, 255);
        $decoded255 = MaxValidTypeToken::decode($token255->encode());
        expect($decoded255->typeOf(255))->toBeTrue();

        // Test type = 127 (middle of range)
        $token127 = MinValidTypeToken::create($this->identity, $this->expiredAt, 127);
        $decoded127 = MinValidTypeToken::decode($token127->encode());
        expect($decoded127->typeOf(127))->toBeTrue();
    });
});

describe('Type Boundary Values - TypeOf Method', function () {
    test('typeOf correctly identifies minimum valid type (1)', function () {
        $token = MinValidTypeToken::create($this->identity, $this->expiredAt);

        expect($token->typeOf(1))->toBeTrue()
            ->and($token->typeOf(0))->toBeFalse()
            ->and($token->typeOf(2))->toBeFalse();
    });

    test('typeOf correctly identifies maximum valid type (255)', function () {
        $token = MaxValidTypeToken::create($this->identity, $this->expiredAt);

        expect($token->typeOf(255))->toBeTrue()
            ->and($token->typeOf(254))->toBeFalse()
            ->and($token->typeOf(256))->toBeFalse();
    });

    test('typeOf works correctly across full valid range', function () {
        foreach ([1, 10, 50, 100, 150, 200, 254, 255] as $type) {
            $token = MinValidTypeToken::create($this->identity, $this->expiredAt, $type);

            expect($token->typeOf($type))->toBeTrue()
                ->and($token->typeOf($type - 1))->toBeFalse()
                ->and($token->typeOf($type + 1))->toBeFalse();
        }
    });
});
