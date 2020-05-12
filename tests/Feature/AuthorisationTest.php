<?php

namespace Lnch\LaravelBouncer\Tests\Feature;

use Illuminate\Support\Facades\Gate;
use Lnch\LaravelBouncer\Models\Permission;
use Lnch\LaravelBouncer\Tests\TestCase;
use Lnch\LaravelBouncer\Tests\User;

class AuthorisationTest extends TestCase
{
    private $user;
    private $permission;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->permission = factory(Permission::class)->create(['key' => 'create_users']);
        $this->user->assignPermission($this->permission);
        $this->be($this->user);
    }

    /** @test */
    public function a_permission_passes_a_gate_check_with_the_can_method(): void
    {
        Gate::define($this->permission->key, function ($user) {
            return $user->hasPermission($this->permission);
        });

        $this->assertTrue($this->user->can('create_users'));
    }
}
