@props([
    'lockType' => '', {{-- '' = all (lançamentos), 'receita' = AR, 'despesa' = AP --}}
])
{{-- Reusable ledger table for Accounts Payable / Receivable / all entries:
     persisted filters + create/edit modal + mark-as-paid + detail drawer.
     Mirrors erp/react components/fin/LedgerView.tsx (+ FilterBar, EntryFormModal,
     EntryDetailDrawer). --}}
@php($lock = $lockType)
<div class="space-y-4"
    x-data="ledger(@js($lock))">

    {{-- New entry button --}}
    <div class="flex items-center justify-end">
        <button type="button" @click="openCreate()"
            class="focus-ring inline-flex h-10 items-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white transition hover:bg-brand-600">
            <x-icon name="plus" :size="16" /> <span x-text="$t('entryForm.newTitle')"></span>
        </button>
    </div>

    {{-- Filter bar (wired to persisted $store.fin.filters) --}}
    <div class="surface-card flex flex-wrap items-center gap-2 rounded-xl border p-3">
        <div class="relative min-w-[180px] flex-1">
            <span class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-muted"><x-icon name="search" :size="16" /></span>
            <input :value="$store.fin.filters.query" @input="$store.fin.setFilters({ query: $event.target.value }); page = 1"
                :placeholder="$t('filters.search')"
                class="focus-ring h-10 w-full rounded-lg border border-themed bg-transparent py-2 pr-3 pl-9 text-sm text-body" />
        </div>

        <select class="focus-ring h-10 rounded-lg border border-themed bg-transparent px-3 text-sm text-body"
            :value="$store.fin.filters.period" @change="$store.fin.setFilters({ period: $event.target.value }); page = 1">
            <template x-for="p in ['all','30d','90d','180d']" :key="p">
                <option :value="p" :selected="$store.fin.filters.period === p" x-text="$t('filters.period.' + p)"></option>
            </template>
        </select>

        <select class="focus-ring h-10 rounded-lg border border-themed bg-transparent px-3 text-sm text-body"
            :value="$store.fin.filters.status" @change="$store.fin.setFilters({ status: $event.target.value }); page = 1">
            <option value="" x-text="$t('filters.allStatus')"></option>
            <template x-for="s in ['pago','pendente','vencido']" :key="s">
                <option :value="s" :selected="$store.fin.filters.status === s" x-text="$t('status.' + s)"></option>
            </template>
        </select>

        <select class="focus-ring h-10 rounded-lg border border-themed bg-transparent px-3 text-sm text-body"
            :value="$store.fin.filters.categoryId" @change="$store.fin.setFilters({ categoryId: $event.target.value }); page = 1">
            <option value="" x-text="$t('filters.allCategories')"></option>
            <template x-for="c in categoryList" :key="c.id">
                <option :value="c.id" :selected="$store.fin.filters.categoryId === c.id" x-text="c.name"></option>
            </template>
        </select>

        <template x-if="!lockType">
            <select class="focus-ring h-10 rounded-lg border border-themed bg-transparent px-3 text-sm text-body"
                :value="$store.fin.filters.type" @change="$store.fin.setFilters({ type: $event.target.value }); page = 1">
                <option value="" x-text="$t('filters.allTypes')"></option>
                <option value="receita" x-text="$t('entryType.receita')"></option>
                <option value="despesa" x-text="$t('entryType.despesa')"></option>
            </select>
        </template>

        <button type="button" @click="$store.fin.resetFilters(); page = 1"
            class="focus-ring inline-flex h-10 items-center gap-1.5 rounded-lg border border-themed px-3 text-sm text-muted transition hover:text-body">
            <x-icon name="refresh-cw" :size="15" /> <span x-text="$t('filters.clear')"></span>
        </button>
    </div>

    {{-- Table --}}
    <div class="surface-card overflow-hidden rounded-2xl border shadow-theme-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-themed text-muted">
                        <th class="px-5 py-3 font-medium" x-text="$t('table.description')"></th>
                        <th class="px-5 py-3 font-medium" x-text="partyHeader"></th>
                        <th class="px-5 py-3 font-medium" x-text="$t('table.dueDate')"></th>
                        <th class="px-5 py-3 font-medium" x-text="$t('table.status')"></th>
                        <th class="px-5 py-3 text-right font-medium" x-text="$t('table.amount')"></th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="e in pageRows" :key="e.id">
                        <tr class="border-b border-themed last:border-0">
                            <td class="px-5 py-3">
                                <button type="button" @click="openDetail(e)" class="text-left">
                                    <span class="font-medium text-heading hover:text-brand-600" x-text="e.description"></span>
                                    <span class="block text-xs text-muted" x-text="erHelpers.categoryName(e.categoryId)"></span>
                                </button>
                            </td>
                            <td class="px-5 py-3 text-body" x-text="$store.fin.partyName(e.partyId)"></td>
                            <td class="px-5 py-3">
                                <x-fin.due-date-badge date="e.dueDate" paid="erHelpers.liveStatus(e) === 'pago'" />
                            </td>
                            <td class="px-5 py-3"><x-fin.status-pill status="erHelpers.liveStatus(e)" /></td>
                            <td class="px-5 py-3 text-right"><x-fin.amount-text value="e.amount" type="e.type" /></td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <template x-if="erHelpers.liveStatus(e) !== 'pago'">
                                        <button type="button" :title="$t('entryDetail.markPaid')"
                                            @click="$store.fin.markPaid(e.id); $store.ui.notifySuccess($t('entryDetail.markedPaid'))"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg text-success-600 hover:bg-success-50 dark:hover:bg-success-500/10">
                                            <x-icon name="check-circle" :size="16" />
                                        </button>
                                    </template>
                                    <button type="button" :title="$t('common.edit')" @click="openEdit(e)"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg text-muted hover:bg-gray-100 dark:hover:bg-gray-800">
                                        <x-icon name="edit" :size="16" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filtered.length === 0">
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-muted" x-text="$t('common.noResults')"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div x-show="filtered.length > 0" class="flex flex-col items-center justify-between gap-3 border-t border-themed px-5 py-3 sm:flex-row">
            <p class="text-xs text-muted"
                x-text="$t('common.showing', { from: from, to: to, total: filtered.length })"></p>
            <div class="flex items-center gap-1">
                <button type="button" @click="page = Math.max(1, page - 1)" :disabled="page <= 1"
                    class="focus-ring inline-flex h-9 items-center gap-1 rounded-lg border border-themed px-3 text-sm text-body disabled:opacity-40">
                    <x-icon name="chevron-left" :size="14" /> <span x-text="$t('common.previous')"></span>
                </button>
                <button type="button" @click="page = Math.min(pageCount, page + 1)" :disabled="page >= pageCount"
                    class="focus-ring inline-flex h-9 items-center gap-1 rounded-lg border border-themed px-3 text-sm text-body disabled:opacity-40">
                    <span x-text="$t('common.next')"></span> <x-icon name="chevron-right" :size="14" />
                </button>
            </div>
        </div>
    </div>

    <p class="text-xs text-muted" x-text="$t('table.lastUpdated', { date: $date(new Date().toISOString()) })"></p>

    {{-- ── Create / edit modal ───────────────────────────────────────────── --}}
    <div x-show="formOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/50" @click="formOpen = false"></div>
        <div x-show="formOpen" x-transition class="surface-card relative z-10 w-full max-w-2xl rounded-2xl border p-6 shadow-theme-xl">
            <div class="mb-5">
                <h3 class="text-lg font-semibold text-heading" x-text="editingId ? $t('entryForm.editTitle') : $t('entryForm.newTitle')"></h3>
                <p class="mt-0.5 text-sm text-muted" x-text="editingId ? $t('entryForm.editSubtitle') : $t('entryForm.newSubtitle')"></p>
            </div>
            <form @submit.prevent="submit()" class="space-y-4">
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-heading" x-text="$t('entryForm.description')"></label>
                    <input x-model="form.description" :placeholder="$t('entryForm.descriptionPh')"
                        class="focus-ring h-11 w-full rounded-lg border bg-transparent px-3.5 text-sm text-body" :class="errors.description ? 'border-error-500' : 'border-themed'" />
                    <p x-show="errors.description" x-cloak class="text-xs text-error-500" x-text="errors.description"></p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-heading" x-text="$t('entryForm.type')"></label>
                        <select x-model="form.type" :disabled="!!lockType" @change="form.categoryId=''; form.partyId=''"
                            class="focus-ring h-11 w-full rounded-lg border border-themed bg-transparent px-3 text-sm text-body disabled:opacity-60">
                            <option value="receita" x-text="$t('entryType.receita')"></option>
                            <option value="despesa" x-text="$t('entryType.despesa')"></option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-heading" x-text="$t('entryForm.amount')"></label>
                        <input type="number" step="0.01" x-model="form.amount" placeholder="0,00"
                            class="focus-ring h-11 w-full rounded-lg border bg-transparent px-3.5 text-sm text-body" :class="errors.amount ? 'border-error-500' : 'border-themed'" />
                        <p x-show="errors.amount" x-cloak class="text-xs text-error-500" x-text="errors.amount"></p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-heading" x-text="$t('entryForm.category')"></label>
                        <select x-model="form.categoryId"
                            class="focus-ring h-11 w-full rounded-lg border bg-transparent px-3 text-sm text-body" :class="errors.categoryId ? 'border-error-500' : 'border-themed'">
                            <option value="" x-text="$t('form.choose')"></option>
                            <template x-for="c in formCategories" :key="c.id">
                                <option :value="c.id" x-text="c.name"></option>
                            </template>
                        </select>
                        <p x-show="errors.categoryId" x-cloak class="text-xs text-error-500" x-text="errors.categoryId"></p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-heading" x-text="$t('entryForm.' + (form.type === 'receita' ? 'cliente' : 'fornecedor'))"></label>
                        <select x-model="form.partyId"
                            class="focus-ring h-11 w-full rounded-lg border bg-transparent px-3 text-sm text-body" :class="errors.partyId ? 'border-error-500' : 'border-themed'">
                            <option value="" x-text="$t('form.choose')"></option>
                            <template x-for="p in formParties" :key="p.id">
                                <option :value="p.id" x-text="p.name"></option>
                            </template>
                        </select>
                        <p x-show="errors.partyId" x-cloak class="text-xs text-error-500" x-text="errors.partyId"></p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-heading" x-text="$t('entryForm.dueDate')"></label>
                        <input type="date" x-model="form.dueDate"
                            class="focus-ring h-11 w-full rounded-lg border bg-transparent px-3.5 text-sm text-body" :class="errors.dueDate ? 'border-error-500' : 'border-themed'" />
                        <p x-show="errors.dueDate" x-cloak class="text-xs text-error-500" x-text="errors.dueDate"></p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-heading" x-text="$t('entryForm.account')"></label>
                        <select x-model="form.accountId"
                            class="focus-ring h-11 w-full rounded-lg border border-themed bg-transparent px-3 text-sm text-body">
                            <template x-for="a in $store.fin.accounts" :key="a.id">
                                <option :value="a.id" x-text="a.name + ' · ' + a.bank"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-heading" x-text="$t('entryForm.costCenter')"></label>
                        <select x-model="form.costCenter"
                            class="focus-ring h-11 w-full rounded-lg border border-themed bg-transparent px-3 text-sm text-body">
                            <template x-for="cc in costCenters" :key="cc">
                                <option :value="cc" x-text="$t('costCenter.' + cc)"></option>
                            </template>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-heading" x-text="$t('entryForm.invoice')"></label>
                        <input x-model="form.invoiceNumber" placeholder="000.000.000"
                            class="focus-ring h-11 w-full rounded-lg border border-themed bg-transparent px-3.5 text-sm text-body" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-themed pt-4">
                    <button type="button" @click="formOpen = false"
                        class="focus-ring rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body transition hover:bg-gray-50 dark:hover:bg-gray-800"
                        x-text="$t('common.cancel')"></button>
                    <button type="submit"
                        class="focus-ring rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-600"
                        x-text="editingId ? $t('common.save') : $t('common.create')"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Detail drawer ─────────────────────────────────────────────────── --}}
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
                                <span class="text-sm text-muted" x-text="$t('entryForm.type')"></span>
                                <span class="text-sm font-medium text-heading" x-text="$t('entryType.' + detail.type)"></span>
                            </div>
                            <div class="flex items-center justify-between gap-4 border-b border-themed py-3">
                                <span class="text-sm text-muted" x-text="$t('entryForm.category')"></span>
                                <span class="text-sm font-medium text-heading" x-text="erHelpers.categoryName(detail.categoryId)"></span>
                            </div>
                            <div class="flex items-center justify-between gap-4 border-b border-themed py-3">
                                <span class="text-sm text-muted" x-text="$t('entryForm.dueDate')"></span>
                                <span class="text-sm font-medium text-heading" x-text="$date(detail.dueDate)"></span>
                            </div>
                            <template x-if="detail.paidDate">
                                <div class="flex items-center justify-between gap-4 border-b border-themed py-3">
                                    <span class="text-sm text-muted" x-text="$t('entryDetail.paidOn')"></span>
                                    <span class="text-sm font-medium text-heading" x-text="$date(detail.paidDate)"></span>
                                </div>
                            </template>
                            <div class="flex items-center justify-between gap-4 border-b border-themed py-3">
                                <span class="text-sm text-muted" x-text="$t('entryForm.account')"></span>
                                <span class="text-sm font-medium text-heading" x-text="$store.fin.getAccount(detail.accountId)?.name"></span>
                            </div>
                            <div class="flex items-center justify-between gap-4 border-b border-themed py-3">
                                <span class="text-sm text-muted" x-text="$t('entryForm.costCenter')"></span>
                                <span class="text-sm font-medium text-heading" x-text="$t('costCenter.' + detail.costCenter)"></span>
                            </div>
                            <div class="flex items-center justify-between gap-4 border-b border-themed py-3">
                                <span class="text-sm text-muted" x-text="$t('entryForm.' + (detail.type === 'receita' ? 'cliente' : 'fornecedor'))"></span>
                                <span class="text-sm font-medium text-heading" x-text="$store.fin.partyName(detail.partyId)"></span>
                            </div>
                            <template x-if="detail.invoiceNumber">
                                <div class="flex items-center justify-between gap-4 border-b border-themed py-3">
                                    <span class="text-sm text-muted" x-text="$t('entryForm.invoice')"></span>
                                    <span class="text-sm font-medium text-heading" x-text="detail.invoiceNumber"></span>
                                </div>
                            </template>
                            <div class="flex items-center justify-between gap-4 py-3">
                                <span class="text-sm text-muted" x-text="$t('entryDetail.reconciled')"></span>
                                <span class="text-sm font-medium text-heading" x-text="detail.reconciled ? $t('common.yes') : $t('common.no')"></span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <template x-if="erHelpers.liveStatus(detail) !== 'pago'">
                                <button type="button" @click="$store.fin.markPaid(detail.id); $store.ui.notifySuccess($t('entryDetail.markedPaid')); detail = null"
                                    class="focus-ring inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-success-500 text-sm font-medium text-white transition hover:bg-success-600">
                                    <x-icon name="check-circle" :size="18" /> <span x-text="$t('entryDetail.markPaid')"></span>
                                </button>
                            </template>
                            <button type="button" @click="$store.fin.toggleReconciled(detail.id); detail = $store.fin.entries.find(x => x.id === detail.id)"
                                class="focus-ring inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-themed text-sm font-medium text-body transition hover:bg-gray-50 dark:hover:bg-gray-800">
                                <x-icon name="list-checks" :size="18" /> <span x-text="$t('entryDetail.toggleReconcile')"></span>
                            </button>
                            <button type="button" @click="openEdit(detail); detail = null"
                                class="focus-ring inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-themed text-sm font-medium text-body transition hover:bg-gray-50 dark:hover:bg-gray-800">
                                <x-icon name="edit" :size="18" /> <span x-text="$t('common.edit')"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('ledger', (lockType) => ({
                    lockType,
                    page: 1,
                    pageSize: 10,
                    formOpen: false,
                    editingId: null,
                    detail: null,
                    form: {},
                    errors: {},
                    costCenters: ['comercial', 'operacoes', 'administrativo', 'financeiro', 'marketing', 'ti'],

                    get scoped() {
                        const all = this.$store.fin.entries;
                        return this.lockType ? all.filter((e) => e.type === this.lockType) : all;
                    },
                    get filtered() {
                        return erHelpers.sortByDueDesc(erHelpers.filterEntries(this.scoped, this.$store.fin.filters));
                    },
                    get pageCount() {
                        return Math.max(1, Math.ceil(this.filtered.length / this.pageSize));
                    },
                    get pageRows() {
                        const p = Math.min(this.page, this.pageCount);
                        return this.filtered.slice((p - 1) * this.pageSize, (p - 1) * this.pageSize + this.pageSize);
                    },
                    get from() {
                        return this.filtered.length === 0 ? 0 : (Math.min(this.page, this.pageCount) - 1) * this.pageSize + 1;
                    },
                    get to() {
                        return Math.min(this.filtered.length, Math.min(this.page, this.pageCount) * this.pageSize);
                    },
                    get categoryList() {
                        return this.lockType ? this.$store.fin.categories.filter((c) => c.type === this.lockType) : this.$store.fin.categories;
                    },
                    get partyHeader() {
                        if (this.lockType === 'receita') return this.$t('entryForm.cliente');
                        if (this.lockType === 'despesa') return this.$t('entryForm.fornecedor');
                        return this.$t('table.party');
                    },
                    get formCategories() {
                        return this.$store.fin.categories.filter((c) => c.type === this.form.type);
                    },
                    get formParties() {
                        const kind = this.form.type === 'receita' ? 'cliente' : 'fornecedor';
                        return this.$store.fin.parties.filter((p) => p.kind === kind);
                    },

                    blankForm() {
                        const today = new Date().toISOString().slice(0, 10);
                        return {
                            description: '',
                            type: this.lockType || 'despesa',
                            categoryId: '',
                            amount: '',
                            dueDate: today,
                            accountId: this.$store.fin.accounts[0]?.id || '',
                            costCenter: 'administrativo',
                            partyId: '',
                            invoiceNumber: '',
                        };
                    },
                    openCreate() {
                        this.editingId = null;
                        this.errors = {};
                        this.form = this.blankForm();
                        this.formOpen = true;
                    },
                    openEdit(e) {
                        this.editingId = e.id;
                        this.errors = {};
                        this.form = {
                            description: e.description,
                            type: e.type,
                            categoryId: e.categoryId,
                            amount: e.amount,
                            dueDate: new Date(e.dueDate).toISOString().slice(0, 10),
                            accountId: e.accountId,
                            costCenter: e.costCenter,
                            partyId: e.partyId,
                            invoiceNumber: e.invoiceNumber || '',
                        };
                        this.formOpen = true;
                    },
                    openDetail(e) {
                        this.detail = e;
                    },
                    validate() {
                        const err = {};
                        if (!this.form.description || this.form.description.trim().length < 3) err.description = this.$t('form.min', { n: 3 });
                        if (!(Number(this.form.amount) > 0)) err.amount = this.$t('form.positive');
                        if (!this.form.categoryId) err.categoryId = this.$t('form.required');
                        if (!this.form.partyId) err.partyId = this.$t('form.required');
                        if (!this.form.dueDate) err.dueDate = this.$t('form.required');
                        this.errors = err;
                        return Object.keys(err).length === 0;
                    },
                    submit() {
                        if (!this.validate()) return;
                        const draft = {
                            description: this.form.description,
                            type: this.form.type,
                            categoryId: this.form.categoryId,
                            amount: Number(this.form.amount),
                            dueDate: this.form.dueDate,
                            accountId: this.form.accountId,
                            costCenter: this.form.costCenter,
                            partyId: this.form.partyId,
                            invoiceNumber: this.form.invoiceNumber || null,
                        };
                        if (this.editingId) {
                            this.$store.fin.updateEntry(this.editingId, {
                                ...draft,
                                dueDate: new Date(this.form.dueDate).toISOString(),
                            });
                            this.$store.ui.notifySuccess(this.$t('entryForm.updated'));
                        } else {
                            this.$store.fin.addEntry(draft);
                            this.$store.ui.notifySuccess(this.$t('entryForm.created'));
                        }
                        this.formOpen = false;
                    },
                }));
            });
        </script>
    @endpush
@endonce
