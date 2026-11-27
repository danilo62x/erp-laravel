@php($values = [
    ['icon' => 'shield-check', 'key' => 'trust'],
    ['icon' => 'zap', 'key' => 'speed'],
    ['icon' => 'heart', 'key' => 'care'],
])
@php($stats = ['companies', 'volume', 'team', 'years'])
<x-layout.public :pageTitle="$pageTitle ?? null">
    <div class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h1 class="text-title-md font-bold text-heading" x-text="$t('about.title')"></h1>
            <p class="mt-4 text-muted" x-text="$t('about.subtitle')"></p>
        </div>

        <div class="surface-card mt-12 rounded-2xl border p-8">
            <p class="text-body" x-text="$t('about.body1')"></p>
            <p class="mt-4 text-body" x-text="$t('about.body2')"></p>
        </div>

        <div class="mt-12 grid grid-cols-2 gap-6 sm:grid-cols-4">
            @foreach ($stats as $s)
                <div class="text-center">
                    <p class="text-title-sm font-bold text-brand-500" x-text="$t('about.stats.{{ $s }}.value')"></p>
                    <p class="mt-1 text-sm text-muted" x-text="$t('about.stats.{{ $s }}.label')"></p>
                </div>
            @endforeach
        </div>

        <div class="mt-14 grid gap-6 sm:grid-cols-3">
            @foreach ($values as $v)
                <div class="surface-card rounded-2xl border p-6 text-center shadow-theme-sm">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                        <x-icon name="{{ $v['icon'] }}" :size="24" />
                    </span>
                    <h3 class="mt-4 text-lg font-semibold text-heading" x-text="$t('about.values.{{ $v['key'] }}.title')"></h3>
                    <p class="mt-2 text-sm text-muted" x-text="$t('about.values.{{ $v['key'] }}.desc')"></p>
                </div>
            @endforeach
        </div>
    </div>
</x-layout.public>
