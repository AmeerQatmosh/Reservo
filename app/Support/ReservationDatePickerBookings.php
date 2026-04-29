<?php

namespace App\Support;

use App\Models\Reservation;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Payloads for the reservation date-picker mini-calendar (markers + tooltips).
 */
final class ReservationDatePickerBookings
{
    /**
     * @param  \Illuminate\Database\Eloquent\Collection<int, Reservation>|iterable<Reservation>  $reservations
     * @return list<array<string, mixed>>
     */
    public static function forUserReservationModels(iterable $reservations): array
    {
        /** @var Collection<int, Reservation> $c */
        $c = collect($reservations)
            ->sortBy(fn (Reservation $r): string => (string) ($r->date ?? '').' '.(string) ($r->start_time ?? ''))
            ->values();

        /** @var array<int, array<string, mixed>> $out */
        $out = [];
        $tz = (string) config('app.timezone');

        foreach ($c as $reservation) {
            $out[] = self::payloadFromParts(
                id: (string) $reservation->getKey(),
                dateValue: $reservation->date,
                startTimeStr: (string) $reservation->start_time,
                endTimeStr: (string) $reservation->end_time,
                roomName: $reservation->room?->name ?? __('Room'),
                href: route('reservations.edit', $reservation->getKey()),
                timezone: $tz,
            );
        }

        return array_values($out);
    }

    /**
     * @param  array<int, array<string, mixed>>  $sandboxReservations
     * @return list<array<string, mixed>>
     */
    public static function forDemoSandbox(array $sandboxReservations): array
    {
        /** @var array<int, array<string, mixed>> $out */
        $out = [];
        $tz = (string) config('app.timezone');

        foreach ($sandboxReservations as $r) {
            if (! isset($r['id'])) {
                continue;
            }
            $rid = (string) $r['id'];
            $roomId = (int) ($r['room_id'] ?? 0);
            $room = DemoState::findRoom($roomId);
            /** @phpstan-ignore-next-line */
            $roomTitle = is_array($room) && ! empty($room['name'])
                ? (string) $room['name']
                : __('Room');

            $dateRaw = (string) ($r['date'] ?? '');
            if ($dateRaw === '') {
                continue;
            }

            /** @phpstan-ignore-next-line */
            $href = route('demo.room.show', ['id' => $roomId, 'date' => $dateRaw]);

            $out[] = self::payloadFromParts(
                id: $rid,
                dateValue: $dateRaw,
                startTimeStr: (string) ($r['start_time'] ?? ''),
                endTimeStr: (string) ($r['end_time'] ?? ''),
                roomName: $roomTitle,
                href: $href,
                timezone: $tz,
            );
        }

        /** @phpstan-ignore-next-line */
        usort($out, fn (array $a, array $b): int => (($a['date'] ?? '').($a['start'] ?? '')) <=> (($b['date'] ?? '').($b['start'] ?? '')));

        return array_values($out);
    }

    /**
     * @return array{date: string, id: string, start: string, end: string, room_name: string, href: string, kind: 'past'|'upcoming'}
     */
    private static function payloadFromParts(
        string $id,
        CarbonInterface|string|null $dateValue,
        string $startTimeStr,
        string $endTimeStr,
        string $roomName,
        string $href,
        string $timezone,
    ): array {
        $dateStr = $dateValue instanceof CarbonInterface
            ? $dateValue->timezone($timezone)->format('Y-m-d')
            : Carbon::parse((string) $dateValue, $timezone)->format('Y-m-d');

        $startRaw = substr(trim($startTimeStr), 0, 8);
        $start = strlen($startRaw) >= 5 ? substr($startRaw, 0, 5) : '00:00';
        $endRaw = substr(trim($endTimeStr), 0, 8);
        $end = strlen($endRaw) >= 5 ? substr($endRaw, 0, 5) : '00:00';

        $endWithSec = strlen($endTimeStr) >= 8
            ? substr($endTimeStr, 0, 8)
            : ($end.':00');

        $now = Carbon::now($timezone);

        $bookingEnd = Carbon::parse($dateStr.' '.$endWithSec, $timezone);

        $today = $now->toDateString();
        /** @phpstan-ignore-next-line */
        if ($dateStr < $today) {
            $kind = 'past';
        } elseif ($dateStr > $today) {
            $kind = 'upcoming';
        } else {
            $kind = $bookingEnd->lt($now) ? 'past' : 'upcoming';
        }

        return [
            'date' => $dateStr,
            'id' => $id,
            'start' => $start ?: '00:00',
            'end' => $end ?: '00:00',
            'room_name' => $roomName,
            'href' => $href,
            'kind' => $kind,
        ];
    }
}
