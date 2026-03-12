<?php

declare(strict_types=1);

namespace MadBox\LocaleSwitcher;

use Illuminate\Support\ServiceProvider;

final class LocaleSwitcherServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/locale-switcher.php',
            'locale-switcher',
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/locale-switcher.php' => config_path('locale-switcher.php'),
            ], 'locale-switcher-config');
        }

        $this->loadRoutesFrom(__DIR__ . '/../routes/locale.php');
    }
}
