<?php

namespace Lnch\LaravelBouncer;

use Illuminate\Support\ServiceProvider;

class LaravelBouncerServiceProvider extends ServiceProvider
{
    private $migrations = [
        'create_permissions_table.php',
        'create_permissions_models_table.php',
    ];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/bouncer.php', 'bouncer');

        $this->app->bind('laravel-bouncer', function($app) {
            return new LaravelBouncer();
        });
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            // Publish config file
            $this->publishes([
                __DIR__.'/../config/bouncer.php' => config_path('bouncer.php'),
            ], 'config');

            // Publish migrations
            $this->publishes($this->getMigrationsArray(), 'migrations');
        }
    }

    private function getMigrationsArray()
    {
        $migrationsArray = [];

        foreach ($this->migrations as $key => $migration) {
            $migrationsArray[__DIR__."/../database/migrations/{$migration}.stub"] = $this->getMigrationFilename($migration, $key);
        }

        return $migrationsArray;
    }

    private function getMigrationFilename($migration, $count = 0)
    {
        return database_path('migrations/'.date('Y_m_d_His', time() + $count).'_'.$migration);
    }
}
