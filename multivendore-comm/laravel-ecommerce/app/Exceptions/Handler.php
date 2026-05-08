<?php
namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        InvalidCouponException::class,
        InsufficientStockException::class,
        OrderCancellationException::class,
    ];

    protected $dontFlash = ['current_password','password','password_confirmation'];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {});

        // Always return JSON for API routes
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return $this->handleApiException($e, $request);
            }
        });
    }

    private function handleApiException(Throwable $e, $request)
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        }

        if ($e instanceof AuthenticationException) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($e instanceof AuthorizationException) {
            return response()->json(['message' => $e->getMessage() ?: 'Forbidden.'], 403);
        }

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            $model   = $e instanceof ModelNotFoundException ? class_basename($e->getModel()) : 'Resource';
            return response()->json(['message' => "{$model} not found."], 404);
        }

        if ($e instanceof HttpException) {
            return response()->json(['message' => $e->getMessage() ?: 'HTTP error.'], $e->getStatusCode());
        }

        if ($e instanceof InsufficientStockException || $e instanceof InvalidCouponException || $e instanceof OrderCancellationException) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 422);
        }

        // Generic 500
        $message = config('app.debug') ? $e->getMessage() : 'An internal server error occurred.';
        return response()->json(['message' => $message], 500);
    }
}
