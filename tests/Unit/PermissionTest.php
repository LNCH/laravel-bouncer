<?php

namespace Lnch\LaravelBouncer\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lnch\LaravelBouncer\Models\Permission;
use Lnch\LaravelBouncer\Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_permission_has_a_key(): void
    {
        $permission = factory(Permission::class)->create(['key' => 'create_user']);
        $this->assertEquals('create_user', $permission->key);
    }

    /** @test */
    public function a_permission_has_a_group(): void
    {
        $permission = factory(Permission::class)->create(['group' => 'users']);
        $this->assertEquals('users', $permission->group);
    }

    /** @test */
    public function a_permission_can_have_a_null_group(): void
    {
        $permission = factory(Permission::class)->create(['group' => null]);
        $this->assertNull($permission->group);
    }

    /** @test */
    public function a_permission_has_a_label(): void
    {
        $permission = factory(Permission::class)->create(['label' => 'create_user']);
        $this->assertEquals('create_user', $permission->label);
    }

    /** @test */
    public function a_permission_has_a_description(): void
    {
        $permission = factory(Permission::class)->create(['description' => 'Creates users']);
        $this->assertEquals('Creates users', $permission->description);
    }

    /** @test */
    public function a_permission_can_have_a_null_description(): void
    {
        $permission = factory(Permission::class)->create(['description' => null]);
        $this->assertNull($permission->description);
    }
}
