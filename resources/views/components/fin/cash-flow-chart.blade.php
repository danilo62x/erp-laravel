@props([
    'seriesExpr' => '[]', {{-- Alpine expression resolving to a CashFlowPoint[] --}}
    'height' => 300,
])
{{-- Projected cash-flow area chart: inflow, outflow and running balance.
     Mirrors erp/react components/fin/CashFlowChart.tsx. ApexCharts via CDN. --}}
<div x-data="{
        chart: null,
        get points() { return {{ $seriesExpr }}; },
        build() {
            const base = window.erChart.base();
            const data = this.points;
            const options = {
                ...base,
                chart: { ...base.chart, type: 'area', stacked: false, height: {{ $height }} },
                colors: ['#12b76a', '#f04438', '#465fff'],
                fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.02, shadeIntensity: 1, stops: [0, 90] } },
                stroke: { curve: 'smooth', width: [2, 2, 3] },
                series: [
                    { name: $t('cashflow.inflow'), data: data.map((d) => Math.round(d.inflow)) },
                    { name: $t('cashflow.outflow'), data: data.map((d) => Math.round(d.outflow)) },
                    { name: $t('cashflow.balance'), data: data.map((d) => Math.round(d.balance)) },
                ],
                xaxis: { categories: data.map((d) => erHelpers.formatMonth(d.month)) },
                yaxis: { labels: { formatter: (v) => erHelpers.formatCompactCurrency(v) } },
                tooltip: { theme: base.tooltip.theme, y: { formatter: (v) => erHelpers.formatCurrency(v) } },
                legend: { position: 'top', horizontalAlign: 'right' },
            };
            if (this.chart) this.chart.destroy();
            this.chart = new ApexCharts(this.$refs.chart, options);
            this.chart.render();
        },
    }"
    x-init="build(); $watch('$store.ui.darkMode', () => build()); $watch('$store.ui.lang', () => build())">
    <div x-ref="chart"></div>
</div>
