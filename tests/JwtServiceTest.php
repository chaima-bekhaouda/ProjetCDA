<?php

use PHPUnit\Framework\TestCase;
use App\Services\JwtService;

class JwtServiceTest extends TestCase
{
    public function testCreateAndDecodeToken(): void
    {
        $user = [
            'id' => 'user-123',
            'email' => 'test@example.com',
            'display_name' => 'Test User',
        ];

        $token = JwtService::createToken($user, 60);
        $this->assertIsString($token);

        $payload = JwtService::decodeToken($token);
        $this->assertIsArray($payload);
        $this->assertSame('user-123', $payload['sub']);
        $this->assertSame('test@example.com', $payload['email']);
        $this->assertSame('Test User', $payload['display_name']);
    }

    public function testDecodeInvalidTokenReturnsNull(): void
    {
        $payload = JwtService::decodeToken('invalid.token.value');
        $this->assertNull($payload);
    }
}
