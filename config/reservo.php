<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Guest demo (session-only sandbox)
    |--------------------------------------------------------------------------
    |
    | When true, /demo is available: portfolio visitors can try booking logic
    | without accounts. Data lives in session only—not the database.
    |
    | If RESERVO_DEMO_ENABLED is not set in .env, defaults to ON in local and
    | OFF in production so you see the Free room entry without extra config.
    |
    */
    'demo_enabled' => env('RESERVO_DEMO_ENABLED') === null
        ? env('APP_ENV', 'production') === 'local'
        : filter_var(env('RESERVO_DEMO_ENABLED'), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | Booking day window
    |--------------------------------------------------------------------------
    |
    | Times are HH:MM (24h). Bookings are only allowed within this window.
    | Set RESERVO_BOOKING_OPEN_24_HOURS=true to use the full day (00:00–last
    | slot before midnight; see slot_minutes).
    |
    */
    'booking' => [
        'open_24_hours' => filter_var(env('RESERVO_BOOKING_OPEN_24_HOURS', false), FILTER_VALIDATE_BOOLEAN),
        'day_starts_at' => env('RESERVO_BOOKING_DAY_STARTS_AT', '08:00'),
        'day_ends_at' => env('RESERVO_BOOKING_DAY_ENDS_AT', '18:00'),
        'slot_minutes' => (int) env('RESERVO_BOOKING_SLOT_MINUTES', 30),
    ],

];
