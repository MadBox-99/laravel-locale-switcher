<?php

declare(strict_types=1);

beforeEach(function (): void {
    config()->set('locale-switcher.mode', 'url_prefix');
    config()->set('locale-switcher.locales', ['en' => 'English', 'hu' => 'Magyar', 'de' => 'Deutsch']);
    config()->set('locale-switcher.default_locale', 'hu');
    config()->set('app.url', 'http://localhost');
});

it('redirects to the locale-prefixed previous path without setting a cookie', function (): void {
    $response = $this
        ->from('http://localhost/articles')
        ->get('/language/en');

    $response
        ->assertRedirect('http://localhost/en/articles')
        ->assertCookieMissing('locale');
});

it('redirects to the un-prefixed path for the default locale', function (): void {
    $response = $this
        ->from('http://localhost/en/articles')
        ->get('/language/hu');

    $response->assertRedirect('http://localhost/articles');
});

it('falls back to root when previous URL is on a different host', function (): void {
    $response = $this
        ->from('http://other-site.test/page')
        ->get('/language/en');

    $response->assertRedirect('http://localhost/en/');
});

it('rejects an unknown locale', function (): void {
    $response = $this->get('/language/fr');

    $response->assertStatus(400);
});
