<?php

namespace Tests\Unit;

use App\Models\Room;
use PHPUnit\Framework\TestCase;

class RoomAmenitiesParseTest extends TestCase
{
    public function test_parses_comma_separated_values(): void
    {
        $parsed = Room::parseAmenitiesText('Whiteboard, HDMI, Video bar');
        $this->assertSame(['Whiteboard', 'HDMI', 'Video bar'], $parsed);
    }

    public function test_newlines_still_split_items(): void
    {
        $parsed = Room::parseAmenitiesText("Ethernet\nCoffee station");
        $this->assertSame(['Ethernet', 'Coffee station'], $parsed);
    }

    public function test_mixed_commas_and_newlines(): void
    {
        $parsed = Room::parseAmenitiesText("a, b\nc");
        $this->assertSame(['a', 'b', 'c'], $parsed);
    }

    public function test_empty_string_returns_null(): void
    {
        $this->assertNull(Room::parseAmenitiesText(''));
        $this->assertNull(Room::parseAmenitiesText('   '));
    }

    public function test_truncates_to_thirty_items(): void
    {
        $raw = implode(',', range(1, 40));
        $parsed = Room::parseAmenitiesText($raw);
        $this->assertCount(30, $parsed);
    }
}
