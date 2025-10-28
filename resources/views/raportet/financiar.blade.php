@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Raporti Financiar</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('raportet.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kthehu
                    </a>
                    <button class="btn btn-primary" onclick="eksporto('financiar')">
                        <i class="fas fa-download me-2"></i>Eksporto PDF
                    </button>
                </div>
            </div>

            <!-- Filtrat -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Filtrat e Kërkimit</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('raportet.financiar') }}">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="data_nga" class="form-label">Data nga:</label>
                                <input type="date" class="form-control" id="data_nga" name="data_nga" 
                                       value="{{ request('data_nga') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="data_deri" class="form-label">Data deri:</label>
                                <input type="date" class="form-control" id="data_deri" name="data_deri" 
                                       value="{{ request('data_deri') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="klient_id" class="form-label">Klienti:</label>
                                <select class="form-control" id="klient_id" name="klient_id">
                                    <option value="">Të gjithë</option>
                                    @foreach($klientet as $klient)
                                        <option value="{{ $klient->klient_id }}" 
                                                {{ request('klient_id') == $klient->klient_id ? 'selected' : '' }}>
                                            {{ $klient->emri_klientit }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="status_id" class="form-label">Statusi:</label>
                                <select class="form-control" id="status_id" name="status_id">
                                    <option value="">Të gjitha</option>
                                    @foreach($statuset as $status)
                                        <option value="{{ $status->status_id }}" 
                                                {{ request('status_id') == $status->status_id ? 'selected' : '' }}>
                                            {{ $status->emri_statusit }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-2"></i>Filtro
                                </button>
                                <a href="{{ route('raportet.financiar') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Pastro Filtrat
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistika Financiare -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Të Ardhura Totale</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($totaliTeArdhura, 2) }} €
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Shpenzimet për Materiale</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($totaliShpenzime, 2) }} €
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Fitimi Bruto</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($totaliTeArdhura - $totaliShpenzime, 2) }} €
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Projekte të Faturuara</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $projekteTeFaturuara }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela e Projekteve Financiare -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Detajet Financiare të Projekteve ({{ $projektet->count() }} projekte)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Projekti</th>
                                    <th>Klienti</th>
                                    <th>Çmimi i Kontratës</th>
                                    <th>Kosto e Materialeve</th>
                                    <th>Fitimi</th>
                                    <th>Marzhi (%)</th>
                                    <th>Statusi</th>
                                    <th>Data e Krijimit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projektet as $projekt)
                                <tr>
                                    <td>
                                        <strong>{{ $projekt->emri_projektit }}</strong>
                                        @if($projekt->pershkrimi)
                                            <br><small class="text-muted">{{ Str::limit($projekt->pershkrimi, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $projekt->klient->emri_klientit ?? 'N/A' }}</td>
                                    <td>
                                        @if($projekt->cmimi_total)
                                            <strong class="text-success">{{ number_format($projekt->cmimi_total, 2) }} €</strong>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $kostaMaterialesh = 0;
                                            if ($projekt->projektMateriale) {
                                                $kostaMaterialesh = $projekt->projektMateriale->sum(function($pm) {
                                                    return $pm->sasia * ($pm->material->cmimi_per_njesi ?? 0);
                                                });
                                            }
                                        @endphp
                                        <span class="text-danger">{{ number_format($kostaMaterialesh, 2) }} €</span>
                                    </td>
                                    <td>
                                        @php
                                            $fitimi = ($projekt->cmimi_total ?? 0) - $kostaMaterialesh;
                                        @endphp
                                        <span class="text-{{ $fitimi >= 0 ? 'success' : 'danger' }}">
                                            {{ number_format($fitimi, 2) }} €
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $marzhi = $projekt->cmimi_total > 0 ? ($fitimi / $projekt->cmimi_total) * 100 : 0;
                                        @endphp
                                        <span class="badge badge-{{ $marzhi >= 30 ? 'success' : ($marzhi >= 15 ? 'warning' : 'danger') }}">
                                            {{ number_format($marzhi, 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $projekt->statusi_projektit->ngjyra ?? 'secondary' }}">
                                            {{ $projekt->statusi_projektit->emri_statusit ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $projekt->data_krijimit ? $projekt->data_krijimit->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Nuk ka projekte që përputhen me kriteret e kërkimit.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Grafika Financiare -->
            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Trendi i të Ardhurave Mujore</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Shpërndarja e të Ardhurave</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="pieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analiza e Klientëve -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 Klientët sipas të Ardhurave</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Klienti</th>
                                    <th>Numri i Projekteve</th>
                                    <th>Të Ardhurat Totale</th>
                                    <th>Projekti më i Madh</th>
                                    <th>Vlera Mesatare</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topKlientet as $klient)
                                <tr>
                                    <td>
                                        <strong>{{ $klient->emri_klientit }}</strong>
                                        @if($klient->telefon)
                                            <br><small class="text-muted">{{ $klient->telefon }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $klient->projektet_count }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">{{ number_format($klient->total_te_ardhurat, 2) }} €</strong>
                                    </td>
                                    <td>
                                        @if($klient->projekti_me_i_madh)
                                            <span class="text-info">{{ number_format($klient->projekti_me_i_madh->buxheti ?? 0, 2) }} €</span>
                                            <div class="small text-muted">{{ $klient->projekti_me_i_madh->emri_projektit ?? '' }}</div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-warning">{{ number_format($klient->vlera_mesatare, 2) }} €</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart.js për trendin e të ardhurave
const ctx1 = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx1, {
    type: 'line',
    data: {
        labels: ['Jan', 'Shk', 'Mar', 'Pri', 'Maj', 'Qer', 'Kor', 'Gus', 'Sht', 'Tet', 'Nën', 'Dhj'],
        datasets: [{
            label: 'Të Ardhurat (€)',
            data: [
                @php
                    // Simuloj të dhëna mujore (mund të zëvendësohet me të dhëna reale)
                    $muajt = [];
                    for($i = 1; $i <= 12; $i++) {
                        $muajt[] = $projektet->filter(function($p) use ($i) {
                            return $p->data_krijimit && $p->data_krijimit->month == $i;
                        })->sum('cmimi_total');
                    }
                    echo implode(',', $muajt);
                @endphp
            ],
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.3
        }]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value + ' €';
                    }
                }
            }
        }
    }
});

// Chart.js për shpërndarjen e të ardhurave
const ctx2 = document.getElementById('pieChart').getContext('2d');
const pieChart = new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: ['Të Ardhurat', 'Shpenzimet'],
        datasets: [{
            data: [{{ $totaliTeArdhura }}, {{ $totaliShpenzime }}],
            backgroundColor: ['#1cc88a', '#e74a3b'],
            hoverBackgroundColor: ['#17a673', '#c0392b'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

function eksporto(tipi) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("raportet.eksporto") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    const tipiInput = document.createElement('input');
    tipiInput.type = 'hidden';
    tipiInput.name = 'tipi';
    tipiInput.value = tipi;
    
    // Shtoj filtrat aktualë
    const params = new URLSearchParams(window.location.search);
    params.forEach((value, key) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    });
    
    form.appendChild(csrfToken);
    form.appendChild(tipiInput);
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
@endpush

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.badge-primary {
    background-color: #4e73df;
}

.badge-success {
    background-color: #1cc88a;
}

.badge-warning {
    background-color: #f6c23e;
}

.badge-danger {
    background-color: #e74a3b;
}

.chart-area {
    position: relative;
    height: 300px;
}

.chart-pie {
    position: relative;
    height: 300px;
}
</style>
@endpush
@endsection
