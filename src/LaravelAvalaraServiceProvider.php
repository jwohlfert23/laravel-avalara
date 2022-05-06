<?php

namespace Jwohlfert23\LaravelAvalara;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelAvalaraServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-avalara')
            ->hasConfigFile();
    }
}
