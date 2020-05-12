<?php

namespace Lnch\LaravelBouncer;

use Illuminate\Support\Facades\Facade;

class LaravelBouncerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-bouncer';
    }
}
