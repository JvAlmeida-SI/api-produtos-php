<?php

namespace App\Services\Auth;

use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class JwtService
{
    public function generateToken($user)
    {
        return JWTAuth::fromUser($user);
    }

    public function invalidateToken()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function refreshToken()
    {
        return JWTAuth::refresh(JWTAuth::getToken());
    }

    public function getAuthenticatedUser()
    {
        return JWTAuth::parseToken()->authenticate();
    }
}