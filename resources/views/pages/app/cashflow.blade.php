<x-layout.app :pageTitle="$pageTitle ?? null">
    @push('head')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>
    @endpush

    <div x-data="{
        get series() { return erHelpers.cashFlowSeries($store.fin.entries, $store.fin.accounts, 5, 3); },
        now: Date.now(),
    }">
        <div class="mb-6">
            <nav aria-label="Breadcrumb" class="mb-2">
                <ol class="flex flex-wrap items-center gap-1.5 text-sm">
                    <li class="flex items-center gap-1.5">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-muted transition hover:text-brand-600" x-text="$t('appNav.dashboard')"></a>
                        <x-icon name="chevron-right" :size="14" class="text-gray-400" />
                    </li>
                    <li><span aria-current="page" class="font-medium text-heading" x-text="$t('appNav.cashflow')"></span></li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-heading" x-text="$t('cashflow.title')"></h1>
            <p class="mt-1 text-sm text-muted" x-text="$t('cashflow.subtitle')"></p>
        </div>

        <div class="surface-card mb-6 rounded-2xl border p-5 shadow-theme-sm">
            <h3 class="mb-4 text-base font-semibold text-heading" x-text="$t('cashflow.projection')"></h3>
            <x-fin.cash-flow-chart seriesExpr="series" :height="340" />
        </div>

        <div class="surface-card overflow-hidden rounded-2xl border shadow-theme-sm">
            <div class="border-b border-themed px-5 py-4">
                <h3 class="text-base font-semibold text-heading" x-text="$t('cashflow.byPeriod')"></h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-themed text-muted">
                            <th class="px-5 py-3 font-medium" x-text="$t('cashflow.period')"></th>
                            <th class="px-5 py-3 text-right font-medium" x-text="$t('cashflow.inflow')"></th>
                            <th class="px-5 py-3 text-right font-medium" x-text="$t('cashflow.outflow')"></th>
                            <th class="px-5 py-3 text-right font-medium" x-text="$t('cashflow.net')"></th>
                            <th class="px-5 py-3 text-right font-medium" x-text="$t('cashflow.balance')"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="p in series" :key="p.month">
                            <tr class="border-b border-themed last:border-0" :class="(new Date(p.month).getTime() > now) ? 'opacity-70' : ''">
                                <td class="px-5 py-3 font-medium text-heading">
                                    <span x-text="$month(p.month)"></span>
                                    <span x-show="new Date(p.month).getTime() > now" class="ml-2 text-xs text-muted" x-text="'(' + $t('cashflow.projected') + ')'"></span>
                                </td>
                                <td class="px-5 py-3 text-right tabular-nums text-success-600 dark:text-success-500" x-text="$currency(p.inflow)"></td>
                                <td class="px-5 py-3 text-right tabular-nums text-error-600 dark:text-error-500" x-text="$currency(p.outflow)"></td>
                                <td class="px-5 py-3 text-right font-medium tabular-nums" :class="(p.inflow - p.outflow) >= 0 ? 'text-success-600' : 'text-error-600'" x-text="$net(p.inflow - p.outflow)"></td>
                                <td class="px-5 py-3 text-right font-semibold tabular-nums text-heading" x-text="$currency(p.balance)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout.app>
