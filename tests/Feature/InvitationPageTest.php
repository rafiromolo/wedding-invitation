<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_valid_menampilkan_nama_tamu(): void
    {
        $invitation = Invitation::factory()->create();
        $guest = Guest::factory()->for($invitation)->create(['name' => 'Rafi Maulana']);

        $this->get("/i/{$invitation->slug}/{$guest->token}")
            ->assertOk()
            ->assertSee('Rafi Maulana');
    }

    public function test_tanpa_token_menampilkan_undangan_umum(): void
    {
        $invitation = Invitation::factory()->create();
        Guest::factory()->for($invitation)->create(['name' => 'Rafi Maulana']);

        $this->get("/i/{$invitation->slug}")
            ->assertOk()
            ->assertDontSee('Rafi Maulana')
            ->assertSee($invitation->title);
    }

    public function test_token_salah_menampilkan_undangan_umum_tanpa_nama(): void
    {
        $invitation = Invitation::factory()->create();
        Guest::factory()->for($invitation)->create(['name' => 'Rafi Maulana']);

        $this->get("/i/{$invitation->slug}/" . str_repeat('a', 20))
            ->assertOk()
            ->assertDontSee('Rafi Maulana');

        $this->get("/i/{$invitation->slug}/token-asal-asalan")
            ->assertOk()
            ->assertDontSee('Rafi Maulana');
    }

    public function test_token_milik_undangan_lain_tidak_menampilkan_nama(): void
    {
        $a = Invitation::factory()->create();
        $b = Invitation::factory()->create();
        $tamuB = Guest::factory()->for($b)->create(['name' => 'Tamu Rahasia B']);

        $this->get("/i/{$a->slug}/{$tamuB->token}")
            ->assertOk()
            ->assertDontSee('Tamu Rahasia B');

        $this->assertDatabaseCount('guest_views', 0);
    }

    public function test_token_tamu_yang_dihapus_tidak_berlaku(): void
    {
        $invitation = Invitation::factory()->create();
        $guest = Guest::factory()->for($invitation)->create(['name' => 'Rafi Maulana']);
        $token = $guest->token;
        $guest->delete();

        $this->get("/i/{$invitation->slug}/{$token}")
            ->assertOk()
            ->assertDontSee('Rafi Maulana');
    }

    public function test_slug_tidak_ada_menghasilkan_404(): void
    {
        $this->get('/i/slug-tidak-ada')->assertNotFound();
    }

    public function test_undangan_kedaluwarsa_menghasilkan_410(): void
    {
        $invitation = Invitation::factory()->create(['expires_at' => now()->subDay()]);
        $guest = Guest::factory()->for($invitation)->create();

        $this->get("/i/{$invitation->slug}/{$guest->token}")->assertStatus(410);
    }

    public function test_nama_tamu_di_escape_dari_html(): void
    {
        $invitation = Invitation::factory()->create();
        $payload = '<script>alert(1)</script>';
        $guest = Guest::factory()->for($invitation)->create(['name' => $payload]);

        $this->get("/i/{$invitation->slug}/{$guest->token}")
            ->assertOk()
            ->assertDontSee($payload, false)
            ->assertSee(e($payload), false);
    }

    public function test_header_keamanan_terpasang(): void
    {
        $invitation = Invitation::factory()->create();

        $this->get("/i/{$invitation->slug}")
            ->assertHeader('Referrer-Policy', 'no-referrer')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_pembukaan_dicatat_sekali_dalam_sepuluh_menit(): void
    {
        $invitation = Invitation::factory()->create();
        $guest = Guest::factory()->for($invitation)->create();

        $this->get("/i/{$invitation->slug}/{$guest->token}");
        $this->get("/i/{$invitation->slug}/{$guest->token}");

        $this->assertDatabaseCount('guest_views', 1);
    }

    public function test_bot_pratinjau_tidak_dihitung_sebagai_pembukaan(): void
    {
        $invitation = Invitation::factory()->create();
        $guest = Guest::factory()->for($invitation)->create();

        $this->get("/i/{$invitation->slug}/{$guest->token}", ['User-Agent' => 'WhatsApp/2.23.20'])
            ->assertOk();

        $this->assertDatabaseCount('guest_views', 0);
    }

    public function test_template_jawa_menampilkan_nama_tamu_dan_acara(): void
    {
        $invitation = Invitation::factory()->create([
            'template' => 'jawa',
            'event_data' => [
                'bride' => ['name' => 'Sekar', 'full_name' => 'Sekar Ayu'],
                'groom' => ['name' => 'Bima', 'full_name' => 'Bima Aditya'],
                'events' => [['name' => 'Resepsi', 'venue' => 'Gedung A']],
            ],
        ]);
        $guest = Guest::factory()->for($invitation)->create(['name' => 'Rafi Maulana']);

        $this->get("/i/{$invitation->slug}/{$guest->token}")
            ->assertOk()
            ->assertSee('Rafi Maulana')
            ->assertSee('Resepsi');
    }

    public function test_tautan_peta_non_https_tidak_dirender(): void
    {
        $invitation = Invitation::factory()->create([
            'template' => 'jawa',
            'event_data' => ['events' => [['name' => 'Resepsi', 'maps_url' => 'javascript:alert(1)']]],
        ]);

        $this->get("/i/{$invitation->slug}")
            ->assertOk()
            ->assertDontSee('javascript:alert(1)', false);
    }

    public function test_template_tidak_dikenal_memakai_classic(): void
    {
        $invitation = Invitation::factory()->create(['template' => '../../rahasia']);

        $this->get("/i/{$invitation->slug}")->assertOk();
    }
}