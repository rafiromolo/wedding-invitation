<?php

namespace Database\Seeders;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Rsvp;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command->error('Seeder ini hanya untuk lokal/testing.');
            return;
        }

        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
        ]);

        $client = User::factory()->create([
            'name' => 'Klien Demo',
            'email' => 'klien@example.test',
        ]);

        $invitation = Invitation::factory()->for($client)->create([
            'slug' => 'demo-pernikahan',
            'template' => 'jawa',
            'event_data' => [
                'greeting' => 'Sugeng Rawuh',
                'bride' => ['name' => 'Nancy', 'full_name' => 'Nancy Wheeler', 'parents' => 'Putri dari Bapak Ted Wheeler & Ibu Karen Wheeler'],
                'groom' => ['name' => 'Jonathan', 'full_name' => 'Jonathan Byers', 'parents' => 'Putra dari Bapak Lonnie Byers & Ibu Joyce Byers'],
                'events' => [
                    ['name' => 'Akad', 'starts_at' => '2026-12-12 08:00', 'venue' => 'Starcourt Mall', 'address' => 'Jl. Hawkins Utara No. 21', 'maps_url' => 'https://maps.google.com/...'],
                    ['name' => 'Resepsi', 'starts_at' => '2026-12-12 11:00', 'venue' => 'Starcourt Mall', 'address' => 'Jl. Hawkins Utara No. 21', 'maps_url' => 'https://maps.google.com/...'],
                ],
            ]
        ]);

        $guests = Guest::factory()->count(50)->for($invitation)->create();

        $guests->random(20)->each(
            fn (Guest $guest) => Rsvp::factory()->for($guest)->create()
        );
    }
}