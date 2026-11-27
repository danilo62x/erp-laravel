@props([
    'status' => '', {{-- Alpine expression resolving to 'pago' | 'pendente' | 'vencido' --}}
])
{{-- Payment status pill (pago / pendente / vencido).
     Mirrors erp/react components/fin/StatusPill.tsx --}}
<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
    :class="{
        'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500': erHelpers.statusPill({{ $status }}).color === 'success',
        'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-500': erHelpers.statusPill({{ $status }}).color === 'warning',
        'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500': erHelpers.statusPill({{ $status }}).color === 'error',
    }">
    <template x-if="erHelpers.statusPill({{ $status }}).icon === 'check'"><x-icon name="check" :size="12" /></template>
    <template x-if="erHelpers.statusPill({{ $status }}).icon === 'clock'"><x-icon name="clock" :size="12" /></template>
    <template x-if="erHelpers.statusPill({{ $status }}).icon === 'alert-triangle'"><x-icon name="alert-triangle" :size="12" /></template>
    <span x-text="$t('status.' + ({{ $status }}))"></span>
</span>
