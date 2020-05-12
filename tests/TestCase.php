<?php

namespace Lnch\LaravelBouncer\Tests;

use Lnch\LaravelBouncer\LaravelBouncerServiceProvider;
use Lnch\LaravelBouncer\Models\Permission;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $user;
    protected $permission;

    public function setUp(): void
    {
        parent::setUp();

        // Load in factories for use in the tests
        $this->withFactories(__DIR__.'/factories');

        // Set up a base user and permission to test Gate
        $this->user = factory(User::class)->create();
        $this->permission = factory(Permission::class)->create(['key' => 'create_users']);
        $this->user->assignPermission($this->permission);
        $this->be($this->user);
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelBouncerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Import and migrate Users
        include_once __DIR__ . '/../database/migrations/create_users_table.php.stub';
        (new \CreateUsersTable())->up();

        // Import the package migrations
        include_once __DIR__ . '/../database/migrations/create_permissions_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_permissions_models_table.php.stub';

        // Run the up() method of the package migrations
        (new \CreatePermissionsTable)->up();
        (new \CreatePermissionsModelsTable)->up();
    }
}
