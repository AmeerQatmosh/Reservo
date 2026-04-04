<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Loads the full room catalog from database/data/demo_rooms.php and removes any DB rows
     * not in that file (e.g. old factory-generated names). Photos use w=2400 in the data file.
     */
    public function run(): void
    {
        $this->repairRemovedUnsplashPhotos();

        /** @var array<int, array<string, mixed>> $rooms */
        $rooms = require database_path('data/demo_rooms.php');
        $curatedNames = array_column($rooms, 'name');

        foreach ($rooms as $room) {
            if (! array_key_exists('hourly_rate', $room)) {
                $room['hourly_rate'] = round(mt_rand(1800, 5200) / 100, 2);
            }
            Room::query()->updateOrCreate(
                ['name' => $room['name']],
                $room
            );
        }

        Room::query()->whereNotIn('name', $curatedNames)->delete();
    }

    /**
     * Unsplash/imgix occasionally returns 404 for older photo-* paths; swap known dead IDs for working ones.
     */
    private function repairRemovedUnsplashPhotos(): void
    {
        $replacements = [
            'photo-1562664377-c2a586332a4a' => 'https://images.unsplash.com/photo-1637665662134-db459c1bbb46?auto=format&fit=crop&w=2400&q=90',
            'photo-1522199930739-555875f9cfa6' => 'https://images.unsplash.com/photo-1572025442811-aa5146a780fb?auto=format&fit=crop&w=2400&q=90',
            'photo-1504384305370-3eead8168b2e' => 'https://images.unsplash.com/photo-1685602805987-043a1d78f28c?auto=format&fit=crop&w=2400&q=90',
            'photo-1758369636912-ea53bc2bad5d' => 'https://images.unsplash.com/photo-1774763317427-54e97ec31d9d?auto=format&fit=crop&w=2400&q=90',
            'photo-1685602729758-cecb632237a6' => 'https://images.unsplash.com/photo-1768225709733-18c9f264bc5f?auto=format&fit=crop&w=2400&q=90',
        ];

        foreach ($replacements as $brokenFragment => $imageUrl) {
            Room::query()
                ->where('image_url', 'like', '%'.$brokenFragment.'%')
                ->update(['image_url' => $imageUrl]);
        }
    }
}
