<?php

namespace Lnch\LaravelBouncer\Tests;

use Lnch\LaravelBouncer\LaravelBouncerServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/factories');
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelBouncerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // import the CreatePermissionsTable class from the migration
        include_once __DIR__ . '/../database/migrations/create_permissions_table.php.stub';

        // run the up() method of that migration class
        (new \CreatePermissionsTable)->up();
    }
}
