<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Sample reservations for the demo user so availability views look lived-in.
     */
    public function run(): void
    {
        $user = User::query()->where('email', 'test@example.com')->first();

        if ($user === null) {
            return;
        }

        Reservation::query()->where('user_id', $user->id)->delete();

        $rooms = Room::query()->orderBy('id')->get();

        if ($rooms->isEmpty()) {
            return;
        }

        $timeBlocks = [
            ['09:00:00', '10:00:00'],
            ['10:30:00', '11:30:00'],
            ['13:00:00', '14:00:00'],
            ['14:30:00', '15:30:00'],
            ['16:00:00', '17:00:00'],
        ];

        $day = Carbon::today();
        $maxDays = 14;
        $target = min(32, $rooms->count() * 6);
        $added = 0;

        for ($i = 0; $i < $maxDays; $i++) {
            $current = $day->copy()->addDays($i);

            if ($current->isWeekend()) {
                continue;
            }

            foreach ($rooms as $roomIndex => $room) {
                if ($added >= $target) {
                    break 2;
                }

                $block = $timeBlocks[($roomIndex + $i) % count($timeBlocks)];

                Reservation::query()->create([
                    'user_id' => $user->id,
                    'room_id' => $room->id,
                    'date' => $current->toDateString(),
                    'start_time' => $block[0],
                    'end_time' => $block[1],
                ]);

                $added++;
            }
        }
    }
}
