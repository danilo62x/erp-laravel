<x-layout.blank :pageTitle="$pageTitle ?? null">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 text-center" style="background-color: var(--tx-body-bg)" x-data>
        <span class="flex h-20 w-20 items-center justify-center rounded-3xl bg-brand-50 text-brand-500 dark:bg-brand-500/15">
            <x-icon name="alert-triangle" :size="40" />
        </span>
        <p class="mt-6 text-title-md font-bold text-brand-500">404</p>
        <p class="mt-3 max-w-sm text-sm text-muted" x-text="$t('notFound.message')"></p>
        <div class="mt-8 flex gap-3">
            <a href="{{ route('home') }}" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600" x-text="$t('notFound.back')"></a>
            <template x-if="$store.auth.isAuthenticated">
                <a href="{{ route('dashboard') }}" class="rounded-lg border border-themed px-5 py-2.5 text-sm font-medium text-body transition hover:bg-gray-50 dark:hover:bg-gray-800" x-text="$t('nav.dashboard')"></a>
            </template>
        </div>
    </div>
</x-layout.blank>
