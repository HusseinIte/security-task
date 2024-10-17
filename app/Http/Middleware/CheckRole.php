<?php

namespace App\Http\Middleware;

use App\Exceptions\AuthorizationException;
use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    use ApiResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (Auth::check() && Auth::user()->hasRole($role)) {
            return $next($request);
        }

        $message = 'Unauthorized access. Admin privileges are required.';
        throw new AuthorizationException($message);
    }
}
