<x-layout.app :pageTitle="$pageTitle ?? null">
    <div x-data="{
        filters: { query: '', type: '', status: '' },
        get rows() { return erHelpers.filterInvoices($store.fin.invoices, this.filters); },
        statusColor(s) { return { autorizada: 'success', pendente: 'warning', cancelada: 'error' }[s] || 'gray'; },
    }">
        <div class="mb-6">
            <nav aria-label="Breadcrumb" class="mb-2">
                <ol class="flex flex-wrap items-center gap-1.5 text-sm">
                    <li class="flex items-center gap-1.5">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-muted transition hover:text-brand-600" x-text="$t('appNav.dashboard')"></a>
                        <x-icon name="chevron-right" :size="14" class="text-gray-400" />
                    </li>
                    <li><span aria-current="page" class="font-medium text-heading" x-text="$t('appNav.invoices')"></span></li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-heading" x-text="$t('invoices.title')"></h1>
            <p class="mt-1 text-sm text-muted" x-text="$t('invoices.subtitle')"></p>
        </div>

        <div class="surface-card mb-4 flex flex-wrap items-center gap-2 rounded-xl border p-3">
            <div class="relative min-w-[180px] flex-1">
                <span class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-muted"><x-icon name="search" :size="16" /></span>
                <input x-model="filters.query" :placeholder="$t('invoices.searchPh')"
                    class="focus-ring h-10 w-full rounded-lg border border-themed bg-transparent py-2 pr-3 pl-9 text-sm text-body" />
            </div>
            <select x-model="filters.type" class="focus-ring h-10 rounded-lg border border-themed bg-transparent px-3 text-sm text-body">
                <option value="" x-text="$t('invoices.allTypes')"></option>
                <option value="saida" x-text="$t('invoices.types.saida')"></option>
                <option value="entrada" x-text="$t('invoices.types.entrada')"></option>
            </select>
            <select x-model="filters.status" class="focus-ring h-10 rounded-lg border border-themed bg-transparent px-3 text-sm text-body">
                <option value="" x-text="$t('filters.allStatus')"></option>
                <option value="autorizada" x-text="$t('invoices.statuses.autorizada')"></option>
                <option value="pendente" x-text="$t('invoices.statuses.pendente')"></option>
                <option value="cancelada" x-text="$t('invoices.statuses.cancelada')"></option>
            </select>
        </div>

        <div class="surface-card overflow-hidden rounded-2xl border shadow-theme-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-themed text-muted">
                            <th class="px-5 py-3 font-medium" x-text="$t('invoices.number')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('invoices.type')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('table.party')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('invoices.issueDate')"></th>
                            <th class="px-5 py-3 text-right font-medium" x-text="$t('invoices.tax')"></th>
                            <th class="px-5 py-3 text-right font-medium" x-text="$t('invoices.amount')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('table.status')"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="inv in rows" :key="inv.id">
                            <tr class="border-b border-themed last:border-0">
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center gap-2 font-medium text-heading">
                                        <x-icon name="receipt" :size="16" class="text-brand-500" />
                                        <span x-text="inv.number"></span>
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                        :class="inv.type === 'saida' ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'"
                                        x-text="$t('invoices.types.' + inv.type)"></span>
                                </td>
                                <td class="px-5 py-3 text-body" x-text="$store.fin.partyName(inv.partyId)"></td>
                                <td class="px-5 py-3 tabular-nums text-body" x-text="$date(inv.issueDate)"></td>
                                <td class="px-5 py-3 text-right tabular-nums text-muted" x-text="$currency(inv.tax)"></td>
                                <td class="px-5 py-3 text-right font-semibold tabular-nums text-heading" x-text="$currency(inv.amount)"></td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                        :class="{
                                            'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500': statusColor(inv.status) === 'success',
                                            'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-500': statusColor(inv.status) === 'warning',
                                            'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500': statusColor(inv.status) === 'error',
                                        }"
                                        x-text="$t('invoices.statuses.' + inv.status)"></span>
                                </td>
                            </tr>
                        </template>
                        <template x-if="rows.length === 0">
                            <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-muted" x-text="$t('common.noResults')"></td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout.app>
