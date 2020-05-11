<?php

namespace Lnch\LaravelBouncer;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class LaravelBouncerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/bouncer.php', 'laravel-bouncer');
    }

    public function boot()
    {
        // Register the model factories
//        $this->app->make(Factory::class)->load(__DIR__.'/../database/factories');

        if ($this->app->runningInConsole()) {

            // Publish config file
            $this->publishes([
                __DIR__.'/../config/bouncer.php' => config_path('bouncer.php'),
            ], 'config');

            // Publish migrations
            $this->publishes([
                __DIR__.'/../database/migrations/create_permissions_table.php.stub'
                    => $this->getMigrationFilename('create_permissions_table.php'),
            ], 'migrations');

        }
    }

    private function getMigrationFilename($migration)
    {
        return database_path('migrations/'.date('Y_m_d_His', time()).'_'.$migration);
    }
}
