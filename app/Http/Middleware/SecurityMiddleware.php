<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Remove Fingerprint Headers
        $this->removeFingerprintHeaders($request);

        // Add Security Headers
        $this->addSecurityHeaders($response);

        return $response;
    }

    /**
     * Remove unnecessary fingerprint headers from the request.
     *
     * @param Request $request
     */
    protected function removeFingerprintHeaders(Request $request): void
    {
        $headersToRemove = [
            'X-Powered-By',
            'Server',
            'x-turbo-charged-by'
        ];

        foreach ($headersToRemove as $header) {
            $request->headers->remove($header);
        }
    }

    /**
     * Add security-related headers to the response.
     *
     * @param Response $response
     */
    protected function addSecurityHeaders(Response $response): void
    {
        $securityHeaders = [
            'X-Frame-Options' => 'deny',
            'X-Content-Type-Options' => 'nosniff',
            'X-Permitted-Cross-Domain-Policies' => 'none',
            'Referrer-Policy' => 'no-referrer',
            'Cross-Origin-Embedder-Policy' => 'require-corp',
            'Content-Security-Policy' => "default-src 'none'; style-src 'self'; form-action 'self'",
            'X-XSS-Protection' => '1; mode=block',
        ];

        foreach ($securityHeaders as $header => $value) {
            $response->headers->set($header, $value);
        }

        // Add Strict-Transport-Security header in production environment
        if (config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
    }
}
