<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use MadBox\LocaleSwitcher\Middleware\SetLocale;

beforeEach(function (): void {
    config()->set('locale-switcher.mode', 'url_prefix');
    config()->set('locale-switcher.locales', ['en' => 'English', 'hu' => 'Magyar', 'de' => 'Deutsch']);
    config()->set('locale-switcher.default_locale', 'hu');
    config()->set('app.url', 'http://localhost');

    App::setLocale('hu');
});

it('prefers the _locale request attribute over the cookie', function (): void {
    $request = Request::create('/foo', 'GET');
    $request->cookies->set('locale', 'de');
    $request->attributes->set('_locale', 'en');

    (new SetLocale())->handle($request, fn ($r) => response('ok'));

    expect(App::getLocale())->toBe('en');
});

it('falls back to the cookie when no attribute is set', function (): void {
    $request = Request::create('/foo', 'GET');
    $request->cookies->set('locale', 'en');

    (new SetLocale())->handle($request, fn ($r) => response('ok'));

    expect(App::getLocale())->toBe('en');
});

it('forces the root URL to be locale-prefixed for non-default locales', function (): void {
    $request = Request::create('/foo', 'GET');
    $request->attributes->set('_locale', 'en');

    (new SetLocale())->handle($request, fn ($r) => response('ok'));

    expect(url('/foo'))->toContain('/en/foo');
});

it('does not force a prefixed root URL for the default locale', function (): void {
    $request = Request::create('/foo', 'GET');
    $request->attributes->set('_locale', 'hu');

    (new SetLocale())->handle($request, fn ($r) => response('ok'));

    expect(url('/foo'))->not->toContain('/hu/foo');
});

it('falls back to the default locale when nothing matches', function (): void {
    $request = Request::create('/foo', 'GET');

    (new SetLocale())->handle($request, fn ($r) => response('ok'));

    expect(App::getLocale())->toBe('hu');
});
