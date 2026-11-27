<x-layout.app :pageTitle="$pageTitle ?? null">
    @push('head')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>
    @endpush

    <div x-data="{
        get entries() { return $store.fin.entries; },
        get accounts() { return $store.fin.accounts; },
        get m() { return erHelpers.computeMetrics(this.entries, this.accounts); },
        get cashflow() { return erHelpers.cashFlowSeries(this.entries, this.accounts); },
        get revExp() { return erHelpers.revenueVsExpense(this.entries); },
        get byCat() { return erHelpers.expensesByCategory(this.entries); },
        get due() { return erHelpers.dueSoon(this.entries, 30).slice(0, 5); },
        get recent() { return erHelpers.sortByDueDesc(this.entries).slice(0, 5); },
        detail: null,
    }">
        {{-- Page header --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-heading" x-text="$t('dashboard.title')"></h1>
                <p class="mt-1 text-sm text-muted" x-text="$t('dashboard.subtitle')"></p>
            </div>
        </div>

        {{-- KPIs --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-fin.kpi-card title="$t('dashboard.totalBalance')" value="$currency(m.totalBalance)" icon="wallet" accent="brand" hint="$t('dashboard.acrossAccounts', { n: accounts.length })" />
            <x-fin.kpi-card title="$t('dashboard.receivable')" value="$currency(m.receivableOpen)" icon="arrow-down-left" accent="success" hint="$t('dashboard.openReceivable')" />
            <x-fin.kpi-card title="$t('dashboard.payable')" value="$currency(m.payableOpen)" icon="arrow-up-right" accent="warning" hint="$t('dashboard.openPayable')" />
            <x-fin.kpi-card title="$t('dashboard.overdue')" value="$currency(m.overdue)" icon="alert-triangle" accent="error" hint="$t('dashboard.needsAttention')" />
        </div>

        {{-- Cash flow + donut --}}
        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="surface-card rounded-2xl border p-5 shadow-theme-sm lg:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-heading" x-text="$t('dashboard.cashflowTitle')"></h3>
                    <span class="text-sm font-semibold" :class="m.monthResult >= 0 ? 'text-success-600' : 'text-error-600'"
                        x-text="$t('dashboard.monthResult') + ': ' + $currency(m.monthResult)"></span>
                </div>
                <x-fin.cash-flow-chart seriesExpr="cashflow" />
            </div>

            <div class="surface-card rounded-2xl border p-5 shadow-theme-sm">
                <h3 class="mb-4 text-base font-semibold text-heading" x-text="$t('dashboard.expensesByCategory')"></h3>
                <template x-if="byCat.length > 0">
                    <x-fin.category-donut seriesExpr="byCat" :height="320" />
                </template>
                <template x-if="byCat.length === 0">
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <x-icon name="pie-chart" :size="32" class="text-muted" />
                        <p class="mt-3 text-sm text-muted" x-text="$t('common.noResults')"></p>
                    </div>
                </template>
            </div>
        </div>

        {{-- Revenue vs expense + due soon --}}
        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="surface-card rounded-2xl border p-5 shadow-theme-sm lg:col-span-2">
                <h3 class="mb-4 text-base font-semibold text-heading" x-text="$t('dashboard.revenueVsExpense')"></h3>
                <div x-data="{
                        chart: null,
                        build() {
                            const base = window.erChart.base();
                            const data = revExp;
                            const options = {
                                ...base,
                                chart: { ...base.chart, type: 'bar', height: 300 },
                                colors: ['#12b76a', '#f04438'],
                                plotOptions: { bar: { columnWidth: '55%', borderRadius: 4 } },
                                series: [
                                    { name: $t('entryType.receita'), data: data.map((p) => Math.round(p.receita)) },
                                    { name: $t('entryType.despesa'), data: data.map((p) => Math.round(p.despesa)) },
                                ],
                                xaxis: { categories: data.map((p) => erHelpers.formatMonthShort(p.month)) },
                                yaxis: { labels: { formatter: (v) => erHelpers.formatCompactCurrency(v) } },
                                tooltip: { theme: base.tooltip.theme, y: { formatter: (v) => erHelpers.formatCurrency(v) } },
                                legend: { position: 'top', horizontalAlign: 'right' },
                            };
                            if (this.chart) this.chart.destroy();
                            this.chart = new ApexCharts(this.$refs.bar, options);
                            this.chart.render();
                        },
                    }"
                    x-init="build(); $watch('$store.ui.darkMode', () => build()); $watch('$store.ui.lang', () => build())">
                    <div x-ref="bar"></div>
                </div>
            </div>

            <div class="surface-card rounded-2xl border p-5 shadow-theme-sm">
                <h3 class="mb-3 text-base font-semibold text-heading" x-text="$t('dashboard.dueSoon')"></h3>
                <template x-if="due.length > 0">
                    <div class="space-y-1">
                        <template x-for="entry in due" :key="entry.id">
                            <x-fin.entry-row entry="entry" onClick="detail = entry" />
                        </template>
                    </div>
                </template>
                <template x-if="due.length === 0">
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <x-icon name="check-circle" :size="32" class="text-success-500" />
                        <p class="mt-3 text-sm text-muted" x-text="$t('dashboard.allClear')"></p>
                    </div>
                </template>
            </div>
        </div>

        {{-- Recent entries --}}
        <div class="mt-6 surface-card rounded-2xl border p-5 shadow-theme-sm">
            <h3 class="mb-3 text-base font-semibold text-heading" x-text="$t('dashboard.recentEntries')"></h3>
            <div class="grid gap-1 sm:grid-cols-2">
                <template x-for="entry in recent" :key="entry.id">
                    <x-fin.entry-row entry="entry" onClick="detail = entry" />
                </template>
            </div>
        </div>

        {{-- Detail drawer --}}
        <div x-show="detail" x-cloak class="fixed inset-0 z-[60]">
            <div class="absolute inset-0 bg-gray-900/50" @click="detail = null"></div>
            <div x-show="detail" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                class="surface-card absolute right-0 top-0 flex h-full w-full max-w-md flex-col border-l p-6 shadow-theme-xl">
                <template x-if="detail">
                    <div class="flex h-full flex-col">
                        <div class="mb-5 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-heading" x-text="$t('entryDetail.title')"></h3>
                            <button type="button" @click="detail = null" class="flex h-9 w-9 items-center justify-center rounded-lg text-muted hover:bg-gray-100 dark:hover:bg-gray-800">
                                <x-icon name="x" :size="18" />
                            </button>
                        </div>
                        <div class="space-y-5 overflow-y-auto">
                            <div class="text-center">
                                <span class="text-2xl font-semibold tabular-nums" :class="erHelpers.amountColor(detail.type)" x-text="$signed(detail.amount, detail.type)"></span>
                                <p class="mt-1 text-sm text-muted" x-text="detail.description"></p>
                                <div class="mt-3 flex justify-center"><x-fin.status-pill status="erHelpers.liveStatus(detail)" /></div>
                            </div>
                            <div class="surface-card rounded-xl border p-4">
                                <div class="flex items-center justify-between gap-4 border-b border-themed py-3">
                                    <span class="text-sm text-muted" x-text="$t('entryForm.category')"></span>
                                    <span class="text-sm font-medium text-heading" x-text="erHelpers.categoryName(detail.categoryId)"></span>
                                </div>
                                <div class="flex items-center justify-between gap-4 border-b border-themed py-3">
                                    <span class="text-sm text-muted" x-text="$t('entryForm.dueDate')"></span>
                                    <span class="text-sm font-medium text-heading" x-text="$date(detail.dueDate)"></span>
                                </div>
                                <div class="flex items-center justify-between gap-4 border-b border-themed py-3">
                                    <span class="text-sm text-muted" x-text="$t('entryForm.account')"></span>
                                    <span class="text-sm font-medium text-heading" x-text="$store.fin.getAccount(detail.accountId)?.name"></span>
                                </div>
                                <div class="flex items-center justify-between gap-4 py-3">
                                    <span class="text-sm text-muted" x-text="$t('entryForm.' + (detail.type === 'receita' ? 'cliente' : 'fornecedor'))"></span>
                                    <span class="text-sm font-medium text-heading" x-text="$store.fin.partyName(detail.partyId)"></span>
                                </div>
                            </div>
                            <template x-if="erHelpers.liveStatus(detail) !== 'pago'">
                                <button type="button" @click="$store.fin.markPaid(detail.id); $store.ui.notifySuccess($t('entryDetail.markedPaid')); detail = null"
                                    class="focus-ring inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-success-500 text-sm font-medium text-white transition hover:bg-success-600">
                                    <x-icon name="check-circle" :size="18" /> <span x-text="$t('entryDetail.markPaid')"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</x-layout.app>
