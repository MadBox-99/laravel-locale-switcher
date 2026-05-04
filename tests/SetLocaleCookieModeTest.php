<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use MadBox\LocaleSwitcher\Middleware\SetLocale;

beforeEach(function (): void {
    config()->set('locale-switcher.mode', 'cookie');
    config()->set('locale-switcher.locales', ['en' => 'English', 'hu' => 'Magyar']);
    config()->set('locale-switcher.cookie_name', 'locale');

    App::setLocale('en');
});

it('sets the app locale from a known cookie', function (): void {
    $request = Request::create('/', 'GET');
    $request->cookies->set('locale', 'hu');

    (new SetLocale())->handle($request, fn ($r) => response('ok'));

    expect(App::getLocale())->toBe('hu');
});

it('ignores an unknown cookie value', function (): void {
    $request = Request::create('/', 'GET');
    $request->cookies->set('locale', 'fr');

    (new SetLocale())->handle($request, fn ($r) => response('ok'));

    expect(App::getLocale())->toBe('en');
});

it('leaves the app locale alone when there is no cookie', function (): void {
    $request = Request::create('/', 'GET');

    (new SetLocale())->handle($request, fn ($r) => response('ok'));

    expect(App::getLocale())->toBe('en');
});
