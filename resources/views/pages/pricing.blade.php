@php($plans = [
    ['key' => 'starter', 'features' => ['f1', 'f2', 'f3']],
    ['key' => 'business', 'features' => ['f1', 'f2', 'f3', 'f4', 'f5']],
    ['key' => 'enterprise', 'features' => ['f1', 'f2', 'f3', 'f4', 'f5', 'f6']],
])
<x-layout.public :pageTitle="$pageTitle ?? null">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h1 class="text-title-md font-bold text-heading" x-text="$t('pricing.title')"></h1>
            <p class="mt-4 text-muted" x-text="$t('pricing.subtitle')"></p>
        </div>
        <div class="mt-14 grid gap-6 lg:grid-cols-3">
            @foreach ($plans as $i => $p)
                <div class="relative rounded-2xl border p-8 {{ $i === 1 ? 'border-brand-500 shadow-theme-lg' : 'border-themed surface-card' }}">
                    @if ($i === 1)
                        <span class="absolute -top-3 left-8 rounded-full bg-brand-500 px-3 py-0.5 text-xs font-medium text-white" x-text="$t('pricing.popular')"></span>
                    @endif
                    <h3 class="text-lg font-bold text-heading" x-text="$t('pricing.plans.{{ $p['key'] }}.name')"></h3>
                    <p class="mt-3 text-4xl font-bold text-heading" x-text="$t('pricing.plans.{{ $p['key'] }}.price')"></p>
                    <p class="mt-1 text-sm text-muted" x-text="$t('pricing.plans.{{ $p['key'] }}.period')"></p>
                    <ul class="mt-6 space-y-3">
                        @foreach ($p['features'] as $f)
                            <li class="flex items-center gap-2 text-sm text-body">
                                <x-icon name="check-circle" :size="16" class="text-success-500" />
                                <span x-text="$t('pricing.features.{{ $f }}')"></span>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}"
                        class="mt-8 flex h-11 items-center justify-center rounded-lg text-sm font-medium transition {{ $i === 1 ? 'bg-brand-500 text-white hover:bg-brand-600' : 'border border-themed text-body hover:bg-gray-50 dark:hover:bg-gray-800' }}"
                        x-text="$t('pricing.choose')"></a>
                </div>
            @endforeach
        </div>
    </div>
</x-layout.public>
