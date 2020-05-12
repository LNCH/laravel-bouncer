<?php

namespace Lnch\LaravelBouncer\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lnch\LaravelBouncer\Exceptions\InvalidPermissionException;
use Lnch\LaravelBouncer\Models\Permission;
use Lnch\LaravelBouncer\Tests\TestCase;
use Lnch\LaravelBouncer\Tests\User;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_permission_has_a_key(): void
    {
        $permission = factory(Permission::class)->create(['key' => 'create_user']);
        $this->assertEquals('create_user', $permission->key);
    }

//    /** @test */
//    public function a_permission_has_a_group(): void
//    {
//        $permission = factory(Permission::class)->create(['group' => 'users']);
//        $this->assertEquals('users', $permission->group);
//    }
//
//    /** @test */
//    public function a_permission_can_have_a_null_group(): void
//    {
//        $permission = factory(Permission::class)->create(['group' => null]);
//        $this->assertNull($permission->group);
//    }

    /** @test */
    public function a_permission_has_a_label(): void
    {
        $permission = factory(Permission::class)->create(['label' => 'create_user']);
        $this->assertEquals('create_user', $permission->label);
    }

    /** @test */
    public function a_permission_can_have_a_null_label(): void
    {
        $permission = factory(Permission::class)->create(['label' => null]);
        $this->assertNull($permission->label);
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

    /** @test */
    public function a_user_has_permissions(): void
    {
        $user = factory(User::class)->create();
        $permission = factory(Permission::class)->create();
        $user->permissions()->attach($permission);
        $this->assertCount(1, $user->permissions);
    }

    /** @test */
    public function a_model_can_be_assigned_a_permission_from_a_model(): void
    {
        $user = factory(User::class)->create();
        $permission = factory(Permission::class)->create();

        $this->assertCount(0, $user->permissions);
        $user->assignPermission($permission);
        $this->assertCount(1, $user->refresh()->permissions);
    }

    /** @test */
    public function a_model_can_be_assigned_a_permission_from_a_string(): void
    {
        $user = factory(User::class)->create();

        $this->assertCount(0, $user->permissions);
        $user->assignPermission('create_users');
        $this->assertCount(1, $user->refresh()->permissions);
    }

    // test the permission is created by assigning

    /** @test */
    public function a_model_can_be_assigned_a_permission_from_an_id(): void
    {
        $user = factory(User::class)->create();
        $permission = factory(Permission::class)->create();

        $this->assertCount(0, $user->permissions);
        $user->assignPermission($permission->id);
        $this->assertCount(1, $user->refresh()->permissions);
    }

    // test that the ID must exist to assign the permission

    /** @test */
    public function an_exception_is_thrown_when_an_invalid_value_is_assigned_as_a_permission(): void
    {
        $user = factory(User::class)->create();
        $this->expectException(InvalidPermissionException::class);
        $user->assignPermission(true);
    }

    /** @test */
    public function has_permission_returns_true_if_the_model_has_the_correct_permission_from_model(): void
    {
        $user = factory(User::class)->create();
        $permission = factory(Permission::class)->create();
        $user->assignPermission($permission);

        $this->assertTrue($user->hasPermission($permission));
    }

    /** @test */
    public function has_permission_returns_true_if_the_model_has_the_correct_permission_from_string(): void
    {
        $user = factory(User::class)->create();
        $permission = factory(Permission::class)->create();
        $user->assignPermission($permission);

        $this->assertTrue($user->hasPermission($permission->key));
    }

    /** @test */
    public function has_permission_returns_true_if_the_model_has_the_correct_permission_from_id(): void
    {
        $user = factory(User::class)->create();
        $permission = factory(Permission::class)->create();
        $user->assignPermission($permission);

        $this->assertTrue($user->hasPermission($permission->id));
    }

    /** @test */
    public function has_permission_returns_false_if_the_model_does_not_have_the_correct_permission_from_model(): void
    {
        $user = factory(User::class)->create();
        $permission = factory(Permission::class)->create();
        $permission2 = factory(Permission::class)->create();
        $user->assignPermission($permission);

        $this->assertFalse($user->hasPermission($permission2));
    }

    /** @test */
    public function has_permission_returns_false_if_the_model_does_not_have_the_correct_permission_from_string(): void
    {
        $user = factory(User::class)->create();
        $permission = factory(Permission::class)->create();
        $user->assignPermission($permission);

        $this->assertFalse($user->hasPermission('test string'));
    }

    /** @test */
    public function has_permission_returns_false_if_the_model_does_not_have_the_correct_permission_from_id(): void
    {
        $user = factory(User::class)->create();
        $permission = factory(Permission::class)->create();
        $user->assignPermission($permission);

        $this->assertFalse($user->hasPermission(2));
    }
}
