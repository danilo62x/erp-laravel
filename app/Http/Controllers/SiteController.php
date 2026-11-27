<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Public marketing site (mirrors erp/react src/pages/public): landing,
 * features, pricing, about, contact.
 */
class SiteController extends Controller
{
    public function home(): View
    {
        return view('pages.home', ['pageTitle' => null]);
    }

    public function features(): View
    {
        return view('pages.features', ['pageTitle' => 'Recursos']);
    }

    public function pricing(): View
    {
        return view('pages.pricing', ['pageTitle' => 'Preços']);
    }

    public function about(): View
    {
        return view('pages.about', ['pageTitle' => 'Sobre']);
    }

    public function contact(): View
    {
        return view('pages.contact', ['pageTitle' => 'Contato']);
    }
}
