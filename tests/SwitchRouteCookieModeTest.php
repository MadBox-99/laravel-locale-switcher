<?php

declare(strict_types=1);

beforeEach(function (): void {
    config()->set('locale-switcher.mode', 'cookie');
    config()->set('locale-switcher.locales', ['en' => 'English', 'hu' => 'Magyar']);
    config()->set('app.url', 'http://localhost');
});

it('sets a cookie and redirects to the previous URL', function (): void {
    $response = $this
        ->from('http://localhost/articles')
        ->get('/language/en');

    $response
        ->assertRedirect('http://localhost/articles')
        ->assertCookie('locale', 'en');
});

it('returns 400 for an unknown locale', function (): void {
    $response = $this->get('/language/fr');

    $response->assertStatus(400);
});

it('redirects to root when previous URL is on a different host', function (): void {
    $response = $this
        ->from('http://other-site.test/page')
        ->get('/language/en');

    $response->assertRedirect('/');
});
