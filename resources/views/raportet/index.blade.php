@php
    $isAdmin = auth()->user() && auth()->user()->rol && auth()->user()->rol->emri_rolit === 'administrator';
@endphp

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Raportet & Statistikat</h1>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="eksportoDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-2"></i>Eksporto Të Gjitha
                    </button>
                    <ul class="dropdown-menu">
                        <li><h6 class="dropdown-header">Raporti i Projekteve</h6></li>
                        <li><a class="dropdown-item" href="#" onclick="eksporto('projektet', 'pdf')"><i class="fas fa-file-pdf text-danger me-2"></i>PDF</a></li>
                        <li><a class="dropdown-item" href="#" onclick="eksporto('projektet', 'excel')"><i class="fas fa-file-excel text-success me-2"></i>Excel</a></li>
                        <li><a class="dropdown-item" href="#" onclick="eksporto('projektet', 'image')"><i class="fas fa-image text-info me-2"></i>Foto</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Raporti i Materialeve</h6></li>
                        <li><a class="dropdown-item" href="#" onclick="eksporto('materialet', 'pdf')"><i class="fas fa-file-pdf text-danger me-2"></i>PDF</a></li>
                        <li><a class="dropdown-item" href="#" onclick="eksporto('materialet', 'excel')"><i class="fas fa-file-excel text-success me-2"></i>Excel</a></li>
                        <li><a class="dropdown-item" href="#" onclick="eksporto('materialet', 'image')"><i class="fas fa-image text-info me-2"></i>Foto</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Raporti i Stafit</h6></li>
                        <li><a class="dropdown-item" href="#" onclick="eksporto('stafi', 'pdf')"><i class="fas fa-file-pdf text-danger me-2"></i>PDF</a></li>
                        <li><a class="dropdown-item" href="#" onclick="eksporto('stafi', 'excel')"><i class="fas fa-file-excel text-success me-2"></i>Excel</a></li>
                        <li><a class="dropdown-item" href="#" onclick="eksporto('stafi', 'image')"><i class="fas fa-image text-info me-2"></i>Foto</a></li>
                        <li><hr class="dropdown-divider"></li>
                        @if(auth()->user() && auth()->user()->rol && auth()->user()->rol->emri_rolit === 'administrator')
                        <li><h6 class="dropdown-header">Raporti Financiar</h6></li>
                        <li><a class="dropdown-item" href="#" onclick="eksporto('financiar', 'pdf')"><i class="fas fa-file-pdf text-danger me-2"></i>PDF</a></li>
                        <li><a class="dropdown-item" href="#" onclick="eksporto('financiar', 'excel')"><i class="fas fa-file-excel text-success me-2"></i>Excel</a></li>
                        <li><a class="dropdown-item" href="#" onclick="eksporto('financiar', 'image')"><i class="fas fa-image text-info me-2"></i>Foto</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">File 3D</h6></li>
                        <li><a class="dropdown-item" href="#" onclick="eksporto('projektet', '3d')"><i class="fas fa-cube text-warning me-2"></i>Eksporto 3D Model</a></li>
                    </ul>
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
                                        Total Projekte</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProjekte }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-project-diagram fa-2x text-gray-300"></i>
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
                                        Projekte Aktive</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $projekteAktive }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-spinner fa-2x text-gray-300"></i>
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
                                        Projekte Përfunduar</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $projektePerfunduar }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                        Total Klientë</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalKliente }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafika dhe Raportet Detajuara -->
            <div class="row">
                <!-- Projekte sipas Statusit -->
                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Projekte sipas Statusit</h6>
                            <a href="{{ route('raportet.projektet') }}" class="btn btn-sm btn-primary">Shiko Detajet</a>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Klientët -->
                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Top 5 Klientët</h6>
                        </div>
                        <div class="card-body">
                            @foreach($topKliente as $klient)
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    <div class="icon-circle bg-primary">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small text-gray-500">{{ $klient->emri }} {{ $klient->mbiemri }}</div>
                                    <div class="font-weight-bold">{{ $klient->projektet_count }} projekte</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigimi i Shpejtë -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">
                            <div class="text-white-50 small">Raporti i Projekteve</div>
                            <div class="text-white-75">Shiko të gjitha projektet me filtra të detajuar</div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="{{ route('raportet.projektet') }}">Shiko Raportin</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body">
                            <div class="text-white-50 small">Raporti i Materialeve</div>
                            <div class="text-white-75">Analizo përdorimin e materialeve në projekte</div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="{{ route('raportet.materialet') }}">Shiko Raportin</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card bg-info text-white shadow">
                        <div class="card-body">
                            <div class="text-white-50 small">Performanca e Stafit</div>
                            <div class="text-white-75">Statistika për mjeshtrat dhe montuesit</div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="{{ route('raportet.stafi') }}">Shiko Raportin</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>

                @if(auth()->user() && auth()->user()->rol && auth()->user()->rol->emri_rolit === 'administrator')
                <div class="col-lg-6 mb-4">
                    <div class="card bg-warning text-white shadow">
                        <div class="card-body">
                            <div class="text-white-50 small">Raporti Financiar</div>
                            <div class="text-white-75">Analizë financiare e projekteve</div>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="{{ route('raportet.financiar') }}">Shiko Raportin</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart.js për grafikën e statuseve
