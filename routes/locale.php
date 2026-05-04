<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use MadBox\LocaleSwitcher\LocaleSwitcher;

Route::middleware('web')->get(config('locale-switcher.route_prefix', '/language').'/{locale}', function (string $locale) {
    if (! array_key_exists($locale, LocaleSwitcher::available())) {
        abort(400);
    }

    $previous = url()->previous('/');
    $isSameHost = parse_url($previous, PHP_URL_HOST) === parse_url(config('app.url'), PHP_URL_HOST);

    if (LocaleSwitcher::mode() === LocaleSwitcher::MODE_URL_PREFIX) {
        $previousPath = '/';

        if ($isSameHost) {
            $parsedPath = parse_url($previous, PHP_URL_PATH);
            $previousPath = is_string($parsedPath) && $parsedPath !== '' ? $parsedPath : '/';
        }

        return redirect(LocaleSwitcher::urlFor($locale, $previousPath));
    }

    /** @var int $cookieLifetime */
    $cookieLifetime = config('locale-switcher.cookie_lifetime', 60 * 24 * 365);

    $cookie = cookie((string) config('locale-switcher.cookie_name', 'locale'), $locale, $cookieLifetime);

    return redirect($isSameHost ? $previous : '/')->withCookie($cookie);
})->name(config('locale-switcher.route_name', 'language.switch'));
