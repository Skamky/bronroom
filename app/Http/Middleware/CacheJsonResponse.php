<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheJsonResponse
{
    /**
     *   Добавляет для ответа  заголовок позволяющий кэшировать браузером пользователя овет
     * @param Request $request запрос  формата JSON
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @param int $maxAge - длительность кеша в секундах
     * @return Response
     */
    public function handle(Request $request, Closure $next, int $maxAge = 86400): Response
    {
        $response = $next($request);

        // Если ответ является JSON ответом, устанавливаем заголовки кэширования.
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $response->header('Cache-Control', "private, max-age=$maxAge, must-revalidate");
        }

        return $response;
    }
}
