<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
class PageController extends Controller
{

    public function main(): Response 
    {
        return Inertia::render('Main');
    }

    public function price(): Response 
    {
        return Inertia::render('Price');
    }

    public function services(): Response 
    {
        return Inertia::render('Services');
    }

    public function about(): Response 
    {
        return Inertia::render('About');
    }

    public function specialOffers(): Response 
    {
        return Inertia::render('SpecialOffers');
    }

    public function contacts(): Response 
    {
        return Inertia::render('Contacts');
    }

    public function reviews(): Response 
    {
        return Inertia::render('Reviews');
    }

    public function specialists(): Response 
    {
        return Inertia::render('Specialists');
    }

    public function news(): Response 
    {
        return Inertia::render('News');
    }

    public function guarantees(): Response 
    {
        return Inertia::render('Guarantees');
    }
}

