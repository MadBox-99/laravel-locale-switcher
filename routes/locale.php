<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get(config('locale-switcher.route_prefix', '/language') . '/{locale}', function (string $locale) {
    /** @var array<string> $locales */
    $locales = config('locale-switcher.locales', []);

    if (! in_array($locale, $locales, true)) {
        abort(400);
    }

    /** @var string $cookieName */
    $cookieName = config('locale-switcher.cookie_name', 'locale');

    /** @var int $cookieLifetime */
    $cookieLifetime = config('locale-switcher.cookie_lifetime', 60 * 24 * 365);

    $cookie = cookie($cookieName, $locale, $cookieLifetime);

    $previous = url()->previous('/');
    $isSameHost = parse_url($previous, PHP_URL_HOST) === parse_url(config('app.url'), PHP_URL_HOST);
    $redirectUrl = $isSameHost ? $previous : '/';

    return redirect($redirectUrl)->withCookie($cookie);
})->name(config('locale-switcher.route_name', 'language.switch'));
