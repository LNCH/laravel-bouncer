<?php

namespace Lnch\LaravelBouncer\Traits;

use Illuminate\Database\Eloquent\Collection;
use Lnch\LaravelBouncer\Exceptions\InvalidPermissionException;
use Lnch\LaravelBouncer\Models\Permission;

trait HasPermissions
{
    public function permissions()
    {
        $tableName = config('bouncer.permissions_junction_table_name');
        return $this->morphToMany(Permission::class, $tableName);
    }

    /**
     * Assigns a permission to the model. Can accept an integer ID for an existing
     * permission, an existing Permission model, or a string key. If the string key
     * given does not yet exist, the permission will be created and then assigned.
     *
     * @param $permission
     * @throws InvalidPermissionException
     */
    public function assignPermission($permission): void
    {
        if (!is_string($permission) && !is_int($permission) && !$permission instanceof Permission) {
            throw new InvalidPermissionException();
        }

        if (is_int($permission) || is_string($permission)) {
            $permission = $this->findPermission($permission) ?? $permission;
        }

        if (is_string($permission)) {
            $permission = $this->createPermission($permission);
        }

        $this->permissions()->attach($permission);
        $this->refresh();
    }

    /**
     * Assigns multiple permissions at once to the model according to the rules
     * specified in the assignPermission() method.
     *
     * @param array $permissions
     */
    public function assignPermissions($permissions = [])
    {
        collect($permissions)->each(function ($permission) {
            $this->assignPermission($permission);
        });
    }

    /**
     * Revokes a permission from the model. Follows the same rules specified in
     * the assignPermission() method.
     *
     * @param $permission
     * @throws InvalidPermissionException
     */
    public function revokePermission($permission): void
    {
        if (!is_string($permission) && !is_int($permission) && !$permission instanceof Permission) {
            throw new InvalidPermissionException();
        }

        if (is_int($permission) || is_string($permission)) {
            $permission = $this->findPermission($permission) ?? $permission;
        }

        $this->permissions()->detach($permission);
        $this->refresh();
    }

    /**
     * Revokes multiple permissions from the model.
     *
     * @param array $permissions
     */
    public function revokePermissions($permissions = [])
    {
        collect($permissions)->each(function ($permission) {
            $this->revokePermission($permission);
        });
    }

    /**
     * Returns a boolean value stating if the model already has the
     * given permission assigned.
     *
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission): bool
    {
        if (!$permission instanceof Permission) {
            $permission = is_int($permission)
                ? Permission::find($permission)
                : Permission::where(['key' => $permission])->first();
        }

        if ($permission) {
            return $this->retrieveAllPermissions()->contains($permission);
        }

        return false;
    }

    /**
     * Returns true if the model has any of the array of permissions given. If
     * none of the given permissions are assigned to the model it will return
     * false.
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAnyOfPermissions($permissions = [])
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if the model has all of the given permissions assigned.
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAllOfPermissions($permissions = [])
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retrieves a collection of all permissions assigned, either directly
     * or indirectly.
     *
     * @return Collection
     */
    public function retrieveAllPermissions(): Collection
    {
        $corePermissions = $this->permissions;
        return $corePermissions;
    }

    /**
     * Finds a permission using a given identifier. The identifier can be
     * either an integer ID or a string key.
     *
     * @param $permission
     * @return Permission|null
     */
    private function findPermission($permission): ?Permission
    {
        return is_int($permission)
            ? Permission::find($permission)
            : Permission::where(['key' => $permission])->first();
    }

    /**
     * Creates a new permission from a string key.
     *
     * @param $permission
     * @return Permission|null
     */
    private function createPermission($permission): ?Permission
    {
        return Permission::create(['key' => $permission]);
    }
}
