<?php

namespace App\Support;

final class ReservationBookingWindow
{
    public static function slotMinutes(): int
    {
        $m = (int) config('reservo.booking.slot_minutes', 30);

        return max(5, min(60, $m));
    }

    public static function openMinutes(): int
    {
        if (config('reservo.booking.open_24_hours')) {
            return 0;
        }

        return self::timeStringToMinutes((string) config('reservo.booking.day_starts_at', '08:00'));
    }

    public static function closeMinutes(): int
    {
        if (config('reservo.booking.open_24_hours')) {
            $step = self::slotMinutes();

            return 24 * 60 - $step;
        }

        return self::timeStringToMinutes((string) config('reservo.booking.day_ends_at', '18:00'));
    }

    /**
     * @return list<string> "HH:MM" labels from opening through closing, inclusive.
     */
    public static function slotLabels(): array
    {
        $open = self::openMinutes();
        $close = self::closeMinutes();
        $step = self::slotMinutes();

        if ($close < $open) {
            return [];
        }

        $slots = [];
        for ($m = $open; $m <= $close; $m += $step) {
            $slots[] = self::minutesToLabel($m);
        }

        return $slots;
    }

    /**
     * Slot list for &lt;select&gt;, optionally keeping legacy times for edit forms.
     *
     * @param  list<string|null>  $extras
     * @return list<string>
     */
    public static function selectOptions(array $extras = []): array
    {
        $slots = self::slotLabels();

        foreach ($extras as $extra) {
            if ($extra === null || $extra === '') {
                continue;
            }
            if (! preg_match('/^\d{2}:\d{2}$/', (string) $extra)) {
                continue;
            }
            if (! in_array($extra, $slots, true)) {
                $slots[] = $extra;
            }
        }

        usort($slots, fn (string $a, string $b): int => self::timeStringToMinutes($a) <=> self::timeStringToMinutes($b));

        return $slots;
    }

    public static function hoursSummary(): string
    {
        if (config('reservo.booking.open_24_hours')) {
            return '24 hours (midnight–end of day)';
        }

        $start = (string) config('reservo.booking.day_starts_at', '08:00');
        $end = (string) config('reservo.booking.day_ends_at', '18:00');

        return "{$start}–{$end}";
    }

    public static function slotStepLabel(): string
    {
        $step = self::slotMinutes();

        return $step >= 60 ? '1 hour' : "{$step} minutes";
    }

    /**
     * @param  list<string>  $legacyHi  Start/end times (H:i) that may be kept when editing an existing row.
     * @return array<string, list<string>>
     */
    public static function validationErrors(string $startHi, string $endHi, array $legacyHi = []): array
    {
        $errors = [];
        $open = self::openMinutes();
        $close = self::closeMinutes();
        $step = self::slotMinutes();

        if ($close < $open) {
            return [
                'start_time' => ['Booking hours are misconfigured (end before start).'],
            ];
        }

        $startM = self::timeStringToMinutes($startHi);
        $endM = self::timeStringToMinutes($endHi);

        if (! self::isAcceptableBoundary($startHi, $startM, $open, $close, $step, $legacyHi)) {
            $errors['start_time'] = [self::rejectMessage('start')];
        }

        if (! self::isAcceptableBoundary($endHi, $endM, $open, $close, $step, $legacyHi)) {
            $errors['end_time'] = [self::rejectMessage('end')];
        }

        return $errors;
    }

    private static function rejectMessage(string $which): string
    {
        if (config('reservo.booking.open_24_hours')) {
            return "The {$which} time must align to a ".self::slotStepLabel().' slot.';
        }

        return "The {$which} time must fall within ".self::hoursSummary().' on '.self::slotStepLabel().' steps.';
    }

    /**
     * @param  list<string>  $legacyHi
     */
    private static function isAcceptableBoundary(string $hi, int $minutes, int $open, int $close, int $step, array $legacyHi): bool
    {
        if (in_array($hi, $legacyHi, true)) {
            return true;
        }

        if ($minutes < $open || $minutes > $close) {
            return false;
        }

        return ($minutes - $open) % $step === 0;
    }

    private static function timeStringToMinutes(string $hi): int
    {
        $parts = explode(':', $hi);

        return ((int) $parts[0]) * 60 + ((int) ($parts[1] ?? 0));
    }

    private static function minutesToLabel(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        return sprintf('%02d:%02d', $h, $m);
    }
}
