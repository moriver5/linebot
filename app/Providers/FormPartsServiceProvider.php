<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FormPartsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'formparts',
            'App\Libs\FormParts'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
