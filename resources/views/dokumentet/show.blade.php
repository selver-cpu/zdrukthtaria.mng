@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-semibold text-gray-900">Detaje Dokumenti</h1>
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kthehu
            </a>
        </div>

        <div class="bg-white shadow overflow-hidden rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $dokument->emri_skedarit }}</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Ngarkuar më {{ $dokument->created_at->format('d.m.Y H:i') }}
                    </p>
                </div>
                <a href="{{ route('projektet.dokumentet.download', $dokument->dokument_id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Shkarko
                </a>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Emri i skedarit</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $dokument->emri_skedarit }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Lloji i skedarit</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $dokument->lloji_skedarit }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Madhësia</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ number_format($dokument->madhesia_skedarit / 1024, 2) }} KB</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Kategoria</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($dokument->kategoria == 'vizatim') bg-blue-100 text-blue-800
                                @elseif($dokument->kategoria == 'dimension') bg-green-100 text-green-800
                                @elseif($dokument->kategoria == 'material') bg-yellow-100 text-yellow-800
                                @elseif($dokument->kategoria == '3d_model') bg-purple-100 text-purple-800
                                @elseif($dokument->kategoria == 'excel') bg-green-100 text-green-800
                                @elseif($dokument->kategoria == 'foto') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($dokument->kategoria) }}
                            </span>
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Përshkrimi</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $dokument->pershkrimi ?? 'Pa përshkrim' }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Ngarkuar nga</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($dokument->ngarkuesi)
                                {{ $dokument->ngarkuesi->emri }} {{ $dokument->ngarkuesi->mbiemri }}
                            @else
                                Përdorues i panjohur
                            @endif
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Data e ngarkimit</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $dokument->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
            
            @if(auth()->user()->perdorues_id == $dokument->perdorues_id_ngarkues || auth()->user()->hasRole(['administrator', 'menaxher']))
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 border-t border-gray-200">
                    <form action="{{ route('projektet.dokumentet.destroy', $dokument->dokument_id) }}" method="POST" class="inline-block" onsubmit="return confirm('A jeni të sigurt që dëshironi të fshini këtë dokument?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Fshi Dokumentin
                        </button>
                    </form>
                </div>
            @endif
        </div>
        
        @if(in_array(pathinfo($dokument->emri_skedarit, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
            <div class="mt-6 bg-white shadow overflow-hidden rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Parapamja</h3>
                    <img src="{{ Storage::url($dokument->rruga_skedarit) }}" alt="{{ $dokument->emri_skedarit }}" class="max-w-full h-auto rounded">
                </div>
            </div>
        @elseif(in_array(pathinfo($dokument->emri_skedarit, PATHINFO_EXTENSION), ['pdf']))
            <div class="mt-6 bg-white shadow overflow-hidden rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Parapamja</h3>
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe src="{{ Storage::url($dokument->rruga_skedarit) }}" class="w-full h-screen" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
