<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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
        // Chỉ force HTTPS khi môi trường là production
        if (App::environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            $view->with('user', Auth::user());
        });
        Carbon::setLocale('vi');
    }
}
