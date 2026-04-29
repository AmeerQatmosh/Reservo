<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * In-memory room list filters for the guest demo (session rooms), mirroring RoomListing rules.
 */
final class DemoRoomListing
{
    /**
     * @param  list<array<string, mixed>>  $rooms
     * @return array{locations: Collection<int, string>, amenities: Collection<int, string>}
     */
    public static function filterOptionsFromRooms(array $rooms): array
    {
        $locations = collect($rooms)
            ->pluck('location')
            ->filter(fn ($l) => is_string($l) && $l !== '')
            ->unique()
            ->sort()
            ->values();

        $amenities = collect($rooms)
            ->pluck('amenities')
            ->filter(fn ($a) => is_array($a))
            ->flatten()
            ->filter(fn ($a) => is_string($a) && $a !== '')
            ->unique()
            ->sort()
            ->values();

        return [
            'locations' => $locations,
            'amenities' => $amenities,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rooms
     * @return array{rooms: list<array<string, mixed>>, filters: array<string, mixed>}
     */
    public static function applyRequestFilters(array $rooms, Request $request): array
    {
        $search = trim((string) $request->input('search', ''));
        $minCap = $request->input('min_capacity');
        $maxCap = $request->input('max_capacity');
        $minSize = $request->input('min_size_sqm');
        $maxSize = $request->input('max_size_sqm');
        $location = trim((string) $request->input('location', ''));
        $amenity = trim((string) $request->input('amenity', ''));
        $minHourly = $request->input('min_hourly_rate');
        $maxHourly = $request->input('max_hourly_rate');
        $hasPhoto = $request->boolean('has_photo');
        $sort = $request->input('sort', 'name');
        $sortKey = is_string($sort) ? $sort : 'name';

        $filtered = array_values(array_filter($rooms, function (array $room) use ($search, $minCap, $maxCap, $minSize, $maxSize, $minHourly, $maxHourly, $location, $amenity, $hasPhoto): bool {
            $room = DemoState::normalizeRoom($room);

            if ($search !== '') {
                $needle = Str::lower($search);
                $hay = Str::lower(
                    ($room['name'] ?? '').
                    ' '.($room['description'] ?? '').
                    ' '.($room['location'] ?? '')
                );
                if (! Str::contains($hay, $needle)) {
                    $amenities = is_array($room['amenities'] ?? null) ? $room['amenities'] : [];
                    $amenStr = Str::lower(implode(' ', $amenities));
                    if (! Str::contains($amenStr, $needle)) {
                        return false;
                    }
                }
            }

            $cap = (int) ($room['capacity'] ?? 0);
            if ($minCap !== null && $minCap !== '' && $cap < (int) $minCap) {
                return false;
            }
            if ($maxCap !== null && $maxCap !== '' && $cap > (int) $maxCap) {
                return false;
            }

            $size = $room['size_sqm'] ?? null;
            if ($minSize !== null && $minSize !== '') {
                if ($size === null || (int) $size < (int) $minSize) {
                    return false;
                }
            }
            if ($maxSize !== null && $maxSize !== '') {
                if ($size === null || (int) $size > (int) $maxSize) {
                    return false;
                }
            }

            if ($location !== '') {
                $locRoom = (string) ($room['location'] ?? '');
                if (! Str::contains(Str::lower($locRoom), Str::lower($location))) {
                    return false;
                }
            }

            if ($amenity !== '') {
                $list = is_array($room['amenities'] ?? null) ? $room['amenities'] : [];
                $needle = Str::lower($amenity);
                $matched = false;
                foreach ($list as $item) {
                    if (is_string($item) && Str::contains(Str::lower($item), $needle)) {
                        $matched = true;
                        break;
                    }
                }
                if (! $matched) {
                    return false;
                }
            }

            if ($hasPhoto) {
                $url = (string) ($room['image_url'] ?? '');
                if ($url === '') {
                    return false;
                }
            }

            $hr = $room['hourly_rate'] ?? null;
            if ($minHourly !== null && $minHourly !== '' && is_numeric($minHourly)) {
                if ($hr === null || (float) $hr < (float) $minHourly) {
                    return false;
                }
            }
            if ($maxHourly !== null && $maxHourly !== '' && is_numeric($maxHourly)) {
                if ($hr === null || (float) $hr > (float) $maxHourly) {
                    return false;
                }
            }

            return true;
        }));

        usort($filtered, function (array $a, array $b) use ($sortKey): int {
            $a = DemoState::normalizeRoom($a);
            $b = DemoState::normalizeRoom($b);
            $na = (string) ($a['name'] ?? '');
            $nb = (string) ($b['name'] ?? '');
            $ca = (int) ($a['capacity'] ?? 0);
            $cb = (int) ($b['capacity'] ?? 0);
            $sa = $a['size_sqm'] ?? null;
            $sb = $b['size_sqm'] ?? null;
            $ra = $a['hourly_rate'] ?? null;
            $rb = $b['hourly_rate'] ?? null;
            $fa = $ra !== null && $ra !== '' ? (float) $ra : null;
            $fb = $rb !== null && $rb !== '' ? (float) $rb : null;

            if ($sortKey === 'hourly_asc') {
                if ($fa === null && $fb === null) {
                    return $na <=> $nb;
                }
                if ($fa === null) {
                    return 1;
                }
                if ($fb === null) {
                    return -1;
                }
                if (($cmp = $fa <=> $fb) !== 0) {
                    return $cmp;
                }

                return $na <=> $nb;
            }
            if ($sortKey === 'hourly_desc') {
                if ($fa === null && $fb === null) {
                    return $na <=> $nb;
                }
                if ($fa === null) {
                    return 1;
                }
                if ($fb === null) {
                    return -1;
                }
                if (($cmp = $fb <=> $fa) !== 0) {
                    return $cmp;
                }

                return $na <=> $nb;
            }

            return match ($sortKey) {
                'capacity_asc' => $ca <=> $cb ?: $na <=> $nb,
                'capacity_desc' => $cb <=> $ca ?: $na <=> $nb,
                'size_asc' => ((int) ($sa ?? 999999)) <=> ((int) ($sb ?? 999999)) ?: $na <=> $nb,
                'size_desc' => ((int) ($sb ?? 0)) <=> ((int) ($sa ?? 0)) ?: $na <=> $nb,
                default => $na <=> $nb,
            };
        });

        return [
            'rooms' => $filtered,
            'filters' => [
                'search' => $search,
                'min_capacity' => $minCap,
                'max_capacity' => $maxCap,
                'min_size_sqm' => $minSize,
                'max_size_sqm' => $maxSize,
                'min_hourly_rate' => $minHourly,
                'max_hourly_rate' => $maxHourly,
                'location' => $location,
                'amenity' => $amenity,
                'has_photo' => $hasPhoto,
                'sort' => $sortKey,
            ],
        ];
    }
}
