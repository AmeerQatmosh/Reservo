<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Shorter labels for filter dropdowns / comboboxes (hover shows full value via title).
 */
final class FilterDisplay
{
    public static function locationLabel(string $location, int $max = 48): string
    {
        $s = trim($location);
        if ($s === '') {
            return $s;
        }
        if (mb_strlen($s) <= $max) {
            return $s;
        }
        $beforeComma = Str::of($s)->before(',')->trim();
        if ($beforeComma->length() > 0 && $beforeComma->length() < mb_strlen($s) && $beforeComma->length() <= $max) {
            return (string) $beforeComma->append('…');
        }

        return Str::limit($s, $max, '…');
    }

    public static function amenityLabel(string $amenity, int $max = 36): string
    {
        $s = trim($amenity);
        if (mb_strlen($s) <= $max) {
            return $s;
        }

        return Str::limit($s, $max, '…');
    }

    /**
     * True when the list is narrowed by search, sidebar refinements, or (admin) status.
     * Sort order alone does not count — empty DB with only sort changed should not read as “filtered out”.
     *
     * @param  array<string, mixed>  $filters
     */
    public static function roomBrowseHasNarrowing(array $filters, bool $includeStatus = false): bool
    {
        if (trim((string) ($filters['search'] ?? '')) !== '') {
            return true;
        }
        $intKeys = ['min_capacity', 'max_capacity', 'min_size_sqm', 'max_size_sqm'];
        foreach ($intKeys as $k) {
            $v = $filters[$k] ?? null;
            if ($v !== null && $v !== '' && is_numeric($v) && (int) $v > 0) {
                return true;
            }
        }
        foreach (['min_hourly_rate', 'max_hourly_rate'] as $k) {
            $v = $filters[$k] ?? null;
            if ($v !== null && $v !== '' && is_numeric($v) && (float) $v > 0.0) {
                return true;
            }
        }
        if (trim((string) ($filters['location'] ?? '')) !== '') {
            return true;
        }
        if (trim((string) ($filters['amenity'] ?? '')) !== '') {
            return true;
        }
        if (! empty($filters['has_photo'])) {
            return true;
        }
        if ($includeStatus && ($filters['status'] ?? 'all') !== 'all') {
            return true;
        }

        return false;
    }
}
