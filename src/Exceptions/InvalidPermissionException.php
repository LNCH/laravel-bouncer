<?php

namespace Lnch\LaravelBouncer\Exceptions;

use Lnch\LaravelBouncer\Models\Permission;
use Throwable;

class InvalidPermissionException extends \Exception
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        if ($message === '') {
            $backtrace = debug_backtrace();
            $calledMethod = $backtrace[1]['function'];
            $message = "You must supply either a string, integer ID or instance of '"
                . Permission::class . "' to the {$calledMethod}() method.";
        }

        parent::__construct($message, $code, $previous);
    }
}