const ctx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: [
            @foreach($projektePerStatus as $status)
                '{{ $status->status ? $status->status->emri_statusit : 'Pa Status' }}',
            @endforeach
        ],
        datasets: [{
            data: [
                @foreach($projektePerStatus as $status)
                    {{ $status->total }},
                @endforeach
            ],
            backgroundColor: [
                '#4e73df',
                '#1cc88a',
                '#36b9cc',
                '#f6c23e',
                '#e74a3b'
            ],
            hoverBackgroundColor: [
                '#2e59d9',
                '#17a673',
                '#2c9faf',
                '#f4b619',
                '#e02d1b'
            ],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
        },
        legend: {
            display: true,
            position: 'bottom'
        },
        cutoutPercentage: 80,
    },
});

// Funksioni për eksportimin e raporteve
function eksporto(lloji, formati) {
    // Kontrollo nëse përdoruesi përpiqet të shkarkojë raportin financiar pa të drejtë
    const isAdmin = @json($isAdmin);
    if (lloji === 'financiar' && !isAdmin) {
        Swal.fire({
            icon: 'error',
            title: 'E dënuar',
            text: 'Ju nuk keni të drejtë të shikoni raportin financiar.',
            confirmButtonText: 'Në rregull'
        });
        return false;
    }

    // Shfaq loading indicator
    const loadingToast = Swal.fire({
        title: 'Duke eksportuar...',
        text: `Po përgatitet raporti në format ${formati.toUpperCase()}`,
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    // Krijo form për POST request
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    
    // Përcakto URL-në bazuar në formatin
    if (formati === '3d') {
        form.action = '{{ route("eksporto.3d") }}';
    } else {
        form.action = `{{ url('/eksporto') }}/${formati}`;
    }
    
    // Shto CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // Shto llojin e raportit
    const llojiInput = document.createElement('input');
    llojiInput.type = 'hidden';
    llojiInput.name = 'lloji';
    llojiInput.value = lloji;
    form.appendChild(llojiInput);
    
    // Shto formatin
    const formatiInput = document.createElement('input');
    formatiInput.type = 'hidden';
    formatiInput.name = 'formati';
    formatiInput.value = formati;
    form.appendChild(formatiInput);
    
    // Shto në DOM dhe submit
    document.body.appendChild(form);
    form.submit();
    
    // Hiq formën pas submit
    setTimeout(() => {
        document.body.removeChild(form);
        loadingToast.close();
        
        // Shfaq mesazh suksesi
        Swal.fire({
            icon: 'success',
            title: 'Eksportimi u krye me sukses!',
            text: `Raporti "${lloji}" u eksportua në format ${formati.toUpperCase()}`,
            timer: 3000,
            showConfirmButton: false
        });
    }, 2000);
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
</style>
@endpush
@endsection
