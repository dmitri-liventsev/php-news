<?php

namespace App\News\Domain\Service;

use App\News\Domain\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function generateToken(User $user): string
    {
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600,
            'username' => $user->getUsername(),
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function decodeToken(string $token)
    {
        try {
            return JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }
}
