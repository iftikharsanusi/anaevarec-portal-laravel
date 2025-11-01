<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Strict-Transport-Security (HSTS) - Only add if HTTPS
        if ($request->secure() || $request->header('X-Forwarded-Proto') === 'https') {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // X-Frame-Options - Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // X-Content-Type-Options - Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-XSS-Protection - Enable XSS filtering
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Content-Security-Policy - Prevent XSS and injection attacks
        // Allow external resources needed by the app (fonts, CDNs, screenshot services)
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://fonts.bunny.net https://cdn.jsdelivr.net https://www.googletagmanager.com; " .
               "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://cdn.tailwindcss.com; " .
               "font-src 'self' https://fonts.bunny.net https://fonts.gstatic.com data:; " .
               "img-src 'self' data: https: http: blob:; " .
               "connect-src 'self' https: http:; " .
               "frame-src 'self' https: http:; " .
               "object-src 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self'; " .
               "frame-ancestors 'self';";
        
        // Only add upgrade-insecure-requests if using HTTPS
        if ($request->secure() || $request->header('X-Forwarded-Proto') === 'https') {
            $csp .= " upgrade-insecure-requests;";
        }
        
        $response->headers->set('Content-Security-Policy', $csp);

        // Referrer-Policy - Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions-Policy - Control browser features and APIs
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), accelerometer=()'
        );

        // X-Permitted-Cross-Domain-Policies - Control cross-domain policy files
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // Expect-CT - Certificate Transparency monitoring (optional, can be removed if causing issues)
        // $response->headers->set('Expect-CT', 'max-age=86400, enforce');

        // Clear-Site-Data - Clear browsing data (optional, use carefully)
        // Only uncomment if needed for logout functionality
        // $response->headers->set('Clear-Site-Data', '"cache", "cookies", "storage"');

        // Remove Server header if present (security through obscurity)
        $response->headers->remove('Server');
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
