<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use MadBox\LocaleSwitcher\Middleware\SetLocale as NewSetLocale;
use MadBox\LocaleSwitcher\SetLocale;

it('still resolves the legacy SetLocale class', function (): void {
    expect(class_exists(SetLocale::class))->toBeTrue()
        ->and(is_a(SetLocale::class, NewSetLocale::class, true))->toBeTrue();
});

it('legacy SetLocale still works as a cookie-mode middleware', function (): void {
    config()->set('locale-switcher.mode', 'cookie');
    config()->set('locale-switcher.locales', ['en' => 'English', 'hu' => 'Magyar']);
    config()->set('locale-switcher.cookie_name', 'locale');

    App::setLocale('en');

    $request = Request::create('/', 'GET');
    $request->cookies->set('locale', 'hu');

    (new SetLocale())->handle($request, fn ($r) => response('ok'));

    expect(App::getLocale())->toBe('hu');
});
