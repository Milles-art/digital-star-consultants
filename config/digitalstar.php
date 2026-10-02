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
        'hero' => 'https://images.unsplash.com/photo-1739292774739-ee38cd9a5735?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1400',
        'team' => 'https://images.unsplash.com/photo-1659241869124-d7046cd567ed?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1400',
        'collab' => 'https://images.unsplash.com/photo-1641759191629-cc107016e78a?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1200',
    ],

    'services' => [
        ['title' => 'Web Development', 'copy' => 'Modern, responsive websites that grow your business.', 'icon' => 'laptop', 'points' => ['Business websites', 'E-commerce', 'Landing pages']],
        ['title' => 'Software Development', 'copy' => 'Custom software solutions for your unique needs.', 'icon' => 'code-2', 'points' => ['Management systems', 'Web apps', 'Integrations']],
        ['title' => 'Digital Marketing', 'copy' => 'Grow your brand with strategic digital marketing.', 'icon' => 'megaphone', 'points' => ['Social media', 'Branding', 'Graphic design']],
        ['title' => 'IT Consultancy', 'copy' => 'Expert advice to help you make the right technology decisions.', 'icon' => 'server', 'points' => ['Networks', 'Cloud setup', 'IT support']],
    ],

    'filters' => [
        'all' => 'All projects',
        'it' => 'IT & Technology',
        'software' => 'Software & Web',
        'graphics' => 'Graphics & Branding',
    ],

    'projects' => [
        [
            'title' => 'E-Commerce Platform',
            'category' => 'Web Development',
            'filter' => 'software',
            'description' => 'A fast online store with product catalogue, mobile payments and an easy admin panel for managing orders.',
            'image' => 'https://images.unsplash.com/photo-1688561808434-886a6dd97b8c?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
            'meta' => ['Online store', 'Mobile payments', 'Admin panel'],
        ],
        [
            'title' => 'Management System',
            'category' => 'Software Development',
            'filter' => 'software',
            'description' => 'A custom dashboard that helps a growing organization track operations, reports and staff in one place.',
            'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
            'meta' => ['Dashboards', 'Reports', 'User roles'],
        ],
        [
            'title' => 'Organization Website',
            'category' => 'Web Development',
            'filter' => 'software',
            'description' => 'A modern, content-rich website that makes it easy for visitors to learn about programs and get in touch.',
            'image' => 'https://images.unsplash.com/photo-1542744095-291d1f67b221?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
            'meta' => ['Responsive', 'Content updates', 'SEO ready'],
        ],
        [
            'title' => 'Office Network Setup',
            'category' => 'IT & Technology',
            'filter' => 'it',
            'description' => 'Structured cabling, secure Wi-Fi and server setup for a multi-floor office with reliable ongoing support.',
            'image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
            'meta' => ['Networking', 'Security', 'Support'],
        ],
        [
            'title' => 'Data Center Upgrade',
            'category' => 'IT & Technology',
            'filter' => 'it',
            'description' => 'Planning and rollout of new server infrastructure with backups and monitoring for business continuity.',
            'image' => 'https://images.unsplash.com/photo-1506399558188-acca6f8cbf41?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
            'meta' => ['Servers', 'Backups', 'Monitoring'],
        ],
        [
            'title' => 'Brand Identity Refresh',
            'category' => 'Graphics & Branding',
            'filter' => 'graphics',
            'description' => 'A new logo, colour palette and print collateral that gave a local business a confident, consistent look.',
            'image' => 'https://images.unsplash.com/photo-1763705857736-2b4f16a33758?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080',
            'meta' => ['Logo', 'Brand guide', 'Print'],
        ],
    ],
];
