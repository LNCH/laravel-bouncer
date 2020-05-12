<?php

namespace Lnch\LaravelBouncer;

use Illuminate\Support\Facades\Gate;
use Lnch\LaravelBouncer\Models\Permission;

class LaravelBouncer
{
    public function registerGateChecks()
    {
        foreach (Permission::all() as $permission) {
            Gate::define($permission->key, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }
    }
}
