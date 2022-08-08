<?php

namespace Jwohlfert23\LaravelAvalara\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Jwohlfert23\LaravelAvalara\LaravelAvalaraServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Jwohlfert23\\LaravelAvalara\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelAvalaraServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-avalara_table.php.stub';
        $migration->up();
        */
    }
}
