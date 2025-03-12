<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogErrors
{
    public function handle($request, Closure $next)
    {
        try {
            Log::info('Iniciando requisição', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'headers' => $request->headers->all(),
            ]);

            $response = $next($request);

            Log::info('Finalizando requisição', [
                'status' => $response->status(),
            ]);

            return $response;
        } catch (\Throwable $e) {
            Log::error('Erro não capturado:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_url' => $request->fullUrl(),
                'request_method' => $request->method(),
                'request_headers' => $request->headers->all(),
            ]);
            throw $e;
        }
    }
} 