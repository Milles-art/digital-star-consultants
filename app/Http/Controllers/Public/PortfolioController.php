<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    /**
     * Static showcase items until a CMS/portfolio admin is added.
     * Replace titles, descriptions and stacks with real client work.
     */
    public function index(): View
    {
        $itProjects = [
            [
                'title' => 'Service request & tracking platform',
                'title_sw' => 'Jukwaa la maombi na ufuatiliaji',
                'summary' => 'Web system for citizens to submit service requests, upload documents, and track status by reference number — built for a local service bureau.',
                'summary_sw' => 'Mfumo wa wavuti kwa raia kuwasilisha maombi, kupakia nyaraka, na kufuatilia hali kwa namba ya kumbukumbu.',
                'stack' => 'Laravel · MySQL · Tailwind · Vite',
                'tag' => 'Web app',
            ],
            [
                'title' => 'Business operations dashboard',
                'title_sw' => 'Dashibodi ya uendeshaji biashara',
                'summary' => 'Internal dashboard for staff roles, assignments, reports and submission workflows with role-based access.',
                'summary_sw' => 'Dashibodi ya ndani kwa majukumu ya staff, ugawaji kazi, ripoti na mtiririko wa maombi.',
                'stack' => 'Laravel · Role middleware · Charts API',
                'tag' => 'Internal tool',
            ],
            [
                'title' => 'Custom business website',
                'title_sw' => 'Tovuti maalum ya biashara',
                'summary' => 'Marketing site with service catalogue, contact flows and mobile-first design for a Dar es Salaam SME.',
                'summary_sw' => 'Tovuti ya uuzaji yenye orodha ya huduma, mawasiliano na muundo unaofaa simu kwa SME ya Dar.',
                'stack' => 'Laravel · Tailwind · SEO basics',
                'tag' => 'Website',
            ],
        ];

        $graphicsProjects = [
            [
                'title' => 'Brand identity pack',
                'title_sw' => 'Kifurushi cha utambulisho wa brand',
                'summary' => 'Logo, colour system and print-ready templates for cards, letterheads and social posts.',
                'summary_sw' => 'Nembo, rangi na templeti za print kwa kadi, barua na mitandao ya kijamii.',
                'stack' => 'Brand · Print · Social',
                'tag' => 'Branding',
            ],
            [
                'title' => 'Event & outdoor print',
                'title_sw' => 'Print ya matukio na nje',
                'summary' => 'Banners, posters and large-format artwork prepared for local production.',
                'summary_sw' => 'Mabango, posta na kazi kubwa zilizoandaliwa kwa uzalishaji wa karibu.',
                'stack' => 'Large format · Poster · Banner',
                'tag' => 'Print',
            ],
            [
                'title' => 'Product & promo design',
                'title_sw' => 'Ubunifu wa bidhaa na matangazo',
                'summary' => 'Flyers, menus and promotional layouts for shops and service businesses.',
                'summary_sw' => 'Flaya, menyu na miundo ya matangazo kwa maduka na biashara za huduma.',
                'stack' => 'Flyer · Menu · Promo',
                'tag' => 'Design',
            ],
        ];

        return view('work', compact('itProjects', 'graphicsProjects'));
    }
}
