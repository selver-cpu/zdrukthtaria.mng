@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Raporti i Materialeve</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('raportet.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kthehu
                    </a>
                    <button class="btn btn-primary" onclick="eksporto('materialet')">
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
                    <form method="GET" action="{{ route('raportet.materialet') }}">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="data_nga" class="form-label">Data nga:</label>
                                <input type="date" class="form-control" id="data_nga" name="data_nga" 
                                       value="{{ request('data_nga') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="data_deri" class="form-label">Data deri:</label>
                                <input type="date" class="form-control" id="data_deri" name="data_deri" 
                                       value="{{ request('data_deri') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="material_id" class="form-label">Materiali:</label>
                                <select class="form-control" id="material_id" name="material_id">
                                    <option value="">Të gjitha</option>
                                    @foreach($materialet as $material)
                                        <option value="{{ $material->material_id }}" 
                                                {{ request('material_id') == $material->material_id ? 'selected' : '' }}>
                                            {{ $material->emri_materialit }}
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
                                <a href="{{ route('raportet.materialet') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Pastro Filtrat
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabela e Materialeve -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Përdorimi i Materialeve ({{ $materialetPerdorur->count() }} materiale)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Materiali</th>
                                    <th>Njësia Matëse</th>
                                    <th>Sasia e Përdorur</th>
                                    <th>Çmimi për Njësi</th>
                                    <th>Vlera Totale</th>
                                    <th>Numri i Projekteve</th>
                                    <th>Veprime</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($materialetPerdorur as $materialPerdorur)
                                <tr>
                                    <td>
                                        <strong>{{ $materialPerdorur->material->emri_materialit ?? 'N/A' }}</strong>
                                        @if($materialPerdorur->material->pershkrimi)
                                            <br><small class="text-muted">{{ Str::limit($materialPerdorur->material->pershkrimi, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $materialPerdorur->material->njesia_matese ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-primary">
                                            {{ number_format($materialPerdorur->total_perdorur, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($materialPerdorur->material->cmimi_per_njesi)
                                            {{ number_format($materialPerdorur->material->cmimi_per_njesi, 2) }} €
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($materialPerdorur->material->cmimi_per_njesi)
                                            <strong>{{ number_format($materialPerdorur->total_perdorur * $materialPerdorur->material->cmimi_per_njesi, 2) }} €</strong>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $materialPerdorur->material->projektMateriale->count() }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="shfaqDetajet({{ $materialPerdorur->material_id }})">
                                            <i class="fas fa-eye"></i> Detajet
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nuk ka materiale që përputhen me kriteret e kërkimit.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Statistika të Materialeve -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Top 10 Materialet më të Përdorura</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="topMaterialetChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Vlera Totale e Materialeve</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $totalVlera = $materialetPerdorur->sum(function($item) {
                                    return $item->total_perdorur * ($item->material->cmimi_per_njesi ?? 0);
                                });
                            @endphp
                            <div class="text-center">
                                <div class="h2 mb-0 font-weight-bold text-primary">{{ number_format($totalVlera, 2) }} €</div>
                                <div class="text-muted">Vlera totale e materialeve të përdorura</div>
                            </div>
                            
                            <hr>
                            
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="h5 mb-0 font-weight-bold text-success">
                                        {{ $materialetPerdorur->count() }}
                                    </div>
                                    <div class="text-muted small">Lloje Materialesh</div>
                                </div>
                                <div class="col-6">
                                    <div class="h5 mb-0 font-weight-bold text-info">
                                        {{ $materialetPerdorur->sum('total_perdorur') }}
                                    </div>
                                    <div class="text-muted small">Sasia Totale</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal për Detajet e Materialit -->
<div class="modal fade" id="materialDetajetModal" tabindex="-1" aria-labelledby="materialDetajetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="materialDetajetModalLabel">Detajet e Materialit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="materialDetajetContent">
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
// Chart.js për top materialet
const ctx = document.getElementById('topMaterialetChart').getContext('2d');
const topMaterialetChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [
            @foreach($materialetPerdorur->take(10) as $material)
                '{{ Str::limit($material->material->emri_materialit ?? "N/A", 20) }}',
            @endforeach
        ],
        datasets: [{
            label: 'Sasia e Përdorur',
            data: [
                @foreach($materialetPerdorur->take(10) as $material)
                    {{ $material->total_perdorur }},
                @endforeach
            ],
            backgroundColor: '#4e73df',
            borderColor: '#4e73df',
            borderWidth: 1
        }]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                display: false
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

function shfaqDetajet(materialId) {
    const modal = new bootstrap.Modal(document.getElementById('materialDetajetModal'));
    const content = document.getElementById('materialDetajetContent');
    
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
                <h6>Detajet e materialit do të shfaqen këtu</h6>
                <p>Ky funksionalitet mund të implementohet për të shfaqur:</p>
                <ul>
                    <li>Lista e projekteve ku është përdorur</li>
                    <li>Historiku i përdorimit</li>
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
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.badge-primary {
    background-color: #4e73df;
}

.badge-info {
    background-color: #36b9cc;
}

.chart-area {
    position: relative;
    height: 300px;
}
</style>
@endpush
@endsection
