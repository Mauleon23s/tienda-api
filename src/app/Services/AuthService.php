<?php

namespace App\Services;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthService
{
    public function login(array $credentials): string
    {
        if (!$token = auth('api')->attempt($credentials)) {
            throw new HttpException(401, 'Invalid credentials');
        }

        return $token;
    }

    public function logout(): void
    {
        auth('api')->logout();
    }

    public function refresh(): string
    {
        return auth('api')->refresh();
    }

    public function me()
    {
        return auth('api')->user();
    }
}