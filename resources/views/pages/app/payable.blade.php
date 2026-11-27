<x-layout.app :pageTitle="$pageTitle ?? null">
    <div x-data="{ get ap() { return erHelpers.payable($store.fin.entries); } }">
        <div class="mb-6">
            <nav aria-label="Breadcrumb" class="mb-2">
                <ol class="flex flex-wrap items-center gap-1.5 text-sm">
                    <li class="flex items-center gap-1.5">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-muted transition hover:text-brand-600" x-text="$t('appNav.dashboard')"></a>
                        <x-icon name="chevron-right" :size="14" class="text-gray-400" />
                    </li>
                    <li><span aria-current="page" class="font-medium text-heading" x-text="$t('appNav.payable')"></span></li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-heading" x-text="$t('payable.title')"></h1>
            <p class="mt-1 text-sm text-muted" x-text="$t('payable.subtitle')"></p>
        </div>

        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            <x-fin.kpi-card title="$t('payable.open')" value="$currency(erHelpers.openTotal(ap))" icon="arrow-up-right" accent="warning" />
            <x-fin.kpi-card title="$t('payable.overdue')" value="$currency(erHelpers.overdueTotal(ap))" icon="alert-triangle" accent="error" />
            <x-fin.kpi-card title="$t('payable.total')" value="$currency(ap.reduce((s, e) => s + e.amount, 0))" icon="clipboard-list" accent="brand" />
        </div>

        <x-fin.ledger lockType="despesa" />
    </div>
</x-layout.app>
