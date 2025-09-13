<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

it('displays the tokens page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/tokens')
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('settings/tokens')
            ->has('tokens')
        );
});

it('creates a new token', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/settings/tokens', [
            'name' => 'Test Token',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('token');
    $response->assertSessionHas('message');

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_type' => User::class,
        'tokenable_id' => $user->id,
        'name' => 'Test Token',
    ]);
});

it('creates a token with expiration date', function () {
    $user = User::factory()->create();
    $expiresAt = now()->addDays(30);

    $response = $this->actingAs($user)
        ->post('/settings/tokens', [
            'name' => 'Expiring Token',
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);

    $response->assertRedirect();

    $token = PersonalAccessToken::where('name', 'Expiring Token')->first();
    expect($token)->not->toBeNull();
    expect($token->expires_at)->not->toBeNull();
    expect($token->expires_at->format('Y-m-d H:i:s'))->toBe($expiresAt->format('Y-m-d H:i:s'));
});

it('validates token name is required', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/settings/tokens', []);

    $response->assertSessionHasErrors(['name']);
});

it('validates token name is not too long', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/settings/tokens', [
            'name' => str_repeat('a', 256),
        ]);

    $response->assertSessionHasErrors(['name']);
});

it('validates expiration date is in the future', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/settings/tokens', [
            'name' => 'Test Token',
            'expires_at' => now()->subDay()->format('Y-m-d H:i:s'),
        ]);

    $response->assertSessionHasErrors(['expires_at']);
});

it('deletes a token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('Test Token');

    $response = $this->actingAs($user)
        ->delete("/settings/tokens/{$token->accessToken->id}");

    $response->assertRedirect();
    $response->assertSessionHas('message', 'Token deleted successfully.');

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $token->accessToken->id,
    ]);
});

it('cannot delete another users token', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $token = $user2->createToken('Test Token');

    $response = $this->actingAs($user1)
        ->delete("/settings/tokens/{$token->accessToken->id}");

    $response->assertNotFound();
});

it('revokes all tokens', function () {
    $user = User::factory()->create();
    $user->createToken('Token 1');
    $user->createToken('Token 2');

    $response = $this->actingAs($user)
        ->delete('/settings/tokens');

    $response->assertRedirect();
    $response->assertSessionHas('message', 'All tokens have been revoked.');

    expect($user->tokens()->count())->toBe(0);
});

it('requires authentication', function () {
    $this->get('/settings/tokens')
        ->assertRedirect('/login');

    $this->post('/settings/tokens', [
        'name' => 'Test Token',
    ])->assertRedirect('/login');

    $this->delete('/settings/tokens/1')
        ->assertRedirect('/login');

    $this->delete('/settings/tokens')
        ->assertRedirect('/login');
});
