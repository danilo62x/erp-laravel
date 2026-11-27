@php($features = [
    ['icon' => 'arrow-up-right', 'key' => 'payable'],
    ['icon' => 'arrow-down-left', 'key' => 'receivable'],
    ['icon' => 'trending-up', 'key' => 'cashflow'],
    ['icon' => 'receipt', 'key' => 'invoices'],
    ['icon' => 'pie-chart', 'key' => 'dre'],
    ['icon' => 'shield-check', 'key' => 'security'],
])
@php($stats = ['companies', 'volume', 'rating', 'uptime'])
@php($plans = ['starter', 'business', 'enterprise'])
@php($testimonials = ['t1', 't2', 't3'])
<x-layout.public :pageTitle="$pageTitle ?? null">
    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <div class="absolute -top-40 right-0 h-96 w-96 rounded-full bg-brand-200/40 blur-3xl dark:bg-brand-500/10"></div>
            <div class="absolute top-40 -left-20 h-80 w-80 rounded-full bg-success-100/50 blur-3xl dark:bg-brand-700/10"></div>
        </div>
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:py-24 lg:px-8">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full border border-themed px-3 py-1 text-xs font-medium text-brand-600 surface-card">
                    <x-icon name="bolt" :size="13" /> <span x-text="$t('landing.hero.badge')"></span>
                </span>
                <h1 class="mt-5 text-4xl font-bold leading-tight text-heading sm:text-5xl lg:text-title-lg" x-text="$t('landing.hero.title')"></h1>
                <p class="mt-5 max-w-lg text-lg text-muted" x-text="$t('landing.hero.subtitle')"></p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="inline-flex h-12 items-center gap-2 rounded-lg bg-brand-500 px-6 text-sm font-semibold text-white transition hover:bg-brand-600">
                        <x-icon name="wallet" :size="18" /> <span x-text="$t('landing.hero.ctaPrimary')"></span>
                    </a>
                    <a href="{{ route('features') }}" class="inline-flex h-12 items-center gap-2 rounded-lg border border-themed px-6 text-sm font-semibold text-body transition hover:bg-gray-50 dark:hover:bg-gray-800" x-text="$t('landing.hero.ctaSecondary')"></a>
                </div>
                <p class="mt-4 text-xs text-muted" x-text="$t('landing.hero.noCard')"></p>
            </div>

            {{-- Hero dashboard mockup --}}
            <div class="relative mx-auto w-full max-w-md">
                <div class="surface-card relative overflow-hidden rounded-3xl border p-6 shadow-theme-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-muted" x-text="$t('landing.hero.cardLabel')"></p>
                            <p class="mt-1 text-2xl font-bold text-heading">R$ 289.060,85</p>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-600 dark:bg-success-500/15">
                            <x-icon name="arrow-up-right" :size="12" :stroke-width="2.4" /> 12,4%
                        </span>
                    </div>
                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <div class="rounded-xl bg-success-50 p-3 dark:bg-success-500/10">
                            <p class="text-xs text-muted" x-text="$t('appNav.receivable')"></p>
                            <p class="mt-1 text-sm font-bold text-success-600">R$ 142.300</p>
                        </div>
                        <div class="rounded-xl bg-error-50 p-3 dark:bg-error-500/10">
                            <p class="text-xs text-muted" x-text="$t('appNav.payable')"></p>
                            <p class="mt-1 text-sm font-bold text-error-600">R$ 96.540</p>
                        </div>
                    </div>
                    <div class="mt-4 flex h-24 items-end gap-1.5">
                        @foreach ([40, 65, 48, 72, 58, 84, 76] as $h)
                            <div class="flex-1 rounded-t bg-brand-500/80" style="height: {{ $h }}%"></div>
                        @endforeach
                    </div>
                </div>
                <div class="surface-card absolute -bottom-6 -left-6 hidden rounded-2xl border p-4 shadow-theme-lg sm:block">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-warning-50 text-warning-600 dark:bg-warning-500/15">
                            <x-icon name="alert-triangle" :size="20" />
                        </span>
                        <div>
                            <p class="text-xs text-muted" x-text="$t('dashboard.overdue')"></p>
                            <p class="text-sm font-bold text-heading">R$ 18.240</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="border-y border-themed surface-card">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-8 px-4 py-12 sm:px-6 lg:grid-cols-4 lg:px-8">
            @foreach ($stats as $s)
                <div class="text-center">
                    <p class="text-title-sm font-bold text-brand-500" x-text="$t('landing.stats.{{ $s }}.value')"></p>
                    <p class="mt-1 text-sm text-muted" x-text="$t('landing.stats.{{ $s }}.label')"></p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-title-sm font-bold text-heading" x-text="$t('landing.features.title')"></h2>
            <p class="mt-3 text-muted" x-text="$t('landing.features.subtitle')"></p>
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($features as $f)
                <div class="surface-card rounded-2xl border p-6 shadow-theme-sm transition hover:shadow-theme-md">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                        <x-icon name="{{ $f['icon'] }}" :size="24" />
                    </span>
                    <h3 class="mt-5 text-lg font-semibold text-heading" x-text="$t('landing.features.items.{{ $f['key'] }}.title')"></h3>
                    <p class="mt-2 text-sm text-muted" x-text="$t('landing.features.items.{{ $f['key'] }}.desc')"></p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Plans preview --}}
    <section id="plans" class="border-t border-themed surface-card">
        <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-title-sm font-bold text-heading" x-text="$t('landing.plans.title')"></h2>
                <p class="mt-3 text-muted" x-text="$t('landing.plans.subtitle')"></p>
            </div>
            <div class="mt-12 grid gap-6 lg:grid-cols-3">
                @foreach ($plans as $i => $p)
                    <div class="relative rounded-2xl border p-6 {{ $i === 1 ? 'border-brand-500 shadow-theme-lg' : 'border-themed surface-card' }}">
                        @if ($i === 1)
                            <span class="absolute -top-3 left-6 rounded-full bg-brand-500 px-3 py-0.5 text-xs font-medium text-white" x-text="$t('pricing.popular')"></span>
                        @endif
                        <h3 class="text-lg font-bold text-heading" x-text="$t('pricing.plans.{{ $p }}.name')"></h3>
                        <p class="mt-2 text-3xl font-bold text-heading" x-text="$t('pricing.plans.{{ $p }}.price')"></p>
                        <p class="mt-1 text-sm text-muted" x-text="$t('pricing.plans.{{ $p }}.period')"></p>
                        <a href="{{ route('pricing') }}"
                            class="mt-6 flex h-11 items-center justify-center rounded-lg text-sm font-medium transition {{ $i === 1 ? 'bg-brand-500 text-white hover:bg-brand-600' : 'border border-themed text-body hover:bg-gray-50 dark:hover:bg-gray-800' }}"
                            x-text="$t('pricing.choose')"></a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-title-sm font-bold text-heading" x-text="$t('landing.testimonials.title')"></h2>
        </div>
        <div class="mt-12 grid gap-6 lg:grid-cols-3">
            @foreach ($testimonials as $tk)
                <figure class="surface-card rounded-2xl border p-6 shadow-theme-sm">
                    <div class="flex gap-0.5 text-warning-500">
                        @for ($i = 0; $i < 5; $i++)<x-icon name="star" :size="16" />@endfor
                    </div>
                    <blockquote class="mt-4 text-sm text-body" x-text="'“' + $t('landing.testimonials.{{ $tk }}.quote') + '”'"></blockquote>
                    <figcaption class="mt-4 text-sm font-semibold text-heading" x-text="$t('landing.testimonials.{{ $tk }}.author')"></figcaption>
                </figure>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 to-brand-400 px-8 py-14 text-center text-white">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10"></div>
            <h2 class="relative text-title-sm font-bold" x-text="$t('landing.cta.title')"></h2>
            <p class="relative mx-auto mt-3 max-w-xl text-white/80" x-text="$t('landing.cta.subtitle')"></p>
            <a href="{{ route('register') }}" class="relative mt-7 inline-flex h-12 items-center gap-2 rounded-lg bg-white px-7 text-sm font-semibold text-brand-600 transition hover:bg-white/90">
                <x-icon name="wallet" :size="18" /> <span x-text="$t('landing.cta.button')"></span>
            </a>
        </div>
    </section>
</x-layout.public>
