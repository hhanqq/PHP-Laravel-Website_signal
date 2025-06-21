<?php
namespace App\Http\Middleware;

use Closure;

class Cors
{
    public function handle($request, Closure $next)
    {
        // Для OPTIONS-запросов сразу возвращаем ответ с заголовками
        if ($request->isMethod('OPTIONS')) {
            return response()->json('OK', 200, $this->getCorsHeaders());
        }

        $response = $next($request);

        // Добавляем заголовки для всех остальных запросов
        foreach ($this->getCorsHeaders() as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }

    protected function getCorsHeaders()
    {
        return [
            'Access-Control-Allow-Origin' => 'https://aiboostusa.com', // Лучше конкретный домен
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
            'Access-Control-Allow-Credentials' => 'true', // Если нужны credentials
        ];
    }
}

