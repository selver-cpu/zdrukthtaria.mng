@extends('layouts.app')

@section('title', 'Dimensionet e Projekteve')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-ruler-combined"></i> Dimensionet e Projekteve
                        </h4>
                        <div class="d-flex gap-2">
                            <!-- Export Excel -->
                            <form action="{{ route('eksporto.excel') }}" method="POST" class="mr-2">
                                @csrf
                                <input type="hidden" name="lloji" value="dimensionet">
                                <input type="hidden" name="projekt_id" value="{{ request('projekt_id') }}">
                                <input type="hidden" name="statusi" value="{{ request('statusi') }}">
                                <input type="hidden" name="materiali_id" value="{{ request('materiali_id') }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Excel
                                </button>
                            </form>
                            
                            <!-- Export XML për OSI 2007 -->
                            @if(auth()->check() && in_array(auth()->user()->rol_id, [1, 2, 5]))
                            <a href="{{ route('projektet-dimensions.export-xml') }}?{{ http_build_query(request()->all()) }}" 
                               class="btn btn-warning mr-2" 
                               title="Eksport XML për makinën OSI 2007">
                                <i class="fas fa-code"></i> OSI 2007 XML
                            </a>
                            @endif
                            
                            <!-- Cutlist Optimizer - Vetëm për Admin, Menaxher dhe Disajnere -->
                            @if(auth()->check() && in_array(auth()->user()->rol_id, [1, 2, 5]))
                            <a href="{{ route('cutlist-optimizer.index') }}" 
                               class="btn btn-success mr-2" 
                               title="Optimizo Prerjen e Pjesëve">
                                <i class="fas fa-cut"></i> Cutlist Optimizer
                            </a>
                            @endif
                            
                            <!-- Ticket Layout Editor - Vetëm për Admin dhe Menaxher -->
                            @if(auth()->check() && in_array(auth()->user()->rol_id, [1, 2]))
                            <a href="{{ route('ticket-layout.index') }}" 
                               class="btn btn-info mr-2" 
                               title="Edito Layout-in e Tiketave PLC">
                                <i class="fas fa-edit"></i> Ticket Layout
                            </a>
                            @endif
                            
                            <a href="{{ route('projektet-dimensions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Shto Dimension të Ri
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtri -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" class="form-inline">
                                <div class="form-group mr-3">
                                    <label for="projekt_id" class="mr-2">Projekti:</label>
                                    <select name="projekt_id" id="projekt_id" class="form-control">
                                        <option value="">Të gjitha projektet</option>
                                        @foreach($projektet as $projekt)
                                            <option value="{{ $projekt->projekt_id }}" {{ request('projekt_id') == $projekt->projekt_id ? 'selected' : '' }}>
                                                {{ $projekt->emri_projektit }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mr-3">
                                    <label for="statusi" class="mr-2">Statusi:</label>
                                    <select name="statusi" id="statusi" class="form-control">
                                        <option value="">Të gjitha statuset</option>
                                        <option value="pending" {{ request('statusi') == 'pending' ? 'selected' : '' }}>Në pritje</option>
                                        <option value="cutting" {{ request('statusi') == 'cutting' ? 'selected' : '' }}>Duke prerë</option>
                                        <option value="edge_banding" {{ request('statusi') == 'edge_banding' ? 'selected' : '' }}>Duke kantuar</option>
                                        <option value="completed" {{ request('statusi') == 'completed' ? 'selected' : '' }}>Përfunduar</option>
                                    </select>
                                </div>

                                <div class="form-group mr-3">
                                    <label for="materiali_id" class="mr-2">Materiali:</label>
                                    <select name="materiali_id" id="materiali_id" class="form-control">
                                        <option value="">Të gjithë materialet</option>
                                        @foreach($materialet as $material)
                                            <option value="{{ $material->material_id }}" {{ request('materiali_id') == $material->material_id ? 'selected' : '' }}>
                                                {{ $material->emri_materialit }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mr-3">
                                    <label for="search" class="mr-2">Kërko:</label>
                                    <input type="text" name="search" id="search" class="form-control" placeholder="Emri i pjesës, barcode, projekti..." value="{{ request('search') }}">
                                </div>

                                <button type="submit" class="btn btn-secondary mr-2">
                                    <i class="fas fa-search"></i> Filtro
                                </button>

                                <a href="{{ route('projektet-dimensions.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Pastro Filtrat
                                </a>
                            </form>
                        </div>
                    </div>

                    <!-- Tabela e dimensioneve -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Projekti</th>
                                    <th>Pjesa</th>
                                    <th>Dimensionet</th>
                                    <th>Materiali</th>
                                    <th>Kantimi</th>
                                    <th>Sasia</th>
                                    <th>Statusi</th>
                                    <th>Krijuar nga</th>
                                    <th>Data</th>
                                    <th>Veprimet</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dimensions as $dimension)
                                    <tr>
                                        <td>
                                            <strong>{{ $dimension->projekt->emri_projektit ?? 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $dimension->emri_pjeses }}</span>
                                            @if($dimension->barcode)
                                                <br><small class="text-muted">Barcode: {{ $dimension->barcode }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $dimension->gjatesia }} × {{ $dimension->gjeresia }} × {{ $dimension->trashesia }}{{ $dimension->njesi_matese }}</strong>
                                        </td>
                                        <td>
                                            @if($dimension->materiali)
                                                <span class="badge badge-success">{{ $dimension->materiali->emri_materialit }}</span>
                                            @else
                                                <span class="badge badge-warning">{{ $dimension->materiali_personal }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($dimension->kantim_needed)
                                                <span class="badge badge-primary">
                                                    {{ $dimension->anetEKantimit() }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">Pa kantim</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $dimension->sasia }}</strong>
                                        </td>
                                        <td>
                                            @switch($dimension->statusi_prodhimit)
                                                @case('pending')
                                                    <span class="badge badge-warning">Në pritje</span>
                                                    @break
                                                @case('cutting')
                                                    <span class="badge badge-info">Duke prerë</span>
                                                    @break
                                                @case('edge_banding')
                                                    <span class="badge badge-primary">Duke kantuar</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge badge-success">Përfunduar</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ $dimension->statusi_prodhimit }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            {{ $dimension->krijues->emri ?? 'N/A' }} {{ $dimension->krijues->mbiemri ?? '' }}
                                        </td>
                                        <td>
                                            {{ $dimension->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('projektet-dimensions.show', $dimension) }}" class="btn btn-sm btn-outline-info" title="Shiko">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('projektet-dimensions.edit', $dimension) }}" class="btn btn-sm btn-outline-warning" title="Edito">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('projektet-dimensions.print-ticket', $dimension) }}" class="btn btn-sm btn-outline-success" title="Print PLC Ticket" target="_blank">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <form method="POST" action="{{ route('projektet-dimensions.destroy', $dimension) }}" style="display: inline;" onsubmit="return confirm('Jeni i sigurt që doni ta fshini këtë dimension?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Fshi">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <i class="fas fa-ruler-combined fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Nuk ka dimensione të gjetura</h5>
                                            <p class="text-muted">Krijoni dimensionin e parë për të filluar!</p>
                                            <a href="{{ route('projektet-dimensions.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Shto Dimension të Ri
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginimi -->
                    @if($dimensions->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $dimensions->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistikat e shpejta -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h4>{{ $dimensions->total() }}</h4>
                <p>Total Dimensionet</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4>{{ $dimensions->where('statusi_prodhimit', 'pending')->count() }}</h4>
                <p>Në Pritje</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h4>{{ $dimensions->where('statusi_prodhimit', 'cutting')->count() + $dimensions->where('statusi_prodhimit', 'edge_banding')->count() }}</h4>
                <p>Në Prodhim</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4>{{ $dimensions->where('statusi_prodhimit', 'completed')->count() }}</h4>
                <p>Përfunduar</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form when filters change
    $('#projekt_id, #statusi, #materiali_id').change(function() {
        $(this).closest('form').submit();
    });

    // Real-time search
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            $('#projekt_id').closest('form').submit();
        }, 500);
    });
});
</script>
@endpush
