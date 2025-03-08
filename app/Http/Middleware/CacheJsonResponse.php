<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Если ответ является JSON ответом, устанавливаем заголовки кэширования.
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $response->header('Cache-Control', 'public, max-age=86400, must-revalidate');
        }

        return $response;
    }
}
