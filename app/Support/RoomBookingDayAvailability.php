<?php

namespace App\Support;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;

/**
 * Whether at least one valid, non-overlapping slot pair exists for a room on a date.
 */
final class RoomBookingDayAvailability
{
    public static function hasAnyBookableSlot(Room $room, string $date): bool
    {
        $roomId = (int) $room->id;

        return self::scanSlots(function (string $startSec, string $endSec) use ($roomId, $date): bool {
            return self::productionIntervalBlocked($roomId, $date, $startSec, $endSec);
        });
    }

    /**
     * Session-only sandbox: same semantics using {@see DemoState} reservations.
     */
    public static function hasAnyBookableSlotDemo(int $roomId, string $date): bool
    {
        if (DemoState::findRoom($roomId) === null) {
            return false;
        }

        return self::scanSlots(function (string $startSec, string $endSec) use ($roomId, $date): bool {
            return DemoState::blockingReservationsOverlappingInterval($roomId, $date, $startSec, $endSec, null) !== [];
        });
    }

    /**
     * @param  callable(string $startSec, string $endSec): bool  $intervalIsBlocked  Boundaries HH:MM:SS; returns true if this window overlaps existing bookings (unavailable).
     */
    private static function scanSlots(callable $intervalIsBlocked): bool
    {
        $slots = ReservationBookingWindow::slotLabels();
        $n = count($slots);
        if ($n < 2) {
            return false;
        }

        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $startHi = $slots[$i];
                $endHi = $slots[$j];

                if (ReservationBookingWindow::validationErrors($startHi, $endHi, []) !== []) {
                    continue;
                }

                $startSec = strlen($startHi) === 5 ? $startHi.':00' : $startHi;
                $endSec = strlen($endHi) === 5 ? $endHi.':00' : $endHi;

                if ($startSec >= $endSec) {
                    continue;
                }

                if (! $intervalIsBlocked($startSec, $endSec)) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function productionIntervalBlocked(int $roomId, string $date, string $startSec, string $endSec): bool
    {
        return Reservation::query()
            ->where('room_id', $roomId)
            ->whereDate('date', $date)
            ->where(function (Builder $query) use ($startSec, $endSec) {
                $query
                    ->where('start_time', '<', $endSec)
                    ->where('end_time', '>', $startSec);
            })
            ->exists();
    }
}
