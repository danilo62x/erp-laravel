@props([
    'title' => '',     {{-- Alpine expression (e.g. $t('...')) --}}
    'value' => "''",   {{-- Alpine expression resolving to a formatted string --}}
    'icon' => 'wallet',
    'accent' => 'brand', {{-- brand | success | warning | error | info --}}
    'hint' => null,    {{-- Alpine expression, optional --}}
])
@php($bg = [
    'brand' => 'bg-brand-50 dark:bg-brand-500/15',
    'success' => 'bg-success-50 dark:bg-success-500/15',
    'warning' => 'bg-warning-50 dark:bg-warning-500/15',
    'error' => 'bg-error-50 dark:bg-error-500/15',
    'info' => 'bg-blue-light-50 dark:bg-blue-light-500/15',
][$accent] ?? 'bg-brand-50 dark:bg-brand-500/15')
@php($fg = [
    'brand' => 'text-brand-500',
    'success' => 'text-success-600',
    'warning' => 'text-warning-600',
    'error' => 'text-error-500',
    'info' => 'text-blue-light-500',
][$accent] ?? 'text-brand-500')
{{-- Mirrors erp/react components/fin/KpiCard.tsx --}}
<div class="surface-card rounded-2xl border p-5 shadow-theme-sm">
    <div class="flex items-center justify-between">
        <div class="flex h-11 w-11 items-center justify-center rounded-xl {{ $bg }}">
            <x-icon name="{{ $icon }}" :size="20" class="{{ $fg }}" />
        </div>
    </div>
    <p class="mt-4 text-sm text-muted" x-text="{{ $title }}"></p>
    <h4 class="mt-1 text-2xl font-bold tabular-nums text-heading" x-text="{{ $value }}"></h4>
    @if ($hint)
        <p class="mt-1 text-xs text-muted" x-text="{{ $hint }}"></p>
    @endif
</div>
