<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_setiap_tamu_mendapat_token_unik_dan_huruf_kecil(): void
    {
        $guests = Guest::factory()->count(30)->create();

        $this->assertCount(30, $guests->pluck('token')->unique());

        foreach ($guests as $guest) {
            $this->assertMatchesRegularExpression('/^[a-z0-9]{20}$/', $guest->token);
        }
    }

    public function test_token_tidak_bisa_diisi_lewat_mass_assignment(): void
    {
        $guest = Guest::factory()->create();
        $tokenAsli = $guest->token;

        $guest->fill(['token' => 'dibajak']);

        $this->assertSame($tokenAsli, $guest->token);
    }

    public function test_role_tidak_bisa_diisi_lewat_registrasi(): void
    {
        $this->post('/register', [
            'name' => 'Penyusup',
            'email' => 'penyusup@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ]);

        $user = User::where('email', 'penyusup@example.test')->first();

        $this->assertNotNull($user);
        $this->assertFalse($user->isAdmin());
    }
}