<?php

namespace App\Security;

final class JWTToken
{
    private const ALGORITHM = 'HS256';
    private const JWT = 'JWT';

    public function generateToken(array $payload, string $secret, int $validity): string
    {
        $now = time();
        $payload['creationTime'] = $now;

        if ($validity > 0) {
            $payload['expireTime'] = $now + $validity * 3600;
        }

        $headerAndPayload64 = $this->base64UrlEncode(json_encode(['typ' => self::JWT, 'alg' => self::ALGORITHM])) . '.' .
            $this->base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', $headerAndPayload64, $secret, true);
        $signature = $this->base64UrlEncode($signature);

        return $headerAndPayload64 . '.' . $signature;
    }

    public function isValid(string $token): bool
    {
        return preg_match('/^[a-zA-Z\d\-_=]+\.[a-zA-Z\d\-_=]+\.[a-zA-Z\d\-_=]+$/', $token) === 1;
    }

    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);
        return isset($payload['expireTime']) && $payload['expireTime'] < time();
    }

    public function check(string $token, string $secret): bool
    {
        if (!$this->isValid($token)) {
            return false;
        }

        $payload = $this->getPayload($token);
        $verifToken = $this->generateToken($payload, $secret, 0);

        return $token === $verifToken;
    }

    private function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    public function getPayload(string $token): array
    {
        $parts = explode('.', $token);
        return json_decode(base64_decode($parts[1]), true);
    }
}