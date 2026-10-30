<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateCommercialTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_verified_commercial_with_a_hashed_password(): void
    {
        $this->artisan('app:create-commercial', ['email' => 'commercial@example.test'])
            ->expectsQuestion('Mot de passe (12 caractères minimum)', 'Demo-Secret-1234')
            ->expectsQuestion('Confirmer le mot de passe', 'Demo-Secret-1234')
            ->assertSuccessful();

        $user = User::where('email', 'commercial@example.test')->firstOrFail();
        $this->assertSame('commercial', $user->role);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check('Demo-Secret-1234', $user->password));
    }

    public function test_it_never_promotes_an_existing_client(): void
    {
        $client = User::factory()->create(['email' => 'client@example.test']);
        $hash = $client->password;

        $this->artisan('app:create-commercial', ['email' => $client->email])
            ->expectsQuestion('Mot de passe (12 caractères minimum)', 'Demo-Secret-1234')
            ->expectsQuestion('Confirmer le mot de passe', 'Demo-Secret-1234')
            ->assertFailed();

        $this->assertSame('client', $client->fresh()->role);
        $this->assertSame($hash, $client->fresh()->password);
    }

    public function test_it_rejects_an_unconfirmed_or_short_password(): void
    {
        $this->artisan('app:create-commercial', ['email' => 'commercial@example.test'])
            ->expectsQuestion('Mot de passe (12 caractères minimum)', 'short')
            ->expectsQuestion('Confirmer le mot de passe', 'different')
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }
}
