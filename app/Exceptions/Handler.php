<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // Redirect to login if 419 page expired appear
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($exception->getCode() == 419) {
            return redirect()->guest(route('login'))->with('error', 'Sesi Anda telah kedaluwarsa. Silakan masuk lagi.'); // Change 'login' to your login route name
        }

        return redirect()->guest(route('login'))->with('error', 'Sesi Anda telah kedaluwarsa. Silakan masuk lagi.'); // Change 'login' to your login route name
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TokenMismatchException) {
            return redirect()->guest(route('login'))->with('error', 'Sesi Anda telah kedaluwarsa. Silakan masuk lagi.'); // Change 'login' to your login route name
        }

        if ($exception instanceof \Illuminate\Http\Exceptions\PostTooLargeException) {
            // Redirect to a specific route when a 413 error occurs
            return redirect()->route('input.profile.data')->with('error', 'Ukuran file Photo atau CV lebih dari 2MB, Ukuran file maksimum: 2MB');
        }

        return parent::render($request, $exception);
    }
}
