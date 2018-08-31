<?php

namespace App\Providers;

use App\Services\MitakeSmexpress;
use App\Services\Pay2GoMPG;
use App\Services\Pay2GoCancel;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        date_default_timezone_set(config('app.timezone'));
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }
        $this->app->singleton('mitake_smexpress', function() {
            return new MitakeSmexpress;
        });
        $this->app->singleton('pay2go_mpg', function() {
            return new Pay2GoMPG;
        });
        $this->app->singleton('pay2go_cancel', function() {
            return new Pay2GoCancel;
        });
    }
}
