<?php

namespace Jwohlfert23\LaravelAvalara\Facades;

use Illuminate\Support\Facades\Facade;

class Avalara extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-avalara';
    }
}
