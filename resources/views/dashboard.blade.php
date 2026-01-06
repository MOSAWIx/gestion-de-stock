@extends('layouts.app')

@section('content')
<style>
.kpi-card {
    background: linear-gradient(135deg,#ffffff,#f8f9fa);
    border-radius: 18px;
    padding: 26px;
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
    border: none;
}
.kpi-icon {
    width: 58px;
    height: 58px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 26px;
}
.bg-blue { background: linear-gradient(135deg,#4facfe,#00f2fe); }
.bg-green { background: linear-gradient(135deg,#43e97b,#38f9d7); }
.bg-orange { background: linear-gradient(135deg,#fa709a,#fee140); }

.kpi-label { font-size: 14px; color: #6c757d; }
.kpi-value { font-size: 30px; font-weight: 700; }

.table-scroll {
    max-height: 260px;
    overflow-y: auto;
}
.table-scroll thead th {
    position: sticky;
    top: 0;
    background: #212529;
    color: white;
}
</style>

<div class="container-fluid">
    <h2 class="mb-4 fw-bold">DASHBOARD</h2>

    {{-- KPIs --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="kpi-card d-flex justify-content-between">
                <div>
                    <div class="kpi-label">Total Produits</div>
                    <div class="kpi-value">{{ $produits_count }}</div>
                </div>
                <div class="kpi-icon bg-blue"><i class="bi bi-box-seam"></i></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="kpi-card d-flex justify-content-between">
                <div>
                    <div class="kpi-label">Quantité Totale</div>
                    <div class="kpi-value">{{ $quantite_total }}</div>
                </div>
                <div class="kpi-icon bg-green"><i class="bi bi-bar-chart"></i></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="kpi-card d-flex justify-content-between">
                <div>
                    <div class="kpi-label">Valeur du Stock</div>
                    <div class="kpi-value">{{ number_format($valeur_stock,2) }} DH</div>
                </div>
                <div class="kpi-icon bg-orange"><i class="bi bi-currency-dollar"></i></div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-4 mb-4">

        {{-- Ventes --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header d-flex justify-content-between align-items-center fw-bold">
                    <span id="chartTitle">{{ $title }}</span>

                    <div class="d-flex gap-2">
                        {{-- Filter période --}}
                        <select id="filterChart" class="form-select form-select-sm w-auto">
                            <option value="days">jours</option>
                            <option value="weeks">Semaines</option>
                            <option value="months">Mois</option>
                        </select>

                        {{-- ✅ Y AXIS SCALE (NOUVEAU) --}}
                        <select id="yAxisScale" class="form-select form-select-sm w-auto">
                            <option value="auto">Auto</option>
                            <option value="10000">10 000</option>
                            <option value="30000">30 000</option>
                            <option value="70000">70 000</option>
                            <option value="100000">100 000</option>
                            <option value="150000">150 000</option>
                        </select>
                    </div>
                </div>

                <div class="card-body">
                    <div id="chartVentes"></div>
                </div>
            </div>
        </div>

        {{-- Stock --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header fw-bold">Répartition Stock</div>
                <div class="card-body">
                    <div id="chartStock"></div>
                </div>
            </div>
        </div>

    </div>

    {{-- Low stock --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-danger text-white fw-bold">
            Produits Faible en Stock
        </div>
        <div class="card-body p-0 table-scroll">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Qté</th>
                        <th>Stock min</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStockProduits as $p)
                        <tr>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->quantite }}</td>
                            <td>{{ $p->stock_min }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted p-3">
                                Aucun produit en stock faible
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
let chartVentes, chartStock;

document.addEventListener('DOMContentLoaded', function () {

    // ===== CHART VENTES =====
    chartVentes = new ApexCharts(document.querySelector("#chartVentes"), {
        chart: { type: 'bar', height: 300 },
        series: [{ name: 'Ventes (DH)', data: @json($data) }],
        xaxis: { categories: @json($labels) },
        yaxis: {
            min: 0,
            forceNiceScale: true
        }
    });
    chartVentes.render();

    // ===== CHART STOCK =====
    chartStock = new ApexCharts(document.querySelector("#chartStock"), {
        chart: { type: 'donut', height: 300 },
        labels: ['En stock', 'Faible', 'Rupture'],
        series: [{{ $countEnStock }}, {{ $countFaible }}, {{ $countRupture }}],
        colors: ['#28a745','#ffc107','#dc3545'],
        legend: { position: 'bottom' }
    });
    chartStock.render();

    // ===== FILTER PERIODE =====
    document.getElementById('filterChart').addEventListener('change', function () {
        fetch(`{{ route('dashboard.ventes.data') }}?filter=` + this.value)
            .then(r => r.json())
            .then(res => {
                chartVentes.updateOptions({
                    xaxis: { categories: res.labels }
                });
                chartVentes.updateSeries([{ data: res.data }]);
                document.getElementById('chartTitle').innerText = res.title;
                setTimeout(() => chartStock.resize(), 100);
            });
    });

    // ===== ✅ Y AXIS SCALE =====
    document.getElementById('yAxisScale').addEventListener('change', function () {
        if (this.value === 'auto') {
            chartVentes.updateOptions({
                yaxis: { max: undefined }
            });
        } else {
            chartVentes.updateOptions({
                yaxis: { max: parseInt(this.value) }
            });
        }
    });
});
</script>
@endsection
