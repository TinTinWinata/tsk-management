<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ApiErrorHandler
{
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            // Handle different types of exceptions as needed
            $status = $e instanceof HttpException ? $e->getStatusCode() : 500;
            $message = $e->getMessage() ?: 'Server Error';

            return response()->json([
                'error' => true,
                'message' => $message,
            ], $status);
        }
    }
}
