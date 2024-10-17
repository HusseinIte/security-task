<?php

namespace App\Services;

use App\Exceptions\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class AuthService
 * @package App\Services
 */
class AuthService
{
    /**
     * @param Request $request
     * @return array
     * @throws AuthenticationException
     */
    public function login(array $credentials)
    {
        if (!$token = Auth::attempt($credentials)) {
            Log::error("Unauthenticated.");
            throw new AuthenticationException("Unauthenticated.");
        }
        $success = $this->respondWithToken($token);
        return $success;
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function profile()
    {
        $success = Auth::user();
        return $success;
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::logout();
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\Response
     */
    public function refresh()
    {
        $success = $this->respondWithToken(Auth::refresh());
        return $success;
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return array
     */
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ];
    }
}
