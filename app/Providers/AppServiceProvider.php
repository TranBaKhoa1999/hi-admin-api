<?php

namespace App\Providers;

use App\Core\StatusCodeObject;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        require __DIR__ . '/../Helpers/helper.php';

        $this->app->singleton('statusCodeObjectClass', function ($app) {
            return new StatusCodeObject;
        });
    }
}
