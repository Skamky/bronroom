<?php

namespace App\Providers;

use App\Services\BreadcrumbsService;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        Vite::prefetch(concurrency: 3);
        Inertia::share([
            'breadcrumbs' => function () {
                return BreadcrumbsService::generate();
            },
        ]);


        Inertia::share([
            'toast' => function () {

                $t = session('toast');
                if ($t !== null) {
                    session()->forget('toast');
                }
                return $t;
            },
        ]);
    }
}
