<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Authenticated financial pages: Accounts Payable, Accounts Receivable,
 * Cash Flow, Ledger entries, Invoices and Reports (DRE).
 * (mirrors erp/react src/pages/app)
 */
class AppFinController extends Controller
{
    public function payable(): View
    {
        return view('pages.app.payable', ['pageTitle' => 'Contas a Pagar']);
    }

    public function receivable(): View
    {
        return view('pages.app.receivable', ['pageTitle' => 'Contas a Receber']);
    }

    public function cashflow(): View
    {
        return view('pages.app.cashflow', ['pageTitle' => 'Fluxo de Caixa']);
    }

    public function entries(): View
    {
        return view('pages.app.entries', ['pageTitle' => 'Lançamentos']);
    }

    public function invoices(): View
    {
        return view('pages.app.invoices', ['pageTitle' => 'Notas Fiscais']);
    }

    public function reports(): View
    {
        return view('pages.app.reports', ['pageTitle' => 'Relatórios (DRE)']);
    }
}
