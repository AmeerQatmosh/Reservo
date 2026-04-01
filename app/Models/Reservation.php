<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\ReservationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @use HasFactory<ReservationFactory> */
class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'date',
        'start_time',
        'end_time',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class)->withTrashed();
    }

    /** Length of booking in hours (fractional). */
    public function durationHours(): float
    {
        $dateStr = $this->date instanceof \DateTimeInterface
            ? $this->date->format('Y-m-d')
            : (string) $this->date;

        $start = Carbon::parse($dateStr.' '.$this->start_time);
        $end = Carbon::parse($dateStr.' '.$this->end_time);

        return max(0.0, $start->diffInMinutes($end) / 60);
    }

    /**
     * Estimated cost from room hourly rate × duration (informational; no payment in app).
     */
    public function estimatedTotal(): ?float
    {
        $rate = $this->room?->hourly_rate;
        if ($rate === null) {
            return null;
        }

        return round($this->durationHours() * (float) $rate, 2);
    }

    public function estimatedTotalLabel(): ?string
    {
        $total = $this->estimatedTotal();

        return $total === null ? null : '$'.number_format($total, 2);
    }
}

