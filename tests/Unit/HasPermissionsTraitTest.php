<?php

namespace Lnch\LaravelBouncer\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Lnch\LaravelBouncer\Exceptions\InvalidPermissionException;
use Lnch\LaravelBouncer\Models\Permission;
use Lnch\LaravelBouncer\Tests\TestCase;
use Lnch\LaravelBouncer\Tests\User;

class HasPermissionsTraitTest extends TestCase
{
    use RefreshDatabase;

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

    /** @test */
    public function passing_a_string_to_assign_permission_creates_the_permission_if_it_does_not_exist(): void
    {
        $user = factory(User::class)->create();
        $user->assignPermission('create_users');

        $this->assertDatabaseHas(config('bouncer.permissions_table_name'), [
            'key' => 'create_users'
        ]);
    }

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
    public function an_exception_is_thrown_when_an_invalid_value_is_passed_to_assign_permission(): void
    {
        $user = factory(User::class)->create();
        $this->expectException(InvalidPermissionException::class);
        $user->assignPermission(true);
    }

    /** @test */
    public function revoke_permission_removes_a_users_permission_when_given_a_model(): void
    {
        $user = factory(User::class)->create();
        $permission = factory(Permission::class)->create();

        DB::table(config('bouncer.permissions_junction_table_name'))
            ->insert([
                'permission_id' => $permission->id,
                'permissions_models_id' => $user->id,
                'permissions_models_type' => User::class
            ]);

        $this->assertCount(1, $user->permissions);
        $user->revokePermission($permission);
        $this->assertCount(0, $user->refresh()->permissions);
    }

    /** @test */
    public function revoke_permission_removes_a_users_permission_when_given_a_string(): void
    {
        $user = factory(User::class)->create();
        $permission = factory(Permission::class)->create();

        DB::table(config('bouncer.permissions_junction_table_name'))
            ->insert([
                'permission_id' => $permission->id,
                'permissions_models_id' => $user->id,
                'permissions_models_type' => User::class
            ]);

        $this->assertCount(1, $user->permissions);
        $user->revokePermission($permission->key);
        $this->assertCount(0, $user->refresh()->permissions);
    }

    /** @test */
    public function revoke_permission_removes_a_users_permission_when_given_a_model_id(): void
    {
        $user = factory(User::class)->create();
        $permission = factory(Permission::class)->create();

        DB::table(config('bouncer.permissions_junction_table_name'))
            ->insert([
                'permission_id' => $permission->id,
                'permissions_models_id' => $user->id,
                'permissions_models_type' => User::class
            ]);

        $this->assertCount(1, $user->permissions);
        $user->revokePermission($permission->id);
        $this->assertCount(0, $user->refresh()->permissions);
    }

    /** @test */
    public function an_exception_is_thrown_when_an_invalid_value_is_passed_to_revoke_permission(): void
    {
        $user = factory(User::class)->create();
        $this->expectException(InvalidPermissionException::class);
        $user->revokePermission(true);
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

        $this->assertFalse($user->hasPermission(20));
    }

    /** @test */
    public function can_assign_multiple_permissions_at_once(): void
    {
        $user = factory(User::class)->create();
        $user->assignPermissions(['create_users', 'edit_users', 'delete_users']);
        $this->assertCount(3, $user->permissions);
    }

    /** @test */
    public function can_revoke_multiple_permissions_at_once(): void
    {
        $user = factory(User::class)->create();
        $permissions = factory(Permission::class, 5)->create();

        foreach ($permissions as $permission) {
            DB::table(config('bouncer.permissions_junction_table_name'))
                ->insert([
                    'permission_id' => $permission->id,
                    'permissions_models_id' => $user->id,
                    'permissions_models_type' => User::class
                ]);
        }

        $user->revokePermissions([
            $permissions[0]->key,
            $permissions[1]->key,
            $permissions[2]->key,
        ]);

        $this->assertCount(2, $user->permissions);
    }

    /** @test */
    public function can_check_if_a_model_has_any_of_an_array_of_permissions(): void
    {
        $user = factory(User::class)->create();
        $permissions = factory(Permission::class, 5)->create();

        foreach ($permissions as $permission) {
            DB::table(config('bouncer.permissions_junction_table_name'))
                ->insert([
                    'permission_id' => $permission->id,
                    'permissions_models_id' => $user->id,
                    'permissions_models_type' => User::class
                ]);
        }

        $this->assertTrue($user->hasAnyOfPermissions([
            'invalid_permission',
            $permissions[2]->key,
            'another_invalid_permission',
        ]));
    }

    /** @test */
    public function can_check_if_a_model_has_all_of_an_array_of_permissions(): void
    {
        $user = factory(User::class)->create();
        $permissions = factory(Permission::class, 5)->create();

        foreach ($permissions as $permission) {
            DB::table(config('bouncer.permissions_junction_table_name'))
                ->insert([
                    'permission_id' => $permission->id,
                    'permissions_models_id' => $user->id,
                    'permissions_models_type' => User::class
                ]);
        }

        $this->assertTrue($user->hasAllOfPermissions([
            $permissions[2]->key,
            $permissions[4]->key,
            $permissions[1]->key,
        ]));
    }
}
