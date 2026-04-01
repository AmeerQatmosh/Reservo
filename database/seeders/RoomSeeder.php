<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Loads curated demo rooms from database/data/demo_rooms.php (Unsplash imagery).
     */
    public function run(): void
    {
        $this->repairRemovedUnsplashPhotos();

        /** @var array<int, array<string, mixed>> $rooms */
        $rooms = require database_path('data/demo_rooms.php');

        foreach ($rooms as $room) {
            if (! array_key_exists('hourly_rate', $room)) {
                $room['hourly_rate'] = fake()->randomFloat(2, 18, 52);
            }
            Room::query()->updateOrCreate(
                ['name' => $room['name']],
                $room
            );
        }

        $targetTotal = 14;
        $shortfall = $targetTotal - Room::query()->count();

        if ($shortfall > 0) {
            Room::factory()->count($shortfall)->create();
        }
    }

    /**
     * Unsplash/imgix occasionally returns 404 for older photo-* paths; swap known dead IDs for working ones.
     */
    private function repairRemovedUnsplashPhotos(): void
    {
        $replacements = [
            'photo-1562664377-c2a586332a4a' => 'https://images.unsplash.com/photo-1544984243-ec57ea16fe25?auto=format&fit=crop&w=1600&q=85',
            'photo-1522199930739-555875f9cfa6' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1600&q=85',
            'photo-1504384305370-3eead8168b2e' => 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1600&q=85',
        ];

        foreach ($replacements as $brokenFragment => $imageUrl) {
            Room::query()
                ->where('image_url', 'like', '%'.$brokenFragment.'%')
                ->update(['image_url' => $imageUrl]);
        }
    }
}
