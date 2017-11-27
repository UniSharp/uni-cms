<?php

namespace UniSharp\UniCMS;

use Illuminate\Support\ServiceProvider;

class UniCMSServiceProvider extends ServiceProvider
{
    /**
     * Boot the services for the application
     */
    public function boot()
    {
        $this->registerMigration();
    }

    protected function registerMigration()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
