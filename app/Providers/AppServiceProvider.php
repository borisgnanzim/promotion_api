<?php

namespace App\Providers;

use App\Repositories\OtpCodeRepository;
use App\Repositories\OtpCodeRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OtpCodeRepositoryInterface::class, OtpCodeRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
