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
        ]);

        $guests = Guest::factory()->count(50)->for($invitation)->create();

        $guests->random(20)->each(
            fn (Guest $guest) => Rsvp::factory()->for($guest)->create()
        );
    }
}