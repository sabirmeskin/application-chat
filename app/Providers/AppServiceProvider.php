<?php

namespace App\Providers;

use App\Services\ConversationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    // app/Providers/AppServiceProvider.php

public function register()
{
    $this->app->singleton(ConversationService::class, function ($app) {
        return new ConversationService();
    });
}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
