<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

class Handler extends ExceptionHandler
{
    protected $levels = [
        // ...
    ];

    protected $dontReport = [
        // ...
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof SuspiciousOperationException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Ficheiro demasiado grande.'], 413);
            }

            return back()->withErrors([
                'documento' => 'O ficheiro é demasiado grande. Reduza o tamanho e tente novamente.',
            ])->withInput();
        }

        return parent::render($request, $exception);
    }
}