@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Historiku i Procesit - {{ $projekt->emri_projektit }}</h3>
                    <a href="{{ route('projektet.show', $projekt) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kthehu te Projekti
                    </a>
                </div>

                <div class="card-body">
                    <!-- Forma për shtimin e një procesi të ri -->
                    @can('update', $projekt)
                    <form action="{{ route('procesi.store', $projekt) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status_id">Statusi i Ri</label>
                                    <select name="status_id" id="status_id" class="form-control @error('status_id') is-invalid @enderror" required>
                                        <option value="">Zgjidhni statusin...</option>
                                        @foreach(\App\Models\StatusetProjektit::orderBy('renditja')->get() as $status)
                                            <option value="{{ $status->status_id }}" {{ old('status_id') == $status->status_id ? 'selected' : '' }}>
                                                {{ $status->emri_statusit }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="komente">Komente</label>
                                    <textarea name="komente" id="komente" rows="2" 
                                        class="form-control @error('komente') is-invalid @enderror" 
                                        required>{{ old('komente') }}</textarea>
                                    @error('komente')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-plus"></i> Shto Proces
                                </button>
                            </div>
                        </div>
                    </form>
                    @endcan

                    <!-- Timeline i proceseve -->
                    <div class="timeline mt-4">
                        @forelse($proceset as $proces)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $proces->statusi_projektit->klasa_css ?? 'secondary' }}"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between">
                                        <h4 class="timeline-title mb-0">
                                            {{ $proces->statusi_projektit->emri_statusit }}
                                        </h4>
                                        <small class="text-muted">
                                            {{ $proces->data_ndryshimit->format('d.m.Y H:i') }}
                                        </small>
                                    </div>
                                    <p class="mb-0">{{ $proces->komente }}</p>
                                    <small class="text-muted">
                                        Regjistruar nga: {{ $proces->perdoruesi->emri }} {{ $proces->perdoruesi->mbiemri }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">Nuk ka procese të regjistruara për këtë projekt.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding: 20px 0;
    }
    
    .timeline-item {
        display: flex;
        margin-bottom: 20px;
    }
    
    .timeline-marker {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        margin-right: 15px;
        margin-top: 5px;
    }
    
    .timeline-content {
        flex: 1;
        background: #f8f9fa;
        padding: 15px;
        border-radius: 4px;
    }
    
    .timeline-title {
        font-size: 1.1rem;
        font-weight: 600;
    }
</style>
@endpush
@endsection
