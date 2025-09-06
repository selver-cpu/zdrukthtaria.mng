@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Raporti i Projekteve</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('raportet.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kthehu
                    </a>
                    <button class="btn btn-primary" onclick="eksporto('projektet')">
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
                    <form method="GET" action="{{ route('raportet.projektet') }}">
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
                            <div class="col-md-3 mb-3">
                                <label for="klient_id" class="form-label">Klienti:</label>
                                <select class="form-control" id="klient_id" name="klient_id">
                                    <option value="">Të gjithë</option>
                                    @foreach($klientet as $klient)
                                        <option value="{{ $klient->klient_id }}" 
                                                {{ request('klient_id') == $klient->klient_id ? 'selected' : '' }}>
                                            {{ $klient->emri }} {{ $klient->mbiemri }}
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
                                <a href="{{ route('raportet.projektet') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Pastro Filtrat
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabela e Projekteve -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Lista e Projekteve ({{ $projektet->total() }} rezultate)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Emri Projektit</th>
                                    <th>Klienti</th>
                                    <th>Statusi</th>
                                    <th>Mjeshtri</th>
                                    <th>Montuesi</th>
                                    <th>Data Krijimit</th>
                                    <th>Data Fillimit</th>
                                    <th>Data Përfundimit</th>
                                    <th>Veprime</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projektet as $projekt)
                                <tr>
                                    <td>{{ $projekt->projekt_id }}</td>
                                    <td>
                                        <strong>{{ $projekt->emri_projektit }}</strong>
                                        @if($projekt->pershkrimi)
                                            <br><small class="text-muted">{{ Str::limit($projekt->pershkrimi, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $projekt->klient->emri ?? 'N/A' }} {{ $projekt->klient->mbiemri ?? '' }}
                                        @if($projekt->klient->kompania)
                                            <br><small class="text-muted">{{ $projekt->klient->kompania }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $projekt->statusi_projektit->emri_statusit == 'Përfunduar' ? 'success' : 
                                                                   ($projekt->statusi_projektit->emri_statusit == 'Në progres' ? 'primary' : 
                                                                   ($projekt->statusi_projektit->emri_statusit == 'Në pauzë' ? 'warning' : 'secondary')) }}">
                                            {{ $projekt->statusi_projektit->emri_statusit ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($projekt->mjeshtriCaktuar)
                                            {{ $projekt->mjeshtriCaktuar->emri }} {{ $projekt->mjeshtriCaktuar->mbiemri }}
                                        @else
                                            <span class="text-muted">Nuk është caktuar</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($projekt->montuesicaktuar)
                                            {{ $projekt->montuesicaktuar->emri }} {{ $projekt->montuesicaktuar->mbiemri }}
                                        @else
                                            <span class="text-muted">Nuk është caktuar</span>
                                        @endif
                                    </td>
                                    <td>{{ $projekt->data_krijimit ? $projekt->data_krijimit->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $projekt->data_fillimit ? $projekt->data_fillimit->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $projekt->data_perfundimit_planifikuar ? $projekt->data_perfundimit_planifikuar->format('d/m/Y') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('projektet.show', $projekt) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Nuk ka projekte që përputhen me kriteret e kërkimit.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($projektet->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $projektet->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistika të Shpejta -->
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total në Kërkesë</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $projektet->total() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Të Përfunduara</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $projektet->where('status.emri_statusit', 'Përfunduar')->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Në Progres</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $projektet->where('status.emri_statusit', 'Në progres')->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-spinner fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Në Pauzë</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $projektet->where('status.emri_statusit', 'Në pauzë')->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-pause fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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

.badge-secondary {
    background-color: #858796;
}
</style>
@endpush
@endsection
