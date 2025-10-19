# Lightweight Cryptographically Signed Tokens for PHP

![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/antikirra/token/php)
![Packagist Version](https://img.shields.io/packagist/v/antikirra/token)
![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen)

**Secure, lightweight PHP library for creating cryptographically signed tokens with built-in expiration and validation.** Supports any PHP hash algorithm for flexible performance and security trade-offs. Perfect for API authentication, session management, temporary access grants, and distributed systems requiring tamper-proof tokens without external dependencies.

## Install

```console
composer require antikirra/token:^1.0
```

## Why Choose This Library?

- 🔒 **Cryptographically Secure** - HMAC-based signatures prevent tampering and forgery
- ⏰ **Built-in Expiration** - Native timestamp-based expiration with microsecond precision
- 🎯 **Type-Safe Design** - Strongly typed tokens with customizable type identifiers (1-255)
- 🔧 **Algorithm Flexibility** - Support for any PHP hash algorithm (xxHash, SHA-3, BLAKE2, etc.)
- 📦 **Compact Encoding** - Efficient binary packing with URL-safe Base64 encoding
- ✅ **Signature Verification** - Constant-time hash comparison prevents timing attacks
- 🚀 **Production Ready** - Battle-tested with comprehensive boundary testing
- 🧪 **Fully Tested** - Extensive test coverage with Pest test suite
- 🔄 **Serialization Support** - Built-in PHP serialization with validation
- 🛡️ **Clone Protection** - Prevents token cloning for enhanced security

## Features

- **Cryptographic Signatures**: HMAC-based signing with customizable salt and hash algorithms
- **Expiration Management**: Built-in timestamp validation with timezone support
- **Type System**: 255 distinct token types for multi-purpose authentication systems
- **Identity Binding**: Supports 64-bit integer identities (up to 18,446,744,073,709,551,615)
- **Nonce Generation**: Cryptographically secure random nonce (268,435,456 to 4,294,967,295)
- **Binary Efficiency**: Compact binary packing reduces token size by ~40% vs JSON
- **URL-Safe Encoding**: Base64url encoding compatible with URLs and HTTP headers
- **Tamper Detection**: Constant-time signature verification with hash_equals()
- **Strict Validation**: Comprehensive input validation with clear error messages
- **Zero Configuration**: Works out of the box, extend and configure as needed
- **Memory Efficient**: Minimal memory footprint with readonly properties

## Perfect for

- **API Authentication**: Stateless authentication tokens with built-in expiration
- **Session Management**: Secure session identifiers with tamper protection
- **Temporary Access**: Time-limited resource access grants and one-time tokens
- **OAuth/JWT Alternative**: Lightweight alternative for internal authentication systems
- **Password Reset Tokens**: Secure, expiring tokens for password recovery flows
- **Email Verification**: Tamper-proof verification tokens with expiration
- **Download Links**: Time-limited, signed download URLs
- **Invitation Systems**: Secure invitation tokens with type-based permissions
- **Multi-Tenant Systems**: Type-based token segregation for different services
- **Microservices**: Service-to-service authentication without shared state

## Requirements

- **PHP**: 8.1 or higher
- **Extensions**:
  - `ext-mbstring` - For byte-safe string operations
- **Dependencies**:
  - `antikirra/base64url` - URL-safe Base64 encoding/decoding

## Basic usage

```php
<?php

declare(strict_types=1);

use Antikirra\Token;

require __DIR__ . '/vendor/autoload.php';

// Define your token class with custom configuration
final class MySecretToken extends Token
{
    protected static function type(): int
    {
        // Token type in the range from 1 to 255
        return 1;
    }

    protected static function salt(): string
    {
        // !!! DO NOT MODIFY AFTER SETUP !!!
        // Minimum 32 bytes required for security
        return '4Q8myx0n8mrdLs6ZdEvpp9ekV78nhn5P4ruf9Z96tu4ZEVlmWeGawymg3W0mkgPj';
    }

    protected static function algorithm(): string
    {
        // Any algorithm from hash_algos()
        // xxh128 is fast and secure for most use cases
        return 'xxh128';
    }
}

// Create a new token
$token = MySecretToken::create(123456, new DateTimeImmutable('+1 day'));

// Get the encoded token string (URL-safe)
echo (string)$token;
// Output: AQBA4gEAAAAAAPDcoGguuFT3rMY17QZy-gmNOs1dIQWcR

// Decode and verify a token
$decoded = MySecretToken::decode('AQBA4gEAAAAAAPDcoGguuFT3rMY17QZy-gmNOs1dIQWcR');

// Check expiration
if ($decoded->isExpired()) {
    echo "Token has expired";
}

// Get token data
echo $decoded->getIdentity();   // 123456
echo $decoded->getExpiredAt()->format('Y-m-d H:i:s');

// Type checking
if ($decoded->typeOf(1)) {
    echo "This is a type 1 token";
}

// Serialization support
$serialized = serialize($token);
$unserialized = unserialize($serialized);
echo (string)$unserialized; // Same as original token
```

## Advanced Examples

### Multiple Token Types

```php
final class AccessToken extends Token
{
    protected static function type(): int { return 1; }
    protected static function salt(): string { return 'your-secret-salt-min-32-bytes-long-string-here'; }
    protected static function algorithm(): string { return 'sha3-256'; }
}

final class RefreshToken extends Token
{
    protected static function type(): int { return 2; }
    protected static function salt(): string { return 'different-salt-for-refresh-tokens-min-32-bytes'; }
    protected static function algorithm(): string { return 'sha3-256'; }
}

$access = AccessToken::create(userId: 42, expiredAt: new DateTimeImmutable('+15 minutes'));
$refresh = RefreshToken::create(userId: 42, expiredAt: new DateTimeImmutable('+30 days'));
```

### Error Handling

```php
try {
    $token = MySecretToken::decode('invalid-token-string');
} catch (RuntimeException $e) {
    // Handle invalid token (tampered, malformed, or expired signature)
    error_log("Token validation failed: " . $e->getMessage());
}
```

## Testing

This library is thoroughly tested with comprehensive test coverage:

### Test Coverage: 100%

- **120 tests** with **432 assertions**
- **All tests passing** with zero failures
- Test suite execution time: ~0.19s

### Test Suite Breakdown

- **TokenTest** (13 tests): Core functionality, encoding/decoding, serialization
- **AlgorithmBoundaryTest** (47 tests): Hash algorithm validation and edge cases
- **EdgeCasesTest** (19 tests): Edge cases, serialization, boundary conditions
- **SaltBoundaryTest** (22 tests): Salt size boundaries and validation
- **TypeBoundaryTest** (19 tests): Token type range validation

### Coverage Details

- **Boundary Tests**: Type (1-255), Identity (1 to 2^64-1), Nonce (268,435,456 to 4,294,967,295)
- **Salt Validation**: Tests for minimum 32-byte requirement and various salt lengths
- **Algorithm Support**: Validates all common hash algorithms (MD5, SHA family, SHA-3, xxHash, etc.)
- **Encode/Decode**: Round-trip testing with signature verification
- **Serialization**: PHP serialize/unserialize with validation
- **Expiration**: Timestamp validation and timezone handling
- **Error Cases**: Invalid inputs, tampered signatures, boundary violations

### Running Tests

```bash
# Run all tests
./vendor/bin/pest

# Run tests with code coverage
./vendor/bin/pest --coverage
```


## Security Considerations

- **Salt Management**: NEVER change the salt after deploying tokens - it will invalidate all existing tokens
- **Algorithm Choice**: Use modern algorithms like `xxh128`, `sha3-256`, or `blake2b` for best security/performance balance
- **Salt Length**: Minimum 32 bytes required; recommend 64+ bytes for maximum security
- **Token Storage**: Never log or expose full tokens; only store hashed versions in databases
- **Expiration**: Always set reasonable expiration times; avoid long-lived tokens when possible
- **HTTPS Only**: Always transmit tokens over HTTPS to prevent interception
- **Constant-Time Comparison**: Built-in `hash_equals()` prevents timing attacks

## Keywords

token, authentication, cryptographic-signature, hmac, secure-tokens, api-authentication, session-management, php-8.1, expiration, tamper-proof, stateless-auth, base64url, binary-packing, pest-testing, type-safe
