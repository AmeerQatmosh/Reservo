<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

final class UserReservationCalendar
{
    /**
     * @return array{0: int, 1: int}
     */
    public static function parseYearMonthFromRequest(Request $request): array
    {
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);
        $year = max(2020, min(2040, $year));
        $month = max(1, min(12, $month));

        return [$year, $month];
    }

    /**
     * Build ISO date grid (Sunday → Saturday) spanning the calendar month pads.
     *
     * @return array{
     *     monthCarbon: Carbon,
     *     gridStart: Carbon,
     *     gridEnd: Carbon,
     *     weeks: list<list<array{date:string, label:int, in_month:bool, is_today:bool}>>,
     *     heading: string
     * }
     */
    public static function buildWeekGrid(int $year, int $month): array
    {
        $monthCarbon = Carbon::createFromDate($year, $month, 1, config('app.timezone'))
            ->locale(app()->getLocale());

        $gridStart = $monthCarbon->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $gridEnd = $monthCarbon->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $weeks = [];
        $cursor = $gridStart->copy();
        while ($cursor->lte($gridEnd)) {
            $row = [];
            for ($i = 0; $i < 7; $i++) {
                $row[] = [
                    'date' => $cursor->toDateString(),
                    'label' => $cursor->day,
                    'in_month' => $cursor->month === $monthCarbon->month,
                    'is_today' => $cursor->isToday(),
                ];
                $cursor->addDay();
            }
            $weeks[] = $row;
        }

        return [
            'monthCarbon' => $monthCarbon,
            'gridStart' => $gridStart,
            'gridEnd' => $gridEnd,
            'weeks' => $weeks,
            'heading' => $monthCarbon->translatedFormat('F Y'),
        ];
    }
}
