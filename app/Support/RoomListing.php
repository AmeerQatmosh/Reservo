<?php

namespace App\Support;

use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class RoomListing
{
    /**
     * Distinct locations and amenity labels from the database (for filter dropdowns).
     *
     * @return array{locations: Collection<int, string>, amenities: Collection<int, string>}
     */
    public static function filterOptions(bool $withTrashed = false): array
    {
        $base = Room::query();
        if ($withTrashed) {
            $base->withTrashed();
        }

        $locations = $base->clone()
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        $amenities = $base->clone()
            ->whereNotNull('amenities')
            ->pluck('amenities')
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
     * Apply query-string filters and sorting. Returns normalized filter state for views.
     *
     * @param  Builder<Room>  $query
     * @return array<string, mixed>
     */
    public static function applyRequestFilters(Builder $query, Request $request): array
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

        if ($search !== '') {
            $like = '%'.self::escapeLike($search).'%';
            $likeLower = '%'.self::escapeLike(Str::lower($search)).'%';
            $cast = $query->getConnection()->getDriverName() === 'sqlite'
                ? 'CAST(amenities AS TEXT)'
                : 'CAST(amenities AS CHAR(10000))';

            $query->where(function (Builder $q) use ($like, $likeLower, $cast) {
                $q->where('name', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('location', 'like', $like)
                    ->orWhereRaw('LOWER('.$cast.') LIKE ?', [$likeLower]);
            });
        }

        if ($minCap !== null && $minCap !== '') {
            $query->where('capacity', '>=', (int) $minCap);
        }
        if ($maxCap !== null && $maxCap !== '') {
            $query->where('capacity', '<=', (int) $maxCap);
        }
        if ($minSize !== null && $minSize !== '') {
            $query->where('size_sqm', '>=', (int) $minSize);
        }
        if ($maxSize !== null && $maxSize !== '') {
            $query->where('size_sqm', '<=', (int) $maxSize);
        }
        if ($location !== '') {
            $query->where('location', 'like', '%'.self::escapeLike($location).'%');
        }
        if ($amenity !== '') {
            $likeLower = '%'.self::escapeLike(Str::lower($amenity)).'%';
            $cast = $query->getConnection()->getDriverName() === 'sqlite'
                ? 'CAST(amenities AS TEXT)'
                : 'CAST(amenities AS CHAR(10000))';
            $query->whereRaw('LOWER('.$cast.') LIKE ?', [$likeLower]);
        }
        if ($minHourly !== null && $minHourly !== '' && is_numeric($minHourly)) {
            $query->whereNotNull('hourly_rate')
                ->where('hourly_rate', '>=', (float) $minHourly);
        }
        if ($maxHourly !== null && $maxHourly !== '' && is_numeric($maxHourly)) {
            $query->whereNotNull('hourly_rate')
                ->where('hourly_rate', '<=', (float) $maxHourly);
        }
        if ($hasPhoto) {
            $query->whereNotNull('image_url')->where('image_url', '!=', '');
        }

        $sortKey = is_string($sort) ? $sort : 'name';
        self::applySort($query, $sortKey);

        return [
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
        ];
    }

    /**
     * @param  Builder<Room>  $query
     */
    private static function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'capacity_asc' => $query->orderBy('capacity')->orderBy('name'),
            'capacity_desc' => $query->orderByDesc('capacity')->orderBy('name'),
            'size_asc' => $query->orderByRaw('COALESCE(size_sqm, 999999) ASC')->orderBy('name'),
            'size_desc' => $query->orderByRaw('COALESCE(size_sqm, 0) DESC')->orderBy('name'),
            'hourly_asc' => $query->orderByRaw('COALESCE(hourly_rate, 999999) ASC')->orderBy('name'),
            'hourly_desc' => $query->orderByRaw('COALESCE(hourly_rate, 0) DESC')->orderBy('name'),
            default => $query->orderBy('name'),
        };
    }

    private static function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $value);
    }

    /**
     * Public browse page layout: query `view` is `grid` (default) or `list`.
     */
    public static function browseView(Request $request): string
    {
        $v = $request->query('view', 'grid');

        return in_array($v, ['grid', 'list'], true) ? $v : 'grid';
    }
}
