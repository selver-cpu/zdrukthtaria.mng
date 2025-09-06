@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Raporti i Performancës së Stafit</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('raportet.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kthehu
                    </a>
                    <button class="btn btn-primary" onclick="eksporto('stafi')">
                        <i class="fas fa-download me-2"></i>Eksporto PDF
                    </button>
                </div>
            </div>

            <!-- Statistika të Përgjithshme -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Mjeshtrat</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $mjeshtrat->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-tools fa-2x text-gray-300"></i>
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
                                        Total Montuesit</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $montuesit->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-hammer fa-2x text-gray-300"></i>
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
                                        Projekte Aktive</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $mjeshtrat->sum('projekte_si_mjesher_count') + $montuesit->sum('projekte_si_montues_count') }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-project-diagram fa-2x text-gray-300"></i>
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
                                        Projekte Përfunduar</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $mjeshtrat->sum('projekte_perfunduar') + $montuesit->sum('projekte_perfunduar') }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performanca e Mjeshtrave -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Performanca e Mjeshtrave</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Emri</th>
                                    <th>Email</th>
                                    <th>Telefon</th>
                                    <th>Total Projekte</th>
                                    <th>Projekte Përfunduar</th>
                                    <th>Shkalla e Suksesit</th>
                                    <th>Status</th>
                                    <th>Veprime</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mjeshtrat as $mjeshtri)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <div class="icon-circle bg-primary">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <strong>{{ $mjeshtri->emri }} {{ $mjeshtri->mbiemri }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $mjeshtri->email }}</td>
                                    <td>{{ $mjeshtri->telefon ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-primary">
                                            {{ $mjeshtri->projekte_si_mjesher_count }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">
                                            {{ $mjeshtri->projekte_perfunduar }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $suksesi = $mjeshtri->projekte_si_mjesher_count > 0 ? 
                                                      round(($mjeshtri->projekte_perfunduar / $mjeshtri->projekte_si_mjesher_count) * 100, 1) : 0;
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $suksesi >= 80 ? 'success' : ($suksesi >= 60 ? 'warning' : 'danger') }}" 
                                                 role="progressbar" style="width: {{ $suksesi }}%" 
                                                 aria-valuenow="{{ $suksesi }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $suksesi }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $mjeshtri->aktiv ? 'success' : 'danger' }}">
                                            {{ $mjeshtri->aktiv ? 'Aktiv' : 'Joaktiv' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="shfaqDetajetStafi({{ $mjeshtri->perdorues_id }}, 'mjeshtër')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Nuk ka mjeshtër të regjistruar.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Performanca e Montuesve -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Performanca e Montuesve</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Emri</th>
                                    <th>Email</th>
                                    <th>Telefon</th>
                                    <th>Total Projekte</th>
                                    <th>Projekte Përfunduar</th>
                                    <th>Shkalla e Suksesit</th>
                                    <th>Status</th>
                                    <th>Veprime</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($montuesit as $montuesi)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <div class="icon-circle bg-success">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <strong>{{ $montuesi->emri }} {{ $montuesi->mbiemri }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $montuesi->email }}</td>
                                    <td>{{ $montuesi->telefon ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-primary">
                                            {{ $montuesi->projekte_si_montues_count }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">
                                            {{ $montuesi->projekte_perfunduar }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $suksesi = $montuesi->projekte_si_montues_count > 0 ? 
                                                      round(($montuesi->projekte_perfunduar / $montuesi->projekte_si_montues_count) * 100, 1) : 0;
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $suksesi >= 80 ? 'success' : ($suksesi >= 60 ? 'warning' : 'danger') }}" 
                                                 role="progressbar" style="width: {{ $suksesi }}%" 
                                                 aria-valuenow="{{ $suksesi }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $suksesi }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $montuesi->aktiv ? 'success' : 'danger' }}">
                                            {{ $montuesi->aktiv ? 'Aktiv' : 'Joaktiv' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="shfaqDetajetStafi({{ $montuesi->perdorues_id }}, 'montues')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Nuk ka montues të regjistruar.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Grafika e Performancës -->
            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Krahasimi i Performancës</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="performanceChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Top Performuesit</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $topPerformues = collect($mjeshtrat)->merge($montuesit)
                                    ->map(function($person) {
                                        $total = $person->projekte_si_mjesher_count ?? $person->projekte_si_montues_count;
                                        $perfunduar = $person->projekte_perfunduar;
                                        $suksesi = $total > 0 ? ($perfunduar / $total) * 100 : 0;
                                        return [
                                            'emri' => $person->emri . ' ' . $person->mbiemri,
                                            'suksesi' => $suksesi,
                                            'total' => $total,
                                            'perfunduar' => $perfunduar,
                                            'roli' => isset($person->projekte_si_mjesher_count) ? 'Mjeshtër' : 'Montues'
                                        ];
                                    })
                                    ->sortByDesc('suksesi')
                                    ->take(5);
                            @endphp
                            
                            @foreach($topPerformues as $performues)
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    <div class="icon-circle bg-{{ $performues['roli'] == 'Mjeshtër' ? 'primary' : 'success' }}">
                                        <i class="fas fa-{{ $performues['roli'] == 'Mjeshtër' ? 'tools' : 'hammer' }} text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small text-gray-500">{{ $performues['emri'] }} ({{ $performues['roli'] }})</div>
                                    <div class="font-weight-bold">{{ round($performues['suksesi'], 1) }}% sukses</div>
                                    <div class="small text-muted">{{ $performues['perfunduar'] }}/{{ $performues['total'] }} projekte</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal për Detajet e Stafit -->
<div class="modal fade" id="stafiDetajetModal" tabindex="-1" aria-labelledby="stafiDetajetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stafiDetajetModalLabel">Detajet e Anëtarit të Stafit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="stafiDetajetContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Duke ngarkuar...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart.js për krahasimin e performancës
const ctx = document.getElementById('performanceChart').getContext('2d');
const performanceChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [
            @foreach($mjeshtrat->take(10) as $mjeshtri)
                '{{ $mjeshtri->emri }} {{ $mjeshtri->mbiemri }}',
            @endforeach
            @foreach($montuesit->take(10) as $montuesi)
                '{{ $montuesi->emri }} {{ $montuesi->mbiemri }}',
            @endforeach
        ],
        datasets: [{
            label: 'Projekte Totale',
            data: [
                @foreach($mjeshtrat->take(10) as $mjeshtri)
                    {{ $mjeshtri->projekte_si_mjesher_count }},
                @endforeach
                @foreach($montuesit->take(10) as $montuesi)
                    {{ $montuesi->projekte_si_montues_count }},
                @endforeach
            ],
            backgroundColor: '#4e73df',
            borderColor: '#4e73df',
            borderWidth: 1
        }, {
            label: 'Projekte Përfunduar',
            data: [
                @foreach($mjeshtrat->take(10) as $mjeshtri)
                    {{ $mjeshtri->projekte_perfunduar }},
                @endforeach
                @foreach($montuesit->take(10) as $montuesi)
                    {{ $montuesi->projekte_perfunduar }},
                @endforeach
            ],
            backgroundColor: '#1cc88a',
            borderColor: '#1cc88a',
            borderWidth: 1
        }]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
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
    
    form.appendChild(csrfToken);
    form.appendChild(tipiInput);
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function shfaqDetajetStafi(perdoruesId, roli) {
    const modal = new bootstrap.Modal(document.getElementById('stafiDetajetModal'));
    const content = document.getElementById('stafiDetajetContent');
    
    // Shfaq loading
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Duke ngarkuar...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Simuloj ngarkimin e të dhënave (mund të zëvendësohet me AJAX call)
    setTimeout(() => {
        content.innerHTML = `
            <div class="alert alert-info">
                <h6>Detajet e anëtarit të stafit (ID: ${perdoruesId}, Roli: ${roli})</h6>
                <p>Ky funksionalitet mund të implementohet për të shfaqur:</p>
                <ul>
                    <li>Lista e projekteve të caktuara</li>
                    <li>Historiku i performancës</li>
                    <li>Vlerësimet dhe komentet</li>
                    <li>Statistika të detajuara</li>
                </ul>
            </div>
        `;
    }, 1000);
}
</script>
@endpush

@push('styles')
<style>
.icon-circle {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

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

.text-gray-500 {
    color: #858796 !important;
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

.badge-danger {
    background-color: #e74a3b;
}

.chart-area {
    position: relative;
    height: 300px;
}
</style>
@endpush
@endsection
