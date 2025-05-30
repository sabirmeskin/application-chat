<?php

namespace App\Providers;

use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(\Illuminate\Contracts\Debug\ExceptionHandler::class, function ($app) {
        return new class($app) extends \Illuminate\Foundation\Exceptions\Handler {
            public function render($request, \Throwable $e)
            {
                if ($e instanceof TokenMismatchException && $request->expectsJson()) {
                    return response()->json(['message' => 'Session expired.'], 419);
                }

                return parent::render($request, $e);
            }
        };
    });
    }
}
