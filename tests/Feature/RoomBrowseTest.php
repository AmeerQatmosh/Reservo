<?php

use App\Models\Room;

test('guests can browse rooms with extended filters', function () {
    Room::factory()->create([
        'name' => 'North Filter Room',
        'capacity' => 18,
        'location' => 'North Tower · Demo',
        'size_sqm' => 42,
        'amenities' => ['Demo HDMI port'],
        'image_url' => 'https://images.unsplash.com/photo-1637665662134-db459c1bbb46?w=100',
    ]);

    $this->get(route('rooms.index', [
        'min_capacity' => 10,
        'max_capacity' => 30,
        'min_size_sqm' => 20,
        'max_size_sqm' => 80,
        'location' => 'North Tower · Demo',
        'amenity' => 'Demo HDMI port',
        'has_photo' => '1',
        'sort' => 'capacity_desc',
    ]))->assertOk()->assertSee('North Filter Room');

    $this->get(route('rooms.index', ['search' => 'hdmi', 'sort' => 'size_asc']))->assertOk();

    $this->get(route('rooms.index', [
        'location' => 'North Tower',
        'amenity' => 'hdmi',
    ]))->assertOk()->assertSee('North Filter Room');
});
