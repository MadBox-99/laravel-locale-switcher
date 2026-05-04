<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use MadBox\LocaleSwitcher\Middleware\StripLocalePrefix;

beforeEach(function (): void {
    config()->set('locale-switcher.locales', ['en' => 'English', 'hu' => 'Magyar', 'de' => 'Deutsch']);
    config()->set('locale-switcher.default_locale', 'hu');
});

it('passes through unchanged in cookie mode', function (): void {
    config()->set('locale-switcher.mode', 'cookie');

    $request = Request::create('/en/foo', 'GET');

    (new StripLocalePrefix())->handle($request, fn (Request $r) => response('ok'));

    expect($request->getPathInfo())->toBe('/en/foo')
        ->and($request->attributes->get('_locale'))->toBeNull();
});

it('strips a non-default locale prefix and stores the locale', function (): void {
    config()->set('locale-switcher.mode', 'url_prefix');

    $request = Request::create('/en/foo', 'GET');

    (new StripLocalePrefix())->handle($request, fn (Request $r) => response('ok'));

    expect($request->getPathInfo())->toBe('/foo')
        ->and($request->attributes->get('_locale'))->toBe('en');
});

it('handles a bare locale prefix without a trailing path', function (): void {
    config()->set('locale-switcher.mode', 'url_prefix');

    $request = Request::create('/de', 'GET');

    (new StripLocalePrefix())->handle($request, fn (Request $r) => response('ok'));

    expect($request->getPathInfo())->toBe('/')
        ->and($request->attributes->get('_locale'))->toBe('de');
});

it('leaves non-locale paths untouched', function (): void {
    config()->set('locale-switcher.mode', 'url_prefix');

    $request = Request::create('/foo/bar', 'GET');

    (new StripLocalePrefix())->handle($request, fn (Request $r) => response('ok'));

    expect($request->getPathInfo())->toBe('/foo/bar')
        ->and($request->attributes->get('_locale'))->toBeNull();
});

it('does not strip the default locale when it appears as a prefix', function (): void {
    config()->set('locale-switcher.mode', 'url_prefix');

    $request = Request::create('/hu/foo', 'GET');

    (new StripLocalePrefix())->handle($request, fn (Request $r) => response('ok'));

    expect($request->getPathInfo())->toBe('/hu/foo')
        ->and($request->attributes->get('_locale'))->toBeNull();
});
