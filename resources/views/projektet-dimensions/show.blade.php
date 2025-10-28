@extends('layouts.app')

@section('title', 'Detajet e Dimensionit')

@section('content')
<style>
.card-body, .card-body *, .card-body div, .card-body span, .card-body p {
    color: #000 !important;
}
.text-muted {
    color: #333 !important;
}
label.text-muted {
    color: #666 !important;
    font-weight: bold !important;
}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-eye"></i> Detajet e Dimensionit</h4>
                    <div>
                        <a href="{{ route('projektet-dimensions.edit', $dimension) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edito</a>
                        <a href="{{ route('projektet-dimensions.print-ticket', $dimension) }}" target="_blank" class="btn btn-success"><i class="fas fa-print"></i> Print Ticket</a>
                        <a href="{{ route('projektet-dimensions.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kthehu</a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted">Projekti</label>
                                <div class="h5">{{ $dimension->projekt->emri_projektit ?? 'N/A' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted">Emri i pjesës</label>
                                <div class="h5">{{ $dimension->emri_pjeses }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted">Dimensionet</label>
                                <div class="h5">{{ $dimension->gjatesia }} × {{ $dimension->gjeresia }} × {{ $dimension->trashesia }}{{ $dimension->njesi_matese }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted">Sasia</label>
                                <div class="h5">{{ $dimension->sasia }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted">Barcode</label>
                                <div class="h5">{{ $dimension->barcode ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted">Materiali</label>
                                <div class="h5">
                                    @if($dimension->materiali)
                                        {{ $dimension->materiali->emri_materialit }} ({{ $dimension->materiali->njesia_matese }})
                                    @else
                                        {{ $dimension->materiali_personal ?? 'N/A' }}
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted">Kantimi</label>
                                <div>
                                    @if($dimension->kantim_needed)
                                        <span class="badge badge-primary">{{ $dimension->anetEKantimit() ?: 'Specifiko anët' }}</span>
                                        @if($dimension->kantim_type)
                                            <span class="badge badge-info">{{ $dimension->kantim_type }}</span>
                                        @endif
                                        @if($dimension->kantim_thickness)
                                            <span class="badge badge-secondary">{{ $dimension->kantim_thickness }} mm</span>
                                        @endif
                                        <span class="badge badge-light">Qoshet: {{ $dimension->kantim_corners }}</span>
                                    @else
                                        <span class="badge badge-secondary">Pa kantim</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted">Statusi i prodhimit</label>
                                <div>
                                    <span class="badge badge-{{ $dimension->statusi_prodhimit === 'completed' ? 'success' : ($dimension->statusi_prodhimit === 'pending' ? 'warning' : 'info') }}">
                                        {{ $dimension->statusi_prodhimit }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted">Workstation</label>
                                <div class="h6">{{ $dimension->workstation_current ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">Përshkrimi</label>
                        <div class="border rounded p-3">{{ $dimension->pershkrimi ?? '—' }}</div>
                    </div>

                    <div class="row text-muted">
                        <div class="col-md-4">Krijuar: {{ $dimension->created_at->format('d/m/Y H:i') }}</div>
                        <div class="col-md-4">Përditësuar: {{ $dimension->updated_at->format('d/m/Y H:i') }}</div>
                        <div class="col-md-4">Krijuesi: {{ $dimension->krijues->emri ?? 'N/A' }} {{ $dimension->krijues->mbiemri ?? '' }}</div>
                    </div>

                    <hr>
                    <form action="{{ route('projektet-dimensions.destroy', $dimension) }}" method="POST" onsubmit="return confirm('Fshij këtë dimension?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="fas fa-trash"></i> Fshi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
