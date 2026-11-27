@php($items = [
    ['icon' => 'arrow-up-right', 'key' => 'payable'],
    ['icon' => 'arrow-down-left', 'key' => 'receivable'],
    ['icon' => 'trending-up', 'key' => 'cashflow'],
    ['icon' => 'clipboard-list', 'key' => 'entries'],
    ['icon' => 'receipt', 'key' => 'invoices'],
    ['icon' => 'pie-chart', 'key' => 'dre'],
    ['icon' => 'bank', 'key' => 'reconcile'],
    ['icon' => 'list-checks', 'key' => 'chart'],
    ['icon' => 'shield-check', 'key' => 'security'],
])
<x-layout.public :pageTitle="$pageTitle ?? null">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h1 class="text-title-md font-bold text-heading" x-text="$t('features.title')"></h1>
            <p class="mt-4 text-muted" x-text="$t('features.subtitle')"></p>
        </div>
        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($items as $f)
                <div class="surface-card rounded-2xl border p-6 shadow-theme-sm">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                        <x-icon name="{{ $f['icon'] }}" :size="24" />
                    </span>
                    <h3 class="mt-5 text-lg font-semibold text-heading" x-text="$t('features.items.{{ $f['key'] }}.title')"></h3>
                    <p class="mt-2 text-sm text-muted" x-text="$t('features.items.{{ $f['key'] }}.desc')"></p>
                </div>
            @endforeach
        </div>
    </div>
</x-layout.public>
