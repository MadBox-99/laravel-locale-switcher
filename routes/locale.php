<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware('web')->get(config('locale-switcher.route_prefix', '/language') . '/{locale}', function (string $locale) {
    /** @var array<string, string> $locales */
    $locales = config('locale-switcher.locales', []);

    if (! array_key_exists($locale, $locales)) {
        abort(400);
    }

    /** @var string $mode */
    $mode = config('locale-switcher.mode', 'cookie');

    $previous = url()->previous('/');
    $isSameHost = parse_url($previous, PHP_URL_HOST) === parse_url(config('app.url'), PHP_URL_HOST);

    if ($mode === 'url_prefix') {
        /** @var string $defaultLocale */
        $defaultLocale = config('locale-switcher.default_locale', 'en');

        $appUrl = rtrim((string) config('app.url'), '/');

        $previousPath = '/';

        if ($isSameHost) {
            $parsedPath = parse_url($previous, PHP_URL_PATH);
            $previousPath = is_string($parsedPath) && $parsedPath !== '' ? $parsedPath : '/';

            // Strip any existing locale prefix from previous path so we can re-prefix cleanly.
            $localeKeys = array_keys($locales);

            if ($localeKeys !== [] && preg_match('#^/('.implode('|', $localeKeys).')(/.*)?$#', $previousPath, $matches)) {
                $previousPath = $matches[2] ?? '/';

                if ($previousPath === '') {
                    $previousPath = '/';
                }
            }
        }

        $redirectUrl = $locale === $defaultLocale
            ? $appUrl . $previousPath
            : $appUrl . '/' . $locale . $previousPath;

        return redirect($redirectUrl);
    }

    /** @var string $cookieName */
    $cookieName = config('locale-switcher.cookie_name', 'locale');

    /** @var int $cookieLifetime */
    $cookieLifetime = config('locale-switcher.cookie_lifetime', 60 * 24 * 365);

    $cookie = cookie($cookieName, $locale, $cookieLifetime);

    $redirectUrl = $isSameHost ? $previous : '/';

    return redirect($redirectUrl)->withCookie($cookie);
})->name(config('locale-switcher.route_name', 'language.switch'));
