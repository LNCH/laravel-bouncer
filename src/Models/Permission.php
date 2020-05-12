<?php

namespace Lnch\LaravelBouncer\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('bouncer.permissions_table_name'));
    }
}
