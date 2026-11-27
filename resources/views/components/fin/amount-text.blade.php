@props([
    'value' => '0',        {{-- Alpine expression: numeric amount (always positive) --}}
    'type' => "'despesa'", {{-- Alpine expression: 'receita' | 'despesa' --}}
    'signed' => true,      {{-- render with +/- sign and green/red color --}}
])
{{-- Money amount: green for receita (inflow), red for despesa (outflow).
     Mirrors erp/react components/fin/AmountText.tsx --}}
@if ($signed)
    <span {{ $attributes->merge(['class' => 'font-semibold tabular-nums']) }}
        :class="erHelpers.amountColor({{ $type }})"
        x-text="$signed({{ $value }}, {{ $type }})"></span>
@else
    <span {{ $attributes->merge(['class' => 'font-semibold tabular-nums text-heading']) }}
        x-text="$currency(Math.abs({{ $value }}))"></span>
@endif
