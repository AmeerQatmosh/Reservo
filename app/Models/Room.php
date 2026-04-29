<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'capacity',
        'description',
        'location',
        'size_sqm',
        'hourly_rate',
        'amenities',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'hourly_rate' => 'decimal:2',
        ];
    }

    /** Display label for browse/show (no payment — informational). */
    public function hourlyRateLabel(): ?string
    {
        if ($this->hourly_rate === null) {
            return null;
        }

        return '$'.number_format((float) $this->hourly_rate, 2).'/hr';
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function favoredByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'room_favorites')->withTimestamps();
    }

    /**
     * @return list<string>|null
     */
    public static function parseAmenitiesText(?string $raw): ?array
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        $lines = collect(preg_split('/\r\n|\r|\n/', $raw))
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->take(30)
            ->values()
            ->all();

        return $lines === [] ? null : $lines;
    }
}
