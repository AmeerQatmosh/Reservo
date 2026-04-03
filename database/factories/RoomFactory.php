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
     * Curated Unsplash image IDs (offices / meeting spaces) for demo data.
     *
     * @var list<string>
     */
    /** IDs verified to return HTTP 200 from images.unsplash.com (some legacy Unsplash IDs now 404). */
    public const UNSPLASH_PHOTO_IDS = [
        '1544984243-ec57ea16fe25',
        '1517245386807-bb43f82c33c4',
        '1497215728101-856f4ea42174',
        '1519389950473-47ba0277781c',
        '1540575467063-178a50c2df87',
        '1497366811353-6870744d04b2',
        '1556761175-4b46a572b786',
        '1604328698692-f76ea9498e76',
        '1522071820081-009f0129c71c',
        '1497366216548-37526070297c',
        '1497366754035-f200968a6e72',
        '1524758631624-e2822e304c36',
        '1553877522-43269d4ea984',
        '1521587760476-6c12a4b040da',
    ];

    public function definition(): array
    {
        $faker = \fake();
        $photoId = $faker->randomElement(self::UNSPLASH_PHOTO_IDS);
        $label = $faker->randomElement([
            'Summit Room', 'Canvas North', 'Quartz Studio', 'Meridian Lounge',
            'Blueprint Lab', 'Cedar Conference', 'Orbit Huddle', 'Pulse Gallery',
        ]);

        return [
            'name' => $label.' '.$faker->unique()->numerify('###'),
            'capacity' => $faker->numberBetween(4, 40),
            'description' => $faker->paragraphs(3, true),
            'location' => $faker->randomElement([
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
            'image_url' => "https://images.unsplash.com/photo-{$photoId}?auto=format&fit=crop&w=1600&q=85",
        ];
    }
}
