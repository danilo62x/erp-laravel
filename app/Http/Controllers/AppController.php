<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Authenticated financial area — dashboard + settings. Ledger / AP / AR /
 * cash-flow / invoices / reports pages live in AppFinController.
 * (mirrors erp/react src/pages/app)
 */
class AppController extends Controller
{
    public function dashboard(): View
    {
        return view('pages.app.dashboard', ['pageTitle' => 'Painel']);
    }

    public function settings(): View
    {
        return view('pages.app.settings', ['pageTitle' => 'Configurações']);
    }
}
