<?php

namespace MoeMizrak\ValidatorGuard\Facades;

use Illuminate\Support\Facades\Facade;

class ValidatorGuard extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'validator-guard';
    }
}