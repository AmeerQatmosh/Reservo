<?php

use App\Models\Room;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Rooms created before hourly_rate existed stay NULL and show no price.
     * Assign a varied demo rate (mid “small room” band, not market-calibrated).
     */
    public function up(): void
    {
        foreach (Room::query()->whereNull('hourly_rate')->cursor() as $room) {
            $room->hourly_rate = round(15 + (($room->id * 41) % 32) + (($room->id * 3) % 100) / 100, 2);
            $room->saveQuietly();
        }
    }

    public function down(): void
    {
        // Intentionally empty: cannot know which rows were backfilled vs user-set.
    }
};
