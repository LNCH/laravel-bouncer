<?php

namespace Lnch\LaravelBouncer\Traits;

use Lnch\LaravelBouncer\Exceptions\InvalidPermissionException;
use Lnch\LaravelBouncer\Models\Permission;
use Mockery\Exception;

trait HasPermissions
{
    public function permissions()
    {
        $tableName = config('bouncer.permissions_junction_table_name');
        return $this->morphToMany(Permission::class, $tableName);
    }

    public function assignPermission($permission): void
    {
        if (is_int($permission) || is_string($permission)) {
            $permission = is_int($permission)
                ? Permission::findOrFail($permission)
                : Permission::create(['key' => $permission]);
        }

        if ($permission instanceof Permission) {
            $this->permissions()->attach($permission);
        } else {
            throw new InvalidPermissionException();
        }
    }

    public function hasPermission($permission)
    {
        if (!$permission instanceof Permission) {
            $permission = is_int($permission)
                ? Permission::find($permission)
                : Permission::where('key', $permission)->first();
        }

        if ($permission) {
            return $this->permissions->contains($permission);
        }

        return false;
    }
}
