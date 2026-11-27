@props([
    'seriesExpr' => '[]', {{-- Alpine expression resolving to a CategorySlice[] --}}
    'height' => 300,
])
{{-- Expenses-by-category donut. Mirrors erp/react components/fin/CategoryDonut.tsx. --}}
<div x-data="{
        chart: null,
        palette: ['#465fff', '#f04438', '#f79009', '#12b76a', '#0ba5ec', '#7592ff', '#fdb022', '#9cb9ff'],
        get slices() { return {{ $seriesExpr }}; },
        build() {
            const base = window.erChart.base();
            const data = this.slices;
            const options = {
                chart: { ...base.chart, type: 'donut', height: {{ $height }} },
                labels: data.map((d) => d.name),
                series: data.map((d) => Math.round(d.amount)),
                colors: this.palette,
                legend: { position: 'bottom', labels: base.legend.labels },
                stroke: { width: 0 },
                dataLabels: { enabled: false },
                tooltip: { theme: base.tooltip.theme, y: { formatter: (v) => erHelpers.formatCurrency(v) } },
                plotOptions: { pie: { donut: { size: '68%', labels: { show: true, total: { show: true,
                    formatter: (w) => erHelpers.formatCurrency(w.globals.seriesTotals.reduce((a, b) => a + b, 0)) } } } } },
            };
            if (this.chart) this.chart.destroy();
            this.chart = new ApexCharts(this.$refs.chart, options);
            this.chart.render();
        },
    }"
    x-init="build(); $watch('$store.ui.darkMode', () => build()); $watch('$store.ui.lang', () => build())">
    <div x-ref="chart"></div>
</div>
