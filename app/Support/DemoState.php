<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

/**
 * Session-only sandbox for portfolio / guest demo. Never writes to Eloquent.
 */
final class DemoState
{
    private const SESSION_KEY = 'reservo_demo';

    public static function enabled(): bool
    {
        return (bool) config('reservo.demo_enabled');
    }

    public static function active(): bool
    {
        return (bool) Session::get(self::SESSION_KEY.'.active', false);
    }

    public static function role(): ?string
    {
        $r = Session::get(self::SESSION_KEY.'.role');

        return is_string($r) ? $r : null;
    }

    public static function exitDemo(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public static function bootstrap(string $role): void
    {
        if (! in_array($role, ['user', 'admin', 'super_admin'], true)) {
            return;
        }

        $existing = Session::get(self::SESSION_KEY, []);
        $rooms = $existing['rooms'] ?? null;

        if (! is_array($rooms) || $rooms === []) {
            $seed = self::defaultRooms();
            Session::put(self::SESSION_KEY, [
                'active' => true,
                'role' => $role,
                'rooms' => $seed['rooms'],
                'reservations' => [],
                'next_room_id' => $seed['next_room_id'],
                'next_reservation_id' => 1,
            ]);

            return;
        }

        Session::put(self::SESSION_KEY.'.active', true);
        Session::put(self::SESSION_KEY.'.role', $role);
    }

    public static function setRole(string $role): void
    {
        if (! in_array($role, ['user', 'admin', 'super_admin'], true) || ! self::active()) {
            return;
        }

        Session::put(self::SESSION_KEY.'.role', $role);
    }

    /**
     * @return array{rooms: list<array<string, mixed>>, next_room_id: int}
     */
    private static function defaultRooms(): array
    {
        /** @see database/data/demo_rooms.php — match production image quality where IDs align. */
        $u = static fn (string $id): string => 'https://images.unsplash.com/photo-'.$id.'?auto=format&fit=crop&w=2400&q=90';

        return [
            'rooms' => [
                [
                    'id' => 1,
                    'name' => 'Focus Pod',
                    'capacity' => 4,
                    'location' => 'Meridian House · East wing · 5th floor · Quiet zone',
                    'description' => "Same footprint as Focus Pod 1 in the live directory: whiteboard wall, table for four, and a solid door for confidential calls and interviews.\n\nFair-use: max four hours continuous without a gap.",
                    'size_sqm' => 12,
                    'hourly_rate' => 28.00,
                    'amenities' => ['Whiteboard wall', '27" monitor + HDMI', 'Ethernet drop', 'Acoustic door'],
                    'image_url' => $u('1572025442811-aa5146a780fb'),
                ],
                [
                    'id' => 2,
                    'name' => 'Studio B',
                    'capacity' => 24,
                    'location' => 'Meridian House · Annex · Ground floor · Studio wing',
                    'description' => "Mirrors Studio B — Workshop: classroom-style tables, presenter display, and whiteboards for sprints, onboarding, and facilitated training.\n\nCatering staging uses the annex kitchenette.",
                    'size_sqm' => 78,
                    'hourly_rate' => 55.00,
                    'amenities' => ['85" display', 'Wireless presentation', 'Wall whiteboards (2)', 'Adjacent kitchenette'],
                    'image_url' => $u('1762176264161-09219da49794'),
                ],
                [
                    'id' => 3,
                    'name' => 'Town Hall',
                    'capacity' => 60,
                    'location' => 'Meridian House · Events Center · Hall A',
                    'description' => "Tiered theater seating toward a raised stage—built for all-hands, AMAs, and large onboarding. House lights and projection are fixed; hybrid AV is available with a tech booking.\n\nMatches the production Town Hall listing.",
                    'size_sqm' => 185,
                    'hourly_rate' => 120.00,
                    'amenities' => ['Stage + lighting', 'Front projection', 'Wireless handheld mics (2)', 'Accessible seating rows'],
                    'image_url' => $u('1771911654088-36080143c3bd'),
                ],
                [
                    'id' => 4,
                    'name' => 'Glass Boardroom',
                    'capacity' => 10,
                    'location' => 'Meridian House · South Tower · 8th floor · Corner NE',
                    'description' => "Bright corner room with full-height glazing and a single conference table—similar character to Harbor View Lab but sized for executive huddles and client reviews.\n\nSheer shades for glare; video bar pre-mounted under the display.",
                    'size_sqm' => 28,
                    'hourly_rate' => 75.00,
                    'amenities' => ['Harbor-facing glazing', '65" display', 'Video bar', 'USB-C / HDMI'],
                    'image_url' => $u('1685602729266-9fa302fd7121'),
                ],
                [
                    'id' => 5,
                    'name' => 'Meridian Lounge',
                    'capacity' => 12,
                    'location' => 'Meridian House · Main campus · Level 3 · Social hub',
                    'description' => "Matches the seeded Meridian Lounge: sofas, armchairs, and a long window wall with a coffee counter—built for informal catch-ups, not a single conference table.\n\nUse Focus Pod for confidential HR or video calls.",
                    'size_sqm' => 95,
                    'hourly_rate' => 35.00,
                    'amenities' => ['Window wall', 'Modular sofas + armchairs', 'Coffee station', '65" display (casting)', 'Acoustic clouds'],
                    'image_url' => $u('1758448511255-ac2a24a135d7'),
                ],
                [
                    'id' => 6,
                    'name' => 'Interview Suite',
                    'capacity' => 3,
                    'location' => 'Meridian House · East wing · 5th floor · HR suite',
                    'description' => "Small grey-toned meeting room with a central table—tuned for two interviewers and a candidate, or a quiet three-person legal / HR discussion.\n\nNeutral walls and even overhead light; no decorative clutter on camera.",
                    'size_sqm' => 10,
                    'hourly_rate' => 22.00,
                    'amenities' => ['Wall monitor + HDMI', 'Sound-dampening finishes', 'Ethernet', 'Booking panel outside door'],
                    'image_url' => $u('1637665599155-abfcf9fc3f78'),
                ],
                [
                    'id' => 7,
                    'name' => 'Boardroom',
                    'capacity' => 14,
                    'location' => 'Meridian House · North Tower · 12th floor · Suite 1204',
                    'description' => "Same specification as The Boardroom in the database seed: long white table, black executive chairs, and a wall-mounted display—suited to formal votes, steering committees, and client sign-offs.\n\nExecutive floor; catering uses the rear credenza zone.",
                    'size_sqm' => 52,
                    'hourly_rate' => 95.00,
                    'amenities' => ['Wall-mounted display', 'Conference speakerphone', 'Motorized blinds', 'Climate control'],
                    'image_url' => $u('1637665662134-db459c1bbb46'),
                ],
            ],
            'next_room_id' => 8,
        ];
    }

    /**
     * Read-only sample directory for the sandbox “Users” admin screen (not real accounts).
     *
     * @return list<array{id: int, name: string, email: string, role: string}>
     */
    public static function sampleDirectoryUsers(): array
    {
        return [
            ['id' => 1, 'name' => 'Riley Chen', 'email' => 'riley.chen@example.test', 'role' => 'super_admin'],
            ['id' => 2, 'name' => 'Jordan Okonkwo', 'email' => 'jordan.okonkwo@example.test', 'role' => 'admin'],
            ['id' => 3, 'name' => 'Alex Rivera', 'email' => 'alex.rivera@example.test', 'role' => 'admin'],
            ['id' => 4, 'name' => 'Sam Patel', 'email' => 'sam.patel@example.test', 'role' => 'user'],
            ['id' => 5, 'name' => 'Taylor Brooks', 'email' => 'taylor.brooks@example.test', 'role' => 'user'],
            ['id' => 6, 'name' => 'Casey Nguyen', 'email' => 'casey.nguyen@example.test', 'role' => 'user'],
            ['id' => 7, 'name' => 'Morgan Ellis', 'email' => 'morgan.ellis@example.test', 'role' => 'user'],
            ['id' => 8, 'name' => 'Jamie Foster', 'email' => 'jamie.foster@example.test', 'role' => 'user'],
        ];
    }

    /**
     * @param  array<string, mixed>  $room
     * @return array<string, mixed>
     */
    public static function normalizeRoom(array $room): array
    {
        return array_merge([
            'description' => '',
            'size_sqm' => null,
            'amenities' => [],
            'hourly_rate' => null,
            'image_url' => null,
            'location' => '',
        ], $room);
    }

    public static function hourlyRateLabel(?float $rate): ?string
    {
        if ($rate === null) {
            return null;
        }

        return '$'.number_format((float) $rate, 2).'/hr';
    }

    /**
     * @param  array<string, mixed>  $reservation
     * @param  array<string, mixed>|null  $room
     */
    public static function reservationEstimateLabel(array $reservation, ?array $room): ?string
    {
        if ($room === null || ($room['hourly_rate'] ?? null) === null) {
            return null;
        }

        $hours = self::reservationDurationHours(
            (string) ($reservation['date'] ?? ''),
            (string) ($reservation['start_time'] ?? ''),
            (string) ($reservation['end_time'] ?? ''),
        );
        $total = round($hours * (float) $room['hourly_rate'], 2);

        return '$'.number_format($total, 2);
    }

    private static function reservationDurationHours(string $date, string $start, string $end): float
    {
        $startAt = Carbon::parse($date.' '.substr($start, 0, 8));
        $endAt = Carbon::parse($date.' '.substr($end, 0, 8));

        return max(0.0, $startAt->diffInMinutes($endAt) / 60);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function rooms(): array
    {
        $r = Session::get(self::SESSION_KEY.'.rooms', []);

        return is_array($r) ? $r : [];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function reservations(): array
    {
        $r = Session::get(self::SESSION_KEY.'.reservations', []);

        return is_array($r) ? $r : [];
    }

    public static function findRoom(int $id): ?array
    {
        foreach (self::rooms() as $room) {
            if ((int) ($room['id'] ?? 0) === $id) {
                return self::normalizeRoom($room);
            }
        }

        return null;
    }

    /**
     * @return array<string, string>|null error key => message
     */
    public static function addReservation(int $roomId, string $date, string $startHi, string $endHi, ?int $ignoreId = null): ?array
    {
        $bookingErrors = ReservationBookingWindow::validationErrors($startHi, $endHi, []);
        if ($bookingErrors !== []) {
            return ['time' => $bookingErrors['start_time'][0] ?? $bookingErrors['end_time'][0] ?? 'Invalid time.'];
        }

        if (self::findRoom($roomId) === null) {
            return ['room' => 'Room not found.'];
        }

        $startSec = $startHi.':00';
        $endSec = $endHi.':00';

        if ($startSec >= $endSec) {
            return ['time' => 'End time must be after start time.'];
        }

        if (self::reservationOverlaps($roomId, $date, $startSec, $endSec, $ignoreId)) {
            return ['overlap' => 'This time overlaps another booking in the demo.'];
        }

        $id = (int) Session::get(self::SESSION_KEY.'.next_reservation_id', 1);
        $list = self::reservations();
        $list[] = [
            'id' => $id,
            'room_id' => $roomId,
            'date' => $date,
            'start_time' => $startSec,
            'end_time' => $endSec,
            'label' => 'Demo guest',
        ];
        Session::put(self::SESSION_KEY.'.reservations', $list);
        Session::put(self::SESSION_KEY.'.next_reservation_id', $id + 1);

        return null;
    }

    public static function deleteReservation(int $id): bool
    {
        $list = self::reservations();
        $found = false;
        $next = [];
        foreach ($list as $row) {
            if ((int) ($row['id'] ?? 0) === $id) {
                $found = true;

                continue;
            }
            $next[] = $row;
        }
        if ($found) {
            Session::put(self::SESSION_KEY.'.reservations', $next);
        }

        return $found;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string>|null
     */
    public static function addRoom(array $data): ?array
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            return ['name' => 'Name is required.'];
        }

        $capacity = (int) ($data['capacity'] ?? 0);
        if ($capacity < 1) {
            return ['capacity' => 'Capacity must be at least 1.'];
        }

        $description = trim((string) ($data['description'] ?? ''));
        if ($description === '') {
            return ['description' => 'Description is required.'];
        }

        $location = trim((string) ($data['location'] ?? ''));

        $sizeSqm = $data['size_sqm'] ?? null;
        if ($sizeSqm === '' || $sizeSqm === null) {
            $sizeSqm = null;
        } else {
            $sizeSqm = (int) $sizeSqm;
            if ($sizeSqm < 1) {
                return ['size_sqm' => 'Size must be at least 1 m².'];
            }
        }

        $hourly = $data['hourly_rate'] ?? null;
        if ($hourly === '' || $hourly === null) {
            $hourly = null;
        } else {
            $hourly = (float) $hourly;
            if ($hourly < 0) {
                return ['hourly_rate' => 'Hourly rate cannot be negative.'];
            }
        }

        $imageUrl = trim((string) ($data['image_url'] ?? ''));
        $imageUrl = $imageUrl !== '' ? $imageUrl : null;

        $amenities = $data['amenities'] ?? [];
        if (! is_array($amenities)) {
            $amenities = [];
        }

        $id = (int) Session::get(self::SESSION_KEY.'.next_room_id', 10);
        $rooms = self::rooms();
        $rooms[] = self::normalizeRoom([
            'id' => $id,
            'name' => $name,
            'capacity' => $capacity,
            'location' => $location,
            'description' => $description,
            'size_sqm' => $sizeSqm,
            'hourly_rate' => $hourly,
            'amenities' => $amenities,
            'image_url' => $imageUrl,
        ]);
        Session::put(self::SESSION_KEY.'.rooms', $rooms);
        Session::put(self::SESSION_KEY.'.next_room_id', $id + 1);

        return null;
    }

    public static function deleteRoom(int $id): bool
    {
        $rooms = self::rooms();
        $found = false;
        $next = [];
        foreach ($rooms as $room) {
            if ((int) ($room['id'] ?? 0) === $id) {
                $found = true;

                continue;
            }
            $next[] = $room;
        }
        if (! $found) {
            return false;
        }
        Session::put(self::SESSION_KEY.'.rooms', $next);

        $res = self::reservations();
        Session::put(self::SESSION_KEY.'.reservations', array_values(array_filter(
            $res,
            fn (array $row): bool => (int) ($row['room_id'] ?? 0) !== $id
        )));

        return true;
    }

    public static function reservationsForRoomOnDate(int $roomId, string $date): array
    {
        return array_values(array_filter(
            self::reservations(),
            fn (array $row): bool => (int) ($row['room_id'] ?? 0) === $roomId && ($row['date'] ?? '') === $date
        ));
    }

    private static function reservationOverlaps(int $roomId, string $date, string $start, string $end, ?int $ignoreId): bool
    {
        foreach (self::reservations() as $row) {
            if ((int) ($row['room_id'] ?? 0) !== $roomId) {
                continue;
            }
            if (($row['date'] ?? '') !== $date) {
                continue;
            }
            if ($ignoreId !== null && (int) ($row['id'] ?? 0) === $ignoreId) {
                continue;
            }
            $rs = (string) ($row['start_time'] ?? '');
            $re = (string) ($row['end_time'] ?? '');
            if ($rs < $end && $re > $start) {
                return true;
            }
        }

        return false;
    }

    public static function canUser(): bool
    {
        return in_array(self::role(), ['user', 'admin', 'super_admin'], true);
    }

    public static function canAdmin(): bool
    {
        return in_array(self::role(), ['admin', 'super_admin'], true);
    }

    public static function canSuperAdmin(): bool
    {
        return self::role() === 'super_admin';
    }
}
