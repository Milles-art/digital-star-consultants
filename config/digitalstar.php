<?php

/*
|--------------------------------------------------------------------------
| Digital Star site content
|--------------------------------------------------------------------------
| Shared content for the redesigned pages. Read it with config('digitalstar.projects') etc.
| Replace the stand-in Unsplash images with your own (e.g. asset('images/work/...')).
*/

return [
    'images' => [
        'hero' => '/images/ds/hero.jpg',
        'team' => '/images/ds/team.jpg',
        'collab' => '/images/ds/collab.jpg',
    ],

    'services' => [[
    'title' => 'Government & Online Services',
    'slug' => 'online-government-services',
    'copy' => 'Help with identification, tax, education, travel and online applications.',
    'icon' => 'file-text',
    'points' => ['NIDA & civil registration', 'TRA & tax', 'Online applications']
], [
    'title' => 'Business Services',
    'slug' => 'business-services',
    'copy' => 'Practical support for business registration and the paperwork that comes with it.',
    'icon' => 'briefcase',
    'points' => ['BRELA registration', 'Business documents', 'Application support']
], [
    'title' => 'Printing, Branding & Stationery',
    'slug' => 'printing-graphics-design',
    'copy' => 'From a logo on paper to a sign above your door. Design, print and everyday supplies.',
    'icon' => 'palette',
    'points' => ['Logos & banners', 'Branded items', 'Stationery']
], [
    'title' => 'IT & Technology',
    'slug' => 'it-tech-consultancy',
    'copy' => 'Websites, software and technical support built around how you work.',
    'icon' => 'laptop',
    'points' => ['Websites', 'Software', 'IT support']
]],
    'filters' => [
    'all' => 'All work',
    'logos' => 'Logo design',
    'print' => 'Print & signage',
    'merchandise' => 'Branded items',
    'video' => 'Videos'
],
    'projects' => [[
    'title' => 'Chichi Family',
    'category' => 'Wall branding',
    'filter' => 'merchandise',
    'description' => 'A gold identity presented on a teal feature wall. A look at how the mark works at a larger scale.',
    'image' => 'images/projects/chichi-wall-branding.webp',
    'thumbnail' => 'images/projects/chichi-wall-branding-640.webp',
    'width' => 1200,
    'height' => 800,
    'alt' => 'Gold Chichi Family logo displayed on a teal wall',
    'meta' => ['Wall branding'],
    'type' => 'image'
], [
    'title' => 'Chichi Family',
    'category' => 'Branded mugs',
    'filter' => 'merchandise',
    'description' => 'The same identity carried across black and white mugs, keeping the brand recognizable on everyday items.',
    'image' => 'images/projects/chichi-branded-mugs.webp',
    'thumbnail' => 'images/projects/chichi-branded-mugs-640.webp',
    'width' => 1200,
    'height' => 728,
    'alt' => 'Black and white mugs displaying the Chichi Family logo',
    'meta' => ['Product mockup'],
    'type' => 'image'
], [
    'title' => 'Gefftravel',
    'category' => 'Travel identity',
    'filter' => 'logos',
    'description' => 'A travel identity built around a hiker, mountains and a warm sunset palette.',
    'image' => 'images/projects/gefftravel-identity.webp',
    'thumbnail' => 'images/projects/gefftravel-identity-640.webp',
    'width' => 1024,
    'height' => 1024,
    'alt' => 'Gefftravel logo with hiker and sunset',
    'meta' => ['Logo design'],
    'type' => 'image'
], [
    'title' => 'Wasafi Car Wash',
    'category' => 'Promotional banner',
    'filter' => 'print',
    'description' => 'Bold red lettering and vehicle imagery bring the car wash name and contact details together in one wide-format layout.',
    'image' => 'images/projects/wasafi-car-wash-banner.webp',
    'thumbnail' => 'images/projects/wasafi-car-wash-banner-640.webp',
    'width' => 1200,
    'height' => 600,
    'alt' => 'Wasafi Car Wash banner artwork with red headline and vehicles',
    'meta' => ['Banner artwork'],
    'type' => 'image'
], [
    'title' => 'Bima Hapa',
    'category' => 'Insurance banner',
    'filter' => 'print',
    'description' => 'A yellow-and-black layout that puts the insurance message first, with vehicle and property illustrations beneath it.',
    'image' => 'images/projects/bima-insurance-banner.webp',
    'thumbnail' => 'images/projects/bima-insurance-banner-640.webp',
    'width' => 1200,
    'height' => 420,
    'alt' => 'Yellow Bima Hapa insurance banner artwork',
    'meta' => ['Banner artwork'],
    'type' => 'image'
], [
    'title' => 'Dunyo Quality Furniture',
    'category' => 'Furniture identity',
    'filter' => 'logos',
    'description' => 'A sofa and a roofline form a furniture identity, presented here as a dimensional mark against brickwork.',
    'image' => 'images/projects/dunyo-furniture-identity.webp',
    'thumbnail' => 'images/projects/dunyo-furniture-identity-640.webp',
    'width' => 1024,
    'height' => 1024,
    'alt' => 'Dunyo Quality Furniture logo with a sofa and roofline',
    'meta' => ['Logo presentation'],
    'type' => 'image'
], [
    'title' => 'Lulu Beauty & Cosmetics',
    'category' => 'Beauty identity',
    'filter' => 'logos',
    'description' => 'A gold monogram with ornamental detail, set against a dark background for the beauty and cosmetics brand.',
    'image' => 'images/projects/lulu-beauty-identity.webp',
    'thumbnail' => 'images/projects/lulu-beauty-identity-640.webp',
    'width' => 1024,
    'height' => 1024,
    'alt' => 'Gold Lulu Beauty and Cosmetics logo on a dark background',
    'meta' => ['Logo design'],
    'type' => 'image'
], [
    'title' => 'King James Enterprises',
    'category' => 'Business identity',
    'filter' => 'logos',
    'description' => 'A crown and globe sit above the business name in this gold, red and blue identity.',
    'image' => 'images/projects/king-james-identity.webp',
    'thumbnail' => 'images/projects/king-james-identity-640.webp',
    'width' => 1024,
    'height' => 1024,
    'alt' => 'King James Enterprises crown and globe logo',
    'meta' => ['Logo design'],
    'type' => 'image'
], [
    'title' => 'Robin Dagaa',
    'category' => 'Food business identity',
    'filter' => 'logos',
    'description' => 'Fish baskets, mountain scenery and a gold outline give this food business a distinctive badge-style identity.',
    'image' => 'images/projects/robin-dagaa-identity.webp',
    'thumbnail' => 'images/projects/robin-dagaa-identity-640.webp',
    'width' => 1024,
    'height' => 1024,
    'alt' => 'Robin Dagaa logo showing baskets of fish and mountain scenery',
    'meta' => ['Logo design'],
    'type' => 'image'
], [
    'title' => 'Chichi Family',
    'category' => 'ID card branding',
    'filter' => 'merchandise',
    'description' => 'A monochrome version of the identity applied to a lanyard and identification card mockup.',
    'image' => 'images/projects/chichi-id-card.webp',
    'thumbnail' => 'images/projects/chichi-id-card-640.webp',
    'width' => 1200,
    'height' => 900,
    'alt' => 'Chichi Family identification card and lanyard mockup',
    'meta' => ['ID card mockup'],
    'type' => 'image'
], [
    'title' => 'Heavy Parts',
    'category' => 'Shopfront signage',
    'filter' => 'video',
    'description' => 'A short on-site video showing the wide-format parts signage and its placement around the entrance.',
    'image' => 'images/projects/heavy-parts-signage.webp',
    'thumbnail' => 'images/projects/heavy-parts-signage-640.webp',
    'width' => 478,
    'height' => 850,
    'alt' => 'Shopfront with heavy machinery parts signage',
    'meta' => ['Project video'],
    'type' => 'video',
    'video' => 'videos/projects/heavy-parts-signage.mp4'
], [
    'title' => 'ASMA Lingerie',
    'category' => 'Signage detail',
    'filter' => 'video',
    'description' => 'A closer look at the black-and-gold ASMA Lingerie sign, including its raised lettering.',
    'image' => 'images/projects/asma-lingerie-sign.webp',
    'thumbnail' => 'images/projects/asma-lingerie-sign-640.webp',
    'width' => 478,
    'height' => 850,
    'alt' => 'Black ASMA Lingerie sign with white and gold lettering',
    'meta' => ['Project video'],
    'type' => 'video',
    'video' => 'videos/projects/asma-lingerie-sign.mp4'
], [
    'title' => 'Personalized textiles',
    'category' => 'Names in print',
    'filter' => 'video',
    'description' => 'A short workshop clip showing names applied to a collection of soft textile items.',
    'image' => 'images/projects/personalized-textiles.webp',
    'thumbnail' => 'images/projects/personalized-textiles-640.webp',
    'width' => 360,
    'height' => 640,
    'alt' => 'Personalized textile items with names printed on them',
    'meta' => ['Project video'],
    'type' => 'video',
    'video' => 'videos/projects/personalized-textiles.mp4'
]],
];
