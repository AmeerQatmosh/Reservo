<?php

namespace App\Support;

use App\Models\Reservation;
use Illuminate\Support\Collection;

/**
 * User-facing copy when a reservation would overlap existing bookings for the same room/date.
 */
final class ReservationOverlapMessaging
{
    /**
     * @param  Collection<int, Reservation>  $blocking
     */
    public static function forDatabaseReservations(array $validated, Collection $blocking): string
    {
        $chosenStart = substr($validated['start_time'], 0, 5);
        $chosenEnd = substr($validated['end_time'], 0, 5);
        $date = $validated['date'];

        if ($blocking->isEmpty()) {
            return __('This time slot is already booked. Choose another time or another day.');
        }

        $ranges = $blocking
            ->map(function (Reservation $r): string {
                return substr((string) $r->start_time, 0, 5).'–'.substr((string) $r->end_time, 0, 5);
            })
            ->unique()
            ->values()
            ->all();

        $existing = implode(', ', $ranges);

        return __(
            'You can’t reserve :slot on :date for this room—it overlaps existing booking(s): :existing. Pick a non-overlapping time range or choose another day.',
            ['slot' => $chosenStart.'–'.$chosenEnd, 'date' => $date, 'existing' => $existing],
        );
    }

    /**
     * @param  list<array<string, mixed>>  $blocking
     */
    public static function forSandboxRows(string $date, string $startHi, string $endHi, array $blocking): string
    {
        if ($blocking === []) {
            return __('This time overlaps another booking in the sandbox. Try another time or day.');
        }

        $ranges = [];
        foreach ($blocking as $row) {
            $rs = substr((string) ($row['start_time'] ?? ''), 0, 5);
            $re = substr((string) ($row['end_time'] ?? ''), 0, 5);
            if ($rs !== '' && $re !== '') {
                $ranges[] = $rs.'–'.$re;
            }
        }

        $existing = implode(', ', array_unique($ranges));

        return __(
            'You can’t reserve :slot on :date in the sandbox—it overlaps (:existing). Pick a non-overlapping time range or another day.',
            ['slot' => $startHi.'–'.$endHi, 'date' => $date, 'existing' => $existing],
        );
    }
}
