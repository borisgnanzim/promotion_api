<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Cookie;

class TokenService
{
    /**
     * Create an authentication token for a user with their roles
     *
     * @param User $user
     * @return array{token: string, cookie: Cookie}
     */
    public function createToken(User $user): array
    {
        $roleName = $this->getRoleName($user);

        $token = $user->createToken('auth_token', [$roleName])->plainTextToken;
        $cookie = cookie('auth_token', $token, 1440, null, null, false, true);

        return [
            'token' => $token,
            'role' => $roleName,
            'cookie' => $cookie,
        ];
    }

    /**
     * Determine the user's primary role name
     *
     * @param User $user
     * @return string
     */
    private function getRoleName(User $user): string
    {
        if ($user->hasRole('admin')) {
            return 'admin';
        }

        if ($user->hasRole('seller')) {
            return 'seller';
        }

        if ($user->hasRole('client')) {
            return 'client';
        }

        return 'guest';
    }

    /**
     * Forget the auth token cookie
     *
     * @return Cookie
     */
    public function forgetToken(): Cookie
    {
        return Cookie::forget('auth_token');
    }
}
