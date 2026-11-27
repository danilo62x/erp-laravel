<x-layout.app :pageTitle="$pageTitle ?? null">
    <div x-data>
        <div class="mb-6">
            <nav aria-label="Breadcrumb" class="mb-2">
                <ol class="flex flex-wrap items-center gap-1.5 text-sm">
                    <li class="flex items-center gap-1.5">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-muted transition hover:text-brand-600" x-text="$t('appNav.dashboard')"></a>
                        <x-icon name="chevron-right" :size="14" class="text-gray-400" />
                    </li>
                    <li><span aria-current="page" class="font-medium text-heading" x-text="$t('appNav.entries')"></span></li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-heading" x-text="$t('entries.title')"></h1>
            <p class="mt-1 text-sm text-muted" x-text="$t('entries.subtitle')"></p>
        </div>

        <x-fin.ledger lockType="" />
    </div>
</x-layout.app>
