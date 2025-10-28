@extends('layouts.app')

@section('title', 'Raporti i Materialeve')

@section('content')
<style>
.card-body, .card-body *, .card-body div, .card-body span, .card-body p, .card-body td, .card-body th {
    color: #000 !important;
}
.text-muted {
    color: #333 !important;
}
label.text-muted {
    color: #666 !important;
    font-weight: bold !important;
}
.table th {
    color: #fff !important;
    background-color: #343a40 !important;
}
.badge {
    color: #fff !important;
}
</style>
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0"><i class="fas fa-clipboard-list"></i> Raporti i Materialeve</h4>
          <a href="{{ route('projektet-dimensions.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Kthehu</a>
        </div>
        <div class="card-body">
          <form method="GET" class="form-inline mb-3">
            <div class="form-group mr-2">
              <label for="projekt_id" class="mr-2">Projekti:</label>
              <select name="projekt_id" id="projekt_id" class="form-control">
                <option value="">Të gjitha projektet</option>
                @foreach($projektet as $p)
                  <option value="{{ $p->projekt_id }}" {{ request('projekt_id') == $p->projekt_id ? 'selected' : '' }}>{{ $p->emri_projektit }}</option>
                @endforeach
              </select>
            </div>
            <button class="btn btn-primary"><i class="fas fa-search"></i> Filtro</button>
          </form>

          @if(empty($report) || count($report) === 0)
            <div class="alert alert-info mb-0">Nuk ka të dhëna për raport.</div>
          @else
            <!-- Summary Section -->
            <div class="row mb-4">
              <div class="col-md-3">
                <div class="card bg-primary text-white">
                  <div class="card-body text-center">
                    <h5 class="card-title">{{ count($report) }}</h5>
                    <p class="card-text">Materiale të ndryshme</p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-success text-white">
                  <div class="card-body text-center">
                    <h5 class="card-title">{{ collect($report)->sum(function($item) { return $item['dimensions']->count(); }) }}</h5>
                    <p class="card-text">Pjesë totale</p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-warning text-white">
                  <div class="card-body text-center">
                    <h5 class="card-title">{{ collect($report)->sum('total_volume') }}</h5>
                    <p class="card-text">Vëllim total</p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-info text-white">
                  <div class="card-body text-center">
                    <h5 class="card-title">{{ $projektet->count() }}</h5>
                    <p class="card-text">Projekte aktive</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-striped table-hover">
                <thead class="thead-dark">
                  <tr>
                    <th>Materiali</th>
                    <th>Sasia Totale</th>
                    <th>Njësia</th>
                    <th>Nr. Pjesëve</th>
                    <th>Projekte Aktive</th>
                    <th>Projekte të Përfunduara</th>
                    <th>Rezervuar</th>
                    <th>Disponueshëm</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($report as $item)
                    @php
                      $firstDim = $item['dimensions']->first();
                      $unit = $firstDim && $firstDim->materiali ? $firstDim->materiali->njesia_matese : '-';
                      $materialId = $firstDim && $firstDim->materiali ? $firstDim->materiali->material_id : null;

                      // Calculate project counts
                      $activeProjects = $item['dimensions']->where('projekt.status_id', '!=', 4)->count(); // Assuming 4 is completed
                      $completedProjects = $item['dimensions']->where('projekt.status_id', 4)->count();

                      // Calculate reserved and available
                      $reserved = $item['total_volume'];
                      $available = $firstDim && $firstDim->materiali ? ($firstDim->materiali->sasia_stokut - $reserved) : 0;
                    @endphp
                    <tr>
                      <td><strong>{{ $item['material'] }}</strong></td>
                      <td>{{ number_format($item['total_volume'], 3) }}</td>
                      <td>{{ $unit }}</td>
                      <td>{{ $item['dimensions']->count() }}</td>
                      <td><span class="badge badge-warning">{{ $activeProjects }}</span></td>
                      <td><span class="badge badge-success">{{ $completedProjects }}</span></td>
                      <td><span class="badge badge-info">{{ number_format($reserved, 3) }} {{ $unit }}</span></td>
                      <td><span class="badge {{ $available >= 0 ? 'badge-success' : 'badge-danger' }}">{{ number_format($available, 3) }} {{ $unit }}</span></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <hr>
            <h5 class="mt-4">Detaje</h5>
            @foreach($report as $item)
              @php
                $firstDim = $item['dimensions']->first();
                $unit = $firstDim && $firstDim->materiali ? $firstDim->materiali->njesia_matese : '-';
              @endphp
              <div class="card mb-3">
                <div class="card-header d-flex justify-content-between">
                  <div>
                    <strong>{{ $item['material'] }}</strong>
                    <span class="badge badge-info ml-2">Totali: {{ number_format($item['total_volume'], 3) }} {{ $unit }}</span>
                  </div>
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table mb-0">
                      <thead>
                        <tr>
                          <th>Projekti</th>
                          <th>Statusi Projekti</th>
                          <th>Pjesa</th>
                          <th>Dimensionet</th>
                          <th>Sasia</th>
                          <th>Nevojitura</th>
                          <th>Workstation</th>
                          <th>Statusi Prodhimit</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($item['dimensions'] as $dim)
                          <tr>
                            <td>{{ $dim->projekt->emri_projektit ?? 'N/A' }}</td>
                            <td>
                              @php
                                $statusClass = 'badge-secondary';
                                if ($dim->projekt->status_id == 1) $statusClass = 'badge-warning';
                                elseif ($dim->projekt->status_id == 2) $statusClass = 'badge-info';
                                elseif ($dim->projekt->status_id == 3) $statusClass = 'badge-primary';
                                elseif ($dim->projekt->status_id == 4) $statusClass = 'badge-success';
                              @endphp
                              <span class="badge {{ $statusClass }}">{{ $dim->projekt->statusi_projektit->emri_statusit ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $dim->emri_pjeses }}</td>
                            <td>{{ $dim->gjatesia }} × {{ $dim->gjeresia }} × {{ $dim->trashesia }}{{ $dim->njesi_matese }}</td>
                            <td>{{ $dim->sasia }}</td>
                            <td>{{ number_format($dim->sasiaMaterialitNevojitur(), 3) }} {{ $unit }}</td>
                            <td>{{ $dim->workstation_current ?? '-' }}</td>
                            <td>
                              @php
                                $prodStatusClass = 'badge-secondary';
                                if ($dim->statusi_prodhimit == 'Në prodhim') $prodStatusClass = 'badge-warning';
                                elseif ($dim->statusi_prodhimit == 'Përfunduar') $prodStatusClass = 'badge-success';
                                elseif ($dim->statusi_prodhimit == 'Anuluar') $prodStatusClass = 'badge-danger';
                              @endphp
                              <span class="badge {{ $prodStatusClass }}">{{ $dim->statusi_prodhimit }}</span>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            @endforeach
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
