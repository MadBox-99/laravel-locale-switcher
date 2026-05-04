<?php

declare(strict_types=1);

namespace MadBox\LocaleSwitcher;

final class LocaleSwitcher
{
    public static function current(): string
    {
        return app()->getLocale();
    }

    /**
     * @return array<string, string>
     */
    public static function available(): array
    {
        /** @var array<string, string> $locales */
        $locales = config('locale-switcher.locales', []);

        return $locales;
    }

    public static function default(): string
    {
        /** @var string $default */
        $default = config('locale-switcher.default_locale', 'en');

        return $default;
    }

    public static function mode(): string
    {
        /** @var string $mode */
        $mode = config('locale-switcher.mode', 'cookie');

        return $mode;
    }

    /**
     * Build the canonical URL for a given locale.
     *
     * In url_prefix mode the URL is built from the configured app URL plus the
     * locale prefix (omitted for the default locale).
     * In cookie mode it returns the locale-switch route URL.
     */
    public static function urlFor(string $locale, ?string $path = null): string
    {
        if (self::mode() === 'url_prefix') {
            $appUrl = rtrim((string) config('app.url'), '/');
            $resolvedPath = $path ?? request()->getPathInfo();

            if ($resolvedPath === '' || $resolvedPath[0] !== '/') {
                $resolvedPath = '/' . $resolvedPath;
            }

            return $locale === self::default()
                ? $appUrl . $resolvedPath
                : $appUrl . '/' . $locale . $resolvedPath;
        }

        /** @var string $routeName */
        $routeName = config('locale-switcher.route_name', 'language.switch');

        return route($routeName, ['locale' => $locale]);
    }

    /**
     * Build hreflang alternates for the given path.
     *
     * @return array<string, string>
     */
    public static function alternates(?string $path = null): array
    {
        $alternates = [];

        foreach (array_keys(self::available()) as $locale) {
            $alternates[$locale] = self::urlFor($locale, $path);
        }

        $alternates['x-default'] = self::urlFor(self::default(), $path);

        return $alternates;
    }
}
