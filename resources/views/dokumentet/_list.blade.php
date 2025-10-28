<div class="bg-white shadow rounded-lg p-4 mb-4">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Dokumentet e Projektit</h3>
    
    @if($projekt->dokumentet->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($projekt->dokumentet as $dokument)
                <div class="border rounded-lg p-3 bg-gray-50 hover:bg-gray-100 transition">
                    <div class="flex items-center mb-2">
                        @php
                            $extension = pathinfo($dokument->emri_skedarit, PATHINFO_EXTENSION);
                            $icon = 'document';
                            
                            if (in_array($extension, ['pdf'])) {
                                $icon = 'document-text';
                            } elseif (in_array($extension, ['doc', 'docx'])) {
                                $icon = 'document-text';
                            } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                $icon = 'document-report';
                            } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                $icon = 'photograph';
                            } elseif (in_array($extension, ['zip', 'rar'])) {
                                $icon = 'archive';
                            } elseif (in_array($extension, ['stl', 'step', 'skp', 'dwg'])) {
                                $icon = 'cube';
                            }
                        @endphp
                        
                        <div class="mr-3 flex-shrink-0">
                            @if($icon == 'document-text')
                                <svg class="h-8 w-8 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            @elseif($icon == 'document-report')
                                <svg class="h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            @elseif($icon == 'photograph')
                                <svg class="h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            @elseif($icon == 'archive')
                                <svg class="h-8 w-8 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                            @elseif($icon == 'cube')
                                <svg class="h-8 w-8 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                </svg>
                            @else
                                <svg class="h-8 w-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            @endif
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $dokument->emri_skedarit }}
                            </p>
                            <p class="text-xs text-gray-500 truncate">
                                {{ number_format($dokument->madhesia_skedarit / 1024, 2) }} KB • {{ strtoupper($extension) }}
                            </p>
                        </div>
                    </div>
                    
                    @if($dokument->pershkrimi)
                        <p class="text-xs text-gray-600 mb-2">{{ $dokument->pershkrimi }}</p>
                    @endif
                    
                    <div class="flex justify-between items-center mt-2">
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
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('projektet.dokumentet.download', $dokument->dokument_id) }}" class="text-indigo-600 hover:text-indigo-900" title="Shkarko">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                            
                            <a href="{{ route('projektet.dokumentet.show', $dokument->dokument_id) }}" class="text-blue-600 hover:text-blue-900" title="Shiko">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            
                            @if(auth()->id() == $dokument->perdorues_id_ngarkues || auth()->user()->hasRole(['administrator', 'menaxher']))
                                <form action="{{ route('projektet.dokumentet.destroy', $dokument->dokument_id) }}" method="POST" onsubmit="return confirm('A jeni të sigurt që dëshironi të fshini këtë dokument?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Fshi">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-4">
            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nuk ka dokumente</h3>
            <p class="mt-1 text-sm text-gray-500">Filloni duke ngarkuar një dokument të ri për këtë projekt.</p>
        </div>
    @endif
</div>
