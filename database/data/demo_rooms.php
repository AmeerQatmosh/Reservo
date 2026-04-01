<?php

/**
 * Curated demo room records (names are stable keys for updateOrCreate).
 * Photos: Unsplash (https://unsplash.com/license) — hotlinked via images.unsplash.com.
 *
 * @return list<array{
 *     name: string,
 *     capacity: int,
 *     description: string,
 *     location: string,
 *     size_sqm: int,
 *     amenities: list<string>,
 *     image_url: string
 * }>
 */
return [
    [
        'name' => 'The Boardroom',
        'capacity' => 14,
        'description' => "Polished executive suite with a 98\" 4K display, polycom conference phone, and leather seating in a herringbone layout. Floor-to-ceiling windows face the park—great for client pitches and board prep.\n\nTypical setup: board table, credenza with refreshments, printed agendas on request via facilities.",
        'location' => 'North Tower · 12th floor · Suite 1204',
        'size_sqm' => 52,
        'amenities' => [
            '4K display + HDMI / USB-C',
            'Conference phone + ceiling mics',
            'Motorized blinds',
            'Climate control',
            'Whiteboard wall',
        ],
        'image_url' => 'https://images.unsplash.com/photo-1544984243-ec57ea16fe25?auto=format&fit=crop&w=1600&q=85',
    ],
    [
        'name' => 'Studio B — Workshop',
        'capacity' => 24,
        'description' => "Flexible training studio with modular tables on casters, stackable chairs, and four flip-chart stations. Designed for design sprints, onboarding weeks, and cross-functional workshops.\n\nSame-day reconfiguration available before 9:00—note your layout in the booking notes.",
        'location' => 'Annex Building · Ground floor · Studio wing',
        'size_sqm' => 78,
        'amenities' => [
            'Modular tables & chairs',
            'Wall-mounted screen 75"',
            'Breakout corners (2)',
            'Kitchenette access',
            'High-speed Wi‑Fi',
        ],
        'image_url' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1600&q=85',
    ],
    [
        'name' => 'The Atrium',
        'capacity' => 8,
        'description' => "Sun-filled collaboration nook beside the indoor garden. Sofas, a standing-height table, and acoustic panels keep conversations comfortable without disturbing the open floor.\n\nIdeal for weekly syncs, 1:1s, and small interviews.",
        'location' => 'Main campus · Atrium level · Garden side',
        'size_sqm' => 28,
        'amenities' => [
            'Sofa + armchairs',
            '55\" display',
            'Wireless casting',
            'Plants & daylight',
        ],
        'image_url' => 'https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&w=1600&q=85',
    ],
    [
        'name' => 'Focus Pod 1',
        'capacity' => 4,
        'description' => "Sound-dampened pod with a frosted glass door, ergonomic task chairs, and a shared monitor. Optimized for confidential calls, deep work blocks, and small pair-programming sessions.\n\nMax continuous booking: 4 hours (fair-use policy).",
        'location' => 'East wing · Floor 5 · Quiet zone',
        'size_sqm' => 12,
        'amenities' => [
            'Acoustic door seal',
            '27\" monitor + dock',
            'Power & USB at table',
            'Adjustable lighting',
        ],
        'image_url' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1600&q=85',
    ],
    [
        'name' => 'Town Hall',
        'capacity' => 60,
        'description' => "Our largest internal venue: tiered seating, stage platform, dual projectors, and a ready-to-use hybrid streaming rig (camera + boundary mics). Holds company all-hands, AMAs, and guest speakers.\n\nCatering and A/V tech must be booked 5 business days ahead.",
        'location' => 'Events Center · Hall A',
        'size_sqm' => 185,
        'amenities' => [
            'Stage + wireless handheld mics',
            'Dual projection',
            'Hybrid streaming kit',
            'Wheelchair-accessible aisles',
            'Green room (shared)',
        ],
        'image_url' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1600&q=85',
    ],
    [
        'name' => 'Harbor View Lab',
        'capacity' => 12,
        'description' => "Corner lab with writable glass on two walls, height-adjustable desks, and a dedicated fiber uplink. Favorite spot for architecture reviews, incident postmortems, and whiteboarding-heavy sprints.\n\nHarbor-facing windows; blackout shades for screen-heavy sessions.",
        'location' => 'South Tower · 8th floor · Corner NE',
        'size_sqm' => 45,
        'amenities' => [
            'Writable glass walls',
            'Standing desks',
            'Dedicated 1 Gbps drop',
            '65\" 4K + camera bar',
            'Harbor view',
        ],
        'image_url' => 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=1600&q=85',
    ],
    [
        'name' => 'Canvas East',
        'capacity' => 10,
        'description' => "Creative war-room with floor-to-ceiling whiteboards on two sides, mobile easels, and a ceiling-mounted projector. Built for brainstorming, journey mapping, and sticky-note heavy sessions.\n\nMarkers and easel pads live in the credenza—restock via facilities if empty.",
        'location' => 'Innovation wing · Floor 2 · East studios',
        'size_sqm' => 38,
        'amenities' => [
            'Full-wall whiteboards',
            'Mobile easels (3)',
            '1080p projector',
            'Natural light',
            'Lockable storage',
        ],
        'image_url' => 'https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=1600&q=85',
    ],
    [
        'name' => 'The Library',
        'capacity' => 6,
        'description' => "Low-noise reading room aesthetic: warm lighting, upholstered chairs, and a shared table for quiet document review or small policy working groups.\n\nPlease keep voices low; not suitable for dial-in calls without a headset.",
        'location' => 'Heritage building · Mezzanine',
        'size_sqm' => 22,
        'amenities' => [
            'Reading lamps',
            'Shared oak table',
            'Small display (HDMI)',
            'Bookable in 2h blocks',
        ],
        'image_url' => 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=1600&q=85',
    ],
    [
        'name' => 'Sprint Box',
        'capacity' => 8,
        'description' => "Compact agile room with wall-mounted TV, camera bar for hybrid stand-ups, and a single long table. Perfect for two-pizza teams running sprints or backlog grooming.\n\nPower strips are under the table—please coil cables when you leave.",
        'location' => 'Tech floor · Pod cluster C',
        'size_sqm' => 24,
        'amenities' => [
            '55\" TV + HDMI',
            'Hybrid camera bar',
            'Glass wall (writable)',
            'Standing room at back',
        ],
        'image_url' => 'https://images.unsplash.com/photo-1556761175-4b46a572b786?auto=format&fit=crop&w=1600&q=85',
    ],
    [
        'name' => 'Skydeck Briefing',
        'capacity' => 16,
        'description' => "Glass-wrapped corner room with skyline views, a long board table, and dimmable zones for both presentation and discussion modes.\n\nSunset slots book fast—reserve early for investor or board-adjacent meetings.",
        'location' => 'North Tower · 18th floor · Skydeck',
        'size_sqm' => 48,
        'amenities' => [
            'Panoramic windows',
            'Dimmable lighting',
            'Conference speakerphone',
            'Catering cart space',
            'Coat closet',
        ],
        'image_url' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1600&q=85',
    ],
];
