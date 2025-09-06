@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Detajet e Procesit - {{ $projekt->emri_projektit }}</h3>
                    <div>
                        <a href="{{ route('procesi.index', $projekt) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kthehu te Lista
                        </a>
                        <a href="{{ route('projektet.show', $projekt) }}" class="btn btn-primary">
                            <i class="fas fa-project-diagram"></i> Shko te Projekti
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="font-weight-bold mb-3">Informacione të Procesit</h4>
                            <dl class="row">
                                <dt class="col-sm-4">Data e Ndryshimit</dt>
                                <dd class="col-sm-8">{{ $proces->data_ndryshimit->format('d.m.Y H:i') }}</dd>

                                <dt class="col-sm-4">Statusi</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-{{ $proces->statusi_projektit->klasa_css ?? 'secondary' }}">
                                        {{ $proces->statusi_projektit->emri_statusit }}
                                    </span>
                                </dd>

                                <dt class="col-sm-4">Regjistruar nga</dt>
                                <dd class="col-sm-8">
                                    {{ $proces->perdoruesi->emri }} {{ $proces->perdoruesi->mbiemri }}
                                    <small class="text-muted">({{ $proces->perdoruesi->rol->emri_rolit }})</small>
                                </dd>
                            </dl>
                        </div>

                        <div class="col-md-6">
                            <h4 class="font-weight-bold mb-3">Komente</h4>
                            <div class="p-3 bg-light rounded">
                                {{ $proces->komente }}
                            </div>
                        </div>
                    </div>

                    @if($proces->dokumente->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4 class="font-weight-bold mb-3">Dokumentet e Bashkangjitura</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Emri i Dokumentit</th>
                                            <th>Lloji</th>
                                            <th>Madhësia</th>
                                            <th>Data e Ngarkimit</th>
                                            <th>Veprime</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($proces->dokumente as $dokument)
                                        <tr>
                                            <td>{{ $dokument->emri_skedarit }}</td>
                                            <td>{{ $dokument->lloji_skedarit }}</td>
                                            <td>{{ number_format($dokument->madhesia_skedarit / 1024, 2) }} KB</td>
                                            <td>{{ $dokument->data_ngarkimit->format('d.m.Y H:i') }}</td>
                                            <td>
                                                <a href="{{ Storage::url($dokument->rruga_skedarit) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   target="_blank">
                                                    <i class="fas fa-download"></i> Shkarko
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
