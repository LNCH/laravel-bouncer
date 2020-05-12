<?php

namespace Lnch\LaravelBouncer\Traits;

use Lnch\LaravelBouncer\Exceptions\InvalidPermissionException;
use Lnch\LaravelBouncer\Models\Permission;

trait HasPermissions
{
    public function permissions()
    {
        $tableName = config('bouncer.permissions_junction_table_name');
        return $this->morphToMany(Permission::class, $tableName);
    }

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

    public function assignPermissions($permissions = [])
    {
        collect($permissions)->each(function ($permission) {
            $this->assignPermission($permission);
        });
    }

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

    public function revokePermissions($permissions = [])
    {
        collect($permissions)->each(function ($permission) {
            $this->revokePermission($permission);
        });
    }

    public function hasPermission($permission): bool
    {
        if (!$permission instanceof Permission) {
            $permission = is_int($permission)
                ? Permission::find($permission)
                : Permission::where(['key' => $permission])->first();
        }

        if ($permission) {
            return $this->permissions->contains($permission);
        }

        return false;
    }

    public function hasAnyOfPermissions($permissions = [])
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function hasAllOfPermissions($permissions = [])
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    private function findPermission($permission): ?Permission
    {
        return is_int($permission)
            ? Permission::find($permission)
            : Permission::where(['key' => $permission])->first();
    }

    private function createPermission($permission): ?Permission
    {
        return Permission::create(['key' => $permission]);
    }
}
