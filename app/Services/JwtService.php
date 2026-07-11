<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

class JwtService
{
    // service JWT pour l'authentification stateless
    private const DEFAULT_TTL = 86400;
    private const DEFAULT_SECRET = 'booknest-development-secret-key-2026-very-long';

    public static function createToken(array $user, int $ttl = self::DEFAULT_TTL): string
    {
        $secret = self::getSecret();
        $issuedAt = time();

        $payload = [
            'iss' => 'booknest',
            'sub' => (string) ($user['id'] ?? ''),
            'email' => $user['email'] ?? '',
            'display_name' => $user['display_name'] ?? '',
            'iat' => $issuedAt,
            'nbf' => $issuedAt,
            'exp' => $issuedAt + $ttl,
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }

    public static function decodeToken(string $token): ?array
    {
        $secret = self::getSecret();

        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            return (array) $decoded;
        } catch (ExpiredException | SignatureInvalidException | BeforeValidException | \UnexpectedValueException | \DomainException) {
            return null;
        }
    }

    private static function getSecret(): string
    {
        return $_ENV['JWT_SECRET'] ?? self::DEFAULT_SECRET;
    }
}
