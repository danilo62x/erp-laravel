<x-layout.app :pageTitle="$pageTitle ?? null">
    <div x-data="{
        period: 0,
        periods: [0, 2, 5],
        get dre() { return erHelpers.dreSummary($store.fin.entries, this.period); },
        get cc() {
            const cutoff = new Date();
            cutoff.setMonth(cutoff.getMonth() - this.period);
            cutoff.setDate(1);
            const scoped = $store.fin.entries.filter((e) => new Date(e.dueDate).getTime() >= cutoff.getTime());
            return erHelpers.resultByCostCenter(scoped);
        },
    }">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <nav aria-label="Breadcrumb" class="mb-2">
                    <ol class="flex flex-wrap items-center gap-1.5 text-sm">
                        <li class="flex items-center gap-1.5">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-muted transition hover:text-brand-600" x-text="$t('appNav.dashboard')"></a>
                            <x-icon name="chevron-right" :size="14" class="text-gray-400" />
                        </li>
                        <li><span aria-current="page" class="font-medium text-heading" x-text="$t('appNav.reports')"></span></li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold text-heading" x-text="$t('reports.title')"></h1>
                <p class="mt-1 text-sm text-muted" x-text="$t('reports.subtitle')"></p>
            </div>
            <select x-model.number="period" class="focus-ring h-10 rounded-lg border border-themed bg-transparent px-3 text-sm text-body">
                <template x-for="p in periods" :key="p">
                    <option :value="p" x-text="$t('reports.periods.' + p)"></option>
                </template>
            </select>
        </div>

        <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-fin.kpi-card title="$t('reports.revenueLiquid')" value="$currency(dre.receitaLiquida)" icon="arrow-down-left" accent="success" />
            <x-fin.kpi-card title="$t('reports.grossProfit')" value="$currency(dre.lucroBruto)" icon="trending-up" accent="brand" />
            <div class="surface-card rounded-2xl border p-5 shadow-theme-sm">
                <div class="flex items-center justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl" :class="dre.resultadoLiquido >= 0 ? 'bg-success-50 dark:bg-success-500/15' : 'bg-error-50 dark:bg-error-500/15'">
                        <x-icon name="scale" :size="20" :class="'text-' + (dre.resultadoLiquido >= 0 ? 'success-600' : 'error-500')" />
                    </div>
                </div>
                <p class="mt-4 text-sm text-muted" x-text="$t('reports.netResult')"></p>
                <h4 class="mt-1 text-2xl font-bold tabular-nums text-heading" x-text="$net(dre.resultadoLiquido)"></h4>
            </div>
            <x-fin.kpi-card title="$t('reports.netMargin')" value="$percent(dre.margemLiquida)" icon="percent" accent="info" />
        </div>

        <div class="grid gap-6 lg:grid-cols-5">
            {{-- DRE statement --}}
            <div class="surface-card rounded-2xl border p-6 shadow-theme-sm lg:col-span-3">
                <h3 class="mb-2 text-base font-semibold text-heading" x-text="$t('reports.dreTitle')"></h3>
                <p class="mb-4 text-xs text-muted" x-text="$t('reports.competence')"></p>

                <div class="flex items-center justify-between py-2.5">
                    <span class="text-body" x-text="$t('reports.lines.receitaBruta')"></span>
                    <span class="tabular-nums text-success-600 dark:text-success-500" x-text="$currency(dre.receitaBruta)"></span>
                </div>
                <div class="flex items-center justify-between py-2.5">
                    <span class="pl-4 text-body" x-text="$t('reports.lines.deducoes')"></span>
                    <span class="tabular-nums text-error-600 dark:text-error-500" x-text="'(' + $currency(dre.deducoes) + ')'"></span>
                </div>
                <div class="flex items-center justify-between border-t border-themed py-2.5 font-semibold">
                    <span class="text-body" x-text="$t('reports.lines.receitaLiquida')"></span>
                    <span class="tabular-nums" :class="dre.receitaLiquida >= 0 ? 'text-heading' : 'text-error-600'" x-text="$net(dre.receitaLiquida)"></span>
                </div>
                <div class="flex items-center justify-between py-2.5">
                    <span class="pl-4 text-body" x-text="$t('reports.lines.custos')"></span>
                    <span class="tabular-nums text-error-600 dark:text-error-500" x-text="'(' + $currency(dre.custos) + ')'"></span>
                </div>
                <div class="flex items-center justify-between border-t border-themed py-2.5 font-semibold">
                    <span class="text-body" x-text="$t('reports.lines.lucroBruto')"></span>
                    <span class="tabular-nums" :class="dre.lucroBruto >= 0 ? 'text-heading' : 'text-error-600'" x-text="$net(dre.lucroBruto)"></span>
                </div>
                <div class="flex items-center justify-between py-2.5">
                    <span class="pl-4 text-body" x-text="$t('reports.lines.despesasOperacionais')"></span>
                    <span class="tabular-nums text-error-600 dark:text-error-500" x-text="'(' + $currency(dre.despesasOperacionais) + ')'"></span>
                </div>
                <div class="flex items-center justify-between border-t border-themed py-2.5 font-semibold">
                    <span class="text-body" x-text="$t('reports.lines.resultadoOperacional')"></span>
                    <span class="tabular-nums" :class="dre.resultadoOperacional >= 0 ? 'text-heading' : 'text-error-600'" x-text="$net(dre.resultadoOperacional)"></span>
                </div>
                <div class="flex items-center justify-between py-2.5">
                    <span class="pl-4 text-body" x-text="$t('reports.lines.receitasFinanceiras')"></span>
                    <span class="tabular-nums text-success-600 dark:text-success-500" x-text="$currency(dre.receitasFinanceiras)"></span>
                </div>
                <div class="flex items-center justify-between py-2.5">
                    <span class="pl-4 text-body" x-text="$t('reports.lines.despesasFinanceiras')"></span>
                    <span class="tabular-nums text-error-600 dark:text-error-500" x-text="'(' + $currency(dre.despesasFinanceiras) + ')'"></span>
                </div>
                <div class="flex items-center justify-between border-t-2 border-themed py-2.5 text-base font-bold">
                    <span class="text-body" x-text="$t('reports.lines.resultadoLiquido')"></span>
                    <span class="tabular-nums" :class="dre.resultadoLiquido >= 0 ? 'text-heading' : 'text-error-600'" x-text="$net(dre.resultadoLiquido)"></span>
                </div>
            </div>

            {{-- By cost center --}}
            <div class="surface-card overflow-hidden rounded-2xl border shadow-theme-sm lg:col-span-2">
                <div class="border-b border-themed px-5 py-4">
                    <h3 class="text-base font-semibold text-heading" x-text="$t('reports.byCostCenter')"></h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-themed text-muted">
                                <th class="px-5 py-3 font-medium" x-text="$t('entryForm.costCenter')"></th>
                                <th class="px-5 py-3 text-right font-medium" x-text="$t('cashflow.net')"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="row in cc" :key="row.costCenter">
                                <tr class="border-b border-themed last:border-0">
                                    <td class="px-5 py-3 text-body" x-text="$t('costCenter.' + row.costCenter)"></td>
                                    <td class="px-5 py-3 text-right font-medium tabular-nums" :class="row.net >= 0 ? 'text-success-600' : 'text-error-600'" x-text="$net(row.net)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
