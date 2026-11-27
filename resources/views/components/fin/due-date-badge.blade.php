@props([
    'date' => '',  {{-- Alpine expression: ISO due date --}}
    'paid' => 'false', {{-- Alpine expression: already settled? --}}
])
{{-- Due date with overdue / due-soon highlighting.
     Mirrors erp/react components/fin/DueDateBadge.tsx --}}
<span class="inline-flex items-center gap-1.5 text-sm tabular-nums"
    x-data="{ get days() { return erHelpers.daysUntil({{ $date }}); },
              get overdue() { return !({{ $paid }}) && this.days < 0; },
              get soon() { return !({{ $paid }}) && this.days >= 0 && this.days <= 7; } }"
    :class="overdue ? 'text-error-600 dark:text-error-500' : (soon ? 'text-warning-600 dark:text-warning-500' : 'text-body')">
    <x-icon name="calendar" :size="14" />
    <span x-text="$date({{ $date }})"></span>
    <span x-show="overdue" x-cloak class="text-xs font-medium" x-text="'(' + $t('due.overdueBy', { days: Math.abs(days) }) + ')'"></span>
    <span x-show="soon" x-cloak class="text-xs font-medium" x-text="'(' + $t('due.inDays', { days: days }) + ')'"></span>
</span>
