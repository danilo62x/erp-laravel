{{-- Shared <head> contents + window.ER bootstrap for all layouts. --}}
<meta charset="UTF-8" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="manifest" href="/manifest.webmanifest" />
<meta name="theme-color" content="#465fff" />
<title>{{ isset($pageTitle) && $pageTitle ? $pageTitle . ' — ' : '' }}ERP — Financeiro &amp; Fluxo de Caixa</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script>
    // Canonical mock data shared with the Alpine stores (mirrors erp/react src/data + stores).
    window.ER = {
        entries: @json(\App\Support\ErData::entries()),
        accounts: @json(\App\Support\ErData::accounts()),
        parties: @json(\App\Support\ErData::parties()),
        invoices: @json(\App\Support\ErData::invoices()),
        categories: @json(\App\Support\ErData::categories()),
        user: @json(session('user')),
    };
</script>
@stack('head')
