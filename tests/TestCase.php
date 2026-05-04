<?php

declare(strict_types=1);

namespace MadBox\LocaleSwitcher\Tests;

use MadBox\LocaleSwitcher\LocaleSwitcherServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LocaleSwitcherServiceProvider::class,
        ];
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.url', 'http://localhost');
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }
}
