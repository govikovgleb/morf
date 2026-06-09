<?php

namespace App\Providers;

use App\Contexts\Content\Domain\Observers\ReferenceSetObserver;
use App\Contexts\Content\Domain\ReferenceSet;
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
        ReferenceSet::observe(ReferenceSetObserver::class);
    }
}
