<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    /**
     * Slugs for extra seeded rooms only — disjoint from database/data/demo_rooms.php
     * so factory rows never reuse a curated room’s hero image.
     *
     * @var list<string>
     */
    public const FACTORY_ONLY_PHOTO_IDS = [
        '1685602729266-9fa302fd7121',
        '1768225709733-18c9f264bc5f',
        '1771147372634-976f022c0033',
        '1765366417046-f46361a7f26f',
        '1761912915676-74c7d63105a3',
        '1770833942746-3cbacd9ea5b2',
        '1758448511255-ac2a24a135d7',
        '1763567823709-9df979a3b7b8',
        '1760455311959-7118d4f11e41',
        '1758800601486-75c3865cc9a5',
    ];

    public function definition(): array
    {
        $faker = \fake();
        $photoId = $faker->randomElement(self::FACTORY_ONLY_PHOTO_IDS);
        $label = $faker->randomElement([
            'Meridian Huddle', 'Meridian Lounge', 'Annex Studio', 'Skydeck Breakout',
            'Harbor Lab', 'Events Wing', 'Innovation Cell', 'North Tower Suite',
        ]);

        return [
            'name' => $label.' '.$faker->unique()->numerify('###'),
            'capacity' => $faker->numberBetween(4, 40),
            'description' => 'Meeting or event space at Meridian House. '.$faker->paragraph(2),
            'location' => 'Meridian House · '.$faker->randomElement([
                'North Tower · Floor '.$faker->numberBetween(2, 14),
                'Annex · Level '.$faker->randomElement(['G', '1', '2']),
                'Main campus · Wing '.$faker->randomElement(['A', 'B', 'C']),
            ]).' · '.$faker->bothify('Room ??#'),
            'size_sqm' => $faker->numberBetween(18, 120),
            'hourly_rate' => $faker->randomFloat(2, 18, 55),
            'amenities' => $faker->randomElements([
                'HDMI / USB-C', 'Wireless casting', 'Whiteboard', 'Video bar',
                'Natural light', 'Standing desks', 'Catering prep', 'Phone booth nearby',
            ], $faker->numberBetween(3, 6)),
            'image_url' => "https://images.unsplash.com/photo-{$photoId}?auto=format&fit=crop&w=2400&q=90",
        ];
    }
}
