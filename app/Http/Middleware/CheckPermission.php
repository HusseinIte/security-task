<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    use ApiResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {

        if (Auth::check() && Auth::user()->hasPermission($permission)) {
            return $next($request);
        }

        $message = 'Unauthorized access. Admin privileges are required.';
        if ($request->expectsJson()) {
            return $this->sendError(null, $message, 403); // 403 Forbidden status code
        }
    }
}
