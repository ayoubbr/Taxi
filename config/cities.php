<?php

return [
    'proximity_map' => [
        'Marrakech' => ['Safi', 'Essaouira', 'Casablanca'],
        'Casablanca' => ['Rabat', 'Marrakech', 'El Jadida'],
        'Rabat' => ['Casablanca', 'Kenitra', 'Meknes'],
        'Safi' => ['Marrakech', 'Essaouira'],
        'Essaouira' => ['Marrakech', 'Safi'],
        'Agadir' => ['Taroudant', 'Essaouira'],
        // ...
    ],
    'search_tiers' => [
        // Tier 1: Immediate city
        0 => 0, // Current city (0 hops away)
        // Tier 2: Directly adjacent cities
        1 => 1, // 1 hop away
        // Tier 3: Cities adjacent to Tier 2 cities (2 hops away)
        2 => 2, // 2 hops away
        // ...
    ],
];
