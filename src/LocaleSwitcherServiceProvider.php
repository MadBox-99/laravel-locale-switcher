<?php

declare(strict_types=1);

namespace MadBox\LocaleSwitcher;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use MadBox\LocaleSwitcher\Middleware\SetLocale;
use MadBox\LocaleSwitcher\Middleware\StripLocalePrefix;

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

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/locale-switcher'),
            ], 'locale-switcher-views');
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'locale-switcher');

        $this->loadRoutesFrom(__DIR__ . '/../routes/locale.php');

        /** @var Router $router */
        $router = $this->app['router'];
        $router->aliasMiddleware('locale.set', SetLocale::class);
        $router->aliasMiddleware('locale.strip', StripLocalePrefix::class);
    }
}
