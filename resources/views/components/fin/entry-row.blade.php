@props([
    'entry' => 'entry', {{-- Alpine expression resolving to an entry object --}}
    'onClick' => null,  {{-- Alpine statement to run on click (optional) --}}
])
{{-- Compact entry list row used on the dashboard (recent / due soon).
     Mirrors erp/react components/fin/EntryRow.tsx --}}
<button type="button"
    @if ($onClick) @click="{{ $onClick }}" @endif
    class="flex w-full items-center gap-3 rounded-xl px-2 py-2.5 text-left transition hover:bg-gray-50 dark:hover:bg-white/5"
    x-data="{ e: {{ $entry }} }">
    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
        :class="e.type === 'receita' ? 'bg-success-50 text-success-600 dark:bg-success-500/15' : 'bg-error-50 text-error-600 dark:bg-error-500/15'">
        <template x-if="e.type === 'receita'"><x-icon name="arrow-down-left" :size="18" :stroke-width="2.2" /></template>
        <template x-if="e.type !== 'receita'"><x-icon name="arrow-up-right" :size="18" :stroke-width="2.2" /></template>
    </span>
    <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-medium text-heading" x-text="e.description"></p>
        <p class="truncate text-xs text-muted" x-text="($store.fin.partyName(e.partyId)) + ' · ' + erHelpers.categoryName(e.categoryId)"></p>
    </div>
    <div class="flex flex-col items-end gap-1">
        <x-fin.amount-text value="e.amount" type="e.type" />
        <x-fin.status-pill status="erHelpers.liveStatus(e)" />
    </div>
</button>
