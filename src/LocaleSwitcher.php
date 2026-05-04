<?php

declare(strict_types=1);

namespace MadBox\LocaleSwitcher;

final class LocaleSwitcher
{
    public const MODE_COOKIE = 'cookie';

    public const MODE_URL_PREFIX = 'url_prefix';

    public const REQUEST_ATTRIBUTE = '_locale';

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
        $mode = config('locale-switcher.mode', self::MODE_COOKIE);

        return $mode;
    }

    public static function appUrl(): string
    {
        return rtrim((string) config('app.url'), '/');
    }

    /**
     * Remove a leading locale prefix (e.g. /en, /de) from a path.
     *
     * @param  bool  $includeDefault  When true, the default locale prefix is also stripped.
     */
    public static function stripLocalePrefix(string $path, bool $includeDefault = false): string
    {
        $keys = $includeDefault
            ? array_keys(self::available())
            : array_diff(array_keys(self::available()), [self::default()]);

        if ($keys !== [] && preg_match('#^/('.implode('|', $keys).')(/.*)?$#', $path, $matches)) {
            $path = $matches[2] ?? '/';
        }

        return $path === '' ? '/' : $path;
    }

    /**
     * Build the canonical URL for a given locale.
     *
     * In url_prefix mode the URL is built from the configured app URL plus the
     * locale prefix (omitted for the default locale). The given path is normalised
     * to drop any pre-existing locale prefix so callers can pass the current
     * request path safely.
     * In cookie mode it returns the locale-switch route URL.
     */
    public static function urlFor(string $locale, ?string $path = null): string
    {
        if (self::mode() === self::MODE_URL_PREFIX) {
            $resolvedPath = self::stripLocalePrefix(
                $path ?? request()->getPathInfo(),
                includeDefault: true,
            );

            if ($resolvedPath === '' || $resolvedPath[0] !== '/') {
                $resolvedPath = '/'.$resolvedPath;
            }

            return $locale === self::default()
                ? self::appUrl().$resolvedPath
                : self::appUrl().'/'.$locale.$resolvedPath;
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
