<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(): View
    {
        // One curated source shared by the homepage and portfolio.
        $projects = config('digitalstar.projects', []);
        return view('work', compact('projects'));
    }
}
