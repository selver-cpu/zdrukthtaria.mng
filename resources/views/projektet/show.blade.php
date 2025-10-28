<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detajet e Projektit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Project Details Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Informacione të Përgjithshme</h3>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('projektet-dimensions.index') . '?projekt_id=' . $projekt->projekt_id }}" class="btn btn-outline-info">
                                <i class="fas fa-ruler-combined me-2"></i> Dimensionet
                            </a>
                            <a href="{{ route('projektet-dimensions.create') . '?projekt_id=' . $projekt->projekt_id }}" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i> Shto Dimension
                            </a>
                            <a href="{{ route('projektet-dimensions.material-report') . '?projekt_id=' . $projekt->projekt_id }}" class="btn btn-outline-secondary">
                                <i class="fas fa-clipboard-list me-2"></i> Raport Materiale
                            </a>
                            <a href="{{ route('projektet.email-form', $projekt->projekt_id) }}" class="btn btn-primary">
                                <i class="fas fa-envelope me-2"></i> Dërgo Email
                            </a>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p><strong>Emri i Projektit:</strong> {{ $projekt->emri_projektit }}</p>
                            <p><strong>Klienti:</strong> {{ $projekt->klient->emri_klientit ?? 'N/A' }}</p>
                            <p><strong>Data e Fillimit:</strong> {{ $projekt->data_fillimit_parashikuar ? $projekt->data_fillimit_parashikuar->format('d.m.Y') : 'N/A' }}</p>
                            <p><strong>Data e Përfundimit:</strong> {{ $projekt->data_perfundimit_parashikuar ? $projekt->data_perfundimit_parashikuar->format('d.m.Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <p><strong>Statusi:</strong> {{ $projekt->statusi_projektit->emri_statusit ?? 'N/A' }}</p>
                            <p><strong>Mjeshtri:</strong> {{ $projekt->mjeshtri ? $projekt->mjeshtri->emri . ' ' . $projekt->mjeshtri->mbiemri : 'N/A' }}</p>
                            <p><strong>Montuesi:</strong> {{ $projekt->montuesi ? $projekt->montuesi->emri . ' ' . $projekt->montuesi->mbiemri : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Phases Section -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Fazat e Projektit</h3>
                        @if(auth()->check() && in_array(auth()->user()->rol_id, [1, 2]))
                            <button onclick="document.getElementById('addPhaseModal').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                + Shto Faza
                            </button>
                        @endif
                    </div>
                    
                    <div class="space-y-4">
                        @forelse ($projekt->fazat->sortBy('renditja') as $faza)
                            <div class="p-4 border rounded-lg shadow-sm">

                                
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                                    <div class="md:col-span-2">
                                        <p class="font-bold text-gray-800">{{ $faza->emri_fazes }}</p>
                                        <p class="text-sm text-gray-500">{{ $faza->pershkrimi }}</p>
                                    </div>
                                    <div>
                                        <p><strong>Statusi:</strong> {{ $faza->pivot->statusi_fazes }}</p>
                                    </div>
                                    <div class="flex justify-end">
                                        @if(auth()->check() && in_array(auth()->user()->rol_id, [1, 2]))
                                            <form action="{{ route('projektet.faza.destroy', ['projekt' => $projekt->projekt_id, 'faza_pivot_id' => $faza->pivot->id]) }}" method="POST" onsubmit="return confirm('Jeni i sigurt që doni ta fshini këtë fazë nga projekti?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200">
                                                    <i class="fas fa-trash-alt mr-1"></i> Fshi
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">Nuk ka faza të shtuara për këtë projekt.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Materials Section -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Materialet e Projektit</h3>
                        @if(auth()->check() && in_array(auth()->user()->rol_id, [1, 2]))
                            <button onclick="document.getElementById('addMaterialModal').classList.remove('hidden')" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                + Shto Material
                            </button>
                        @endif
                    </div>
                    
                    <div class="space-y-4">
                        @if($projekt->materialet->isEmpty())
                            <p class="text-gray-500">Nuk ka materiale të shtuara për këtë projekt.</p>
                        @else
                            <ul class="list-disc list-inside">
                                @foreach($projekt->materialet as $material)
                                    <li class="flex items-center justify-between py-1">
                                        <span>
                                            {{ $material->emri_materialit ?? 'Material pa emër' }}: 
                                            {{ $material->pivot->sasia_perdorur ?? 0 }} 
                                            {{ $material->njesia_matese ?? 'copë' }}
                                        </span>
                                        
                                        <form action="{{ route('projekt-materiale.destroy', $material->material_id) }}" method="POST" onsubmit="return confirm('Jeni i sigurt që doni ta fshini këtë material?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150">
                                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Fshij
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Dokumentet e Projektit</h3>
                    
                    <!-- Upload Form -->
                    @if(auth()->check() && in_array(auth()->user()->rol_id, [1, 2]))
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg border">
                            <h4 class="text-md font-medium mb-3">Ngarko Dokument të Ri</h4>
                            <form action="{{ route('projektet.dokumentet.store', ['projekt' => $projekt->projekt_id]) }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="uploadForm">
                                @csrf
                                
                                <div>
                                    <x-input-label for="pershkrimi" :value="__('Përshkrimi')" />
                                    <x-text-input id="pershkrimi" name="pershkrimi" type="text" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('pershkrimi')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="kategoria" :value="__('Kategoria')" />
                                    <select id="kategoria" name="kategoria" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Zgjidh kategorinë (opsionale)</option>
                                        <option value="vizatim">Vizatim</option>
                                        <option value="dimension">Dimension</option>
                                        <option value="material">Material</option>
                                        <option value="3d_model">Model 3D</option>
                                        <option value="excel">Excel</option>
                                        <option value="tjeter">Tjetër</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('kategoria')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="dokument" :value="__('Skedari')" />
                                    <input id="dokument" name="dokument" type="file" class="mt-1 block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                                        file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700
                                        hover:file:bg-indigo-100" required />
                                    <p class="mt-1 text-sm text-gray-500" id="file-upload-helper">Formate të lejuara: Imazhe (JPG, PNG, GIF, BMP, SVG, WEBP), PDF, Word (DOC, DOCX), Excel (XLS, XLSX, CSV), CAD (DWG, DXF), 3D (STL, OBJ, FBX, STEP, IGES), ZIP, RAR (max 200MB)</p>
                                    <div id="uploadError" class="text-sm text-red-600 mt-2"></div>
                                    <x-input-error :messages="$errors->get('dokument')" class="mt-2" />
                                </div>
                                
                                <div class="flex justify-end">
                                    <x-primary-button type="submit" id="uploadButton" class="ml-3 opacity-50" disabled>{{ __('Ngarko') }}</x-primary-button>
                                </div>
                            </form>
                        </div>
                    @endif
                    
                    <!-- Nav Tabs for Document Categories -->
                    <div class="mb-4 border-b border-gray-200">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="docTabs">
                            <li class="mr-2">
                                <a href="#" data-kategoria="all" class="tab-link inline-block p-4 border-b-2 rounded-t-lg">Të Gjitha</a>
                            </li>
                            <li class="mr-2">
                                <a href="#" data-kategoria="Vizatim" class="tab-link inline-block p-4 border-b-2 border-transparent rounded-t-lg">Vizatim</a>
                            </li>
                            <li class="mr-2">
                                <a href="#" data-kategoria="Dimension" class="tab-link inline-block p-4 border-b-2 border-transparent rounded-t-lg">Dimension</a>
                            </li>
                            <li class="mr-2">
                                <a href="#" data-kategoria="Material" class="tab-link inline-block p-4 border-b-2 border-transparent rounded-t-lg">Material</a>
                            </li>
                            <li class="mr-2">
                                <a href="#" data-kategoria="Model3D" class="tab-link inline-block p-4 border-b-2 border-transparent rounded-t-lg">Model 3D</a>
                            </li>
                            <li class="mr-2">
                                <a href="#" data-kategoria="Excel" class="tab-link inline-block p-4 border-b-2 border-transparent rounded-t-lg">Excel</a>
                            </li>
                            <li class="mr-2">
                                <a href="#" data-kategoria="Tjetër" class="tab-link inline-block p-4 border-b-2 border-transparent rounded-t-lg">Të Tjera</a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Documents List with Previews -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Dokumentet e Bashkangjitura</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse($projekt->dokumentet as $index => $dokument)
                                <div class="border rounded-lg overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow dokument-item p-4" 
                                     data-index="{{ $index }}" 
                                     data-id="{{ $dokument->dokument_id ?? 0 }}"
                                     data-kategoria="{{ ucfirst($dokument->kategoria ?? 'Tjetër') }}">
                                    @php
                                        $extension = strtolower(pathinfo($dokument->emri_skedarit ?? '', PATHINFO_EXTENSION));
                                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
                                        
                                        // Llojet e dokumenteve që mund të shfaqen direkt në shfletues
                                        $directViewable = [
                                            'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'pdf', 'txt', 'csv',
                                            'html', 'htm', 'css', 'js', 'json', 'xml', 'md', 'log', 'ini', 'conf', 'env'
                                        ];
                                        
                                        // Llojet e dokumenteve Office që mund të shfaqen me Google Docs Viewer
                                        $officeTypes = [
                                            'doc', 'docx', 'ppt', 'pptx', 'rtf', 'odt', 'odp'
                                        ];
                                        
                                        // Llojet e dokumenteve Excel që mund të hapen me Google Sheets
                                        $excelTypes = [
                                            'xls', 'xlsx', 'csv', 'ods'
                                        ];
                                        
                                        $isDirectViewable = in_array($extension, $directViewable);
                                        $isOfficeDoc = in_array($extension, $officeTypes);
                                        $isExcelDoc = in_array($extension, $excelTypes);
                                        
                                        // Gjenero URL-në e duhur bazuar në llojin e dokumentit
                                        $viewUrl = '';
                                        $googleSheetsUrl = '';
                                        
                                        if ($isDirectViewable) {
                                            $viewUrl = route('projektet.dokumentet.view', ['projekt' => $projekt->projekt_id, 'id' => $dokument->dokument_id ?? 0]);
                                        } elseif ($isOfficeDoc) {
                                            $fileUrl = route('projektet.dokumentet.download', ['projekt' => $projekt->projekt_id, 'id' => $dokument->dokument_id ?? 0]);
                                            $viewUrl = 'https://docs.google.com/viewer?url=' . urlencode($fileUrl) . '&embedded=true';
                                        }
                                        
                                        if ($isExcelDoc) {
                                            $fileUrl = route('projektet.dokumentet.download', ['projekt' => $projekt->projekt_id, 'id' => $dokument->dokument_id ?? 0]);
                                            $viewUrl = 'https://docs.google.com/viewer?url=' . urlencode($fileUrl) . '&embedded=true';
                                        }
                                    @endphp
                                    
                                    <div class="mb-3">
                                        <h4 class="font-medium text-gray-900">{{ $dokument->emri_skedarit ?? 'Dokument' }}</h4>
                                        <p class="text-sm text-gray-500">{{ $dokument->pershkrimi ?? '' }}</p>
                                    </div>
                                    
                                    @if($isImage)
                                        <div class="h-40 bg-gray-100 overflow-hidden flex items-center justify-center mb-3">
                                            <img src="{{ asset('storage/' . ($dokument->rruga_skedarit ?? '')) }}" 
                                                 alt="{{ $dokument->emri_skedarit ?? 'Imazh' }}" 
                                                 class="max-h-full max-w-full object-contain cursor-pointer preview-image"
                                                 data-index="{{ $index }}"
                                                 loading="lazy">
                                        </div>
                                    @endif
                                    
                                    <div class="flex space-x-2 mt-2">
                                        @if($isDirectViewable || $isOfficeDoc)
                                            <a href="{{ $viewUrl }}" target="_blank" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">
                                                <i class="fas fa-eye mr-1"></i> Shiko
                                            </a>
                                        @endif
                                        
                                        @if($isExcelDoc)
                                            <a href="{{ route('projektet.dokumentet.download', ['projekt' => $projekt->projekt_id, 'id' => $dokument->dokument_id ?? 0]) }}" 
                                               class="text-sm bg-green-100 text-green-700 px-3 py-1 rounded hover:bg-green-200"
                                               title="Shkarko dhe hape në Google Sheets">
                                                <i class="fas fa-file-excel mr-1"></i> Excel
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('projektet.dokumentet.download', ['projekt' => $projekt->projekt_id, 'id' => $dokument->dokument_id ?? 0]) }}" class="text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded hover:bg-gray-200">
                                            <i class="fas fa-download mr-1"></i> Shkarko
                                        </a>
                                        
                                        @if(can('dokumentet', 'delete') && (hasRole(['administrator', 'menaxher']) || $dokument->perdorues_id === auth()->id()))
                                            <form action="{{ route('projektet.dokumentet.destroy', ['projekt' => $projekt->projekt_id, 'id' => $dokument->dokument_id ?? 0]) }}" method="POST" onsubmit="return confirm('Jeni i sigurt që doni ta fshini këtë dokument?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm text-red-600 hover:text-red-800 px-3 py-1 rounded hover:bg-red-50">
                                                    <i class="fas fa-trash-alt mr-1"></i> Fshi
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-3">
                                    <p class="text-gray-500" id="no-docs-message">Nuk ka dokumente të ngarkuara për këtë projekt.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Add Phase Modal -->
    <div id="addPhaseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-10 mx-auto p-4 max-w-md w-full">
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Shto Faza të Re</h3>
                        <button type="button" onclick="document.getElementById('addPhaseModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <form action="{{ route('projektet.faza.store', $projekt->projekt_id) }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label for="faza_id" class="block text-sm font-medium text-gray-700 mb-1">Zgjidh Faza</label>
                        <select name="faza_id" id="faza_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            <option value="">Zgjidh një fazë</option>
                            @foreach($fazat as $faza)
                                <option value="{{ $faza->id }}">{{ $faza->emri_fazes }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="data_fillimit" class="block text-sm font-medium text-gray-700 mb-1">Data e Fillimit</label>
                            <input type="date" name="data_fillimit" id="data_fillimit" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label for="data_perfundimit" class="block text-sm font-medium text-gray-700 mb-1">Data e Përfundimit</label>
                            <input type="date" name="data_perfundimit" id="data_perfundimit" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        </div>
                    </div>
                    <div>
                        <label for="pershkrimi_faze" class="block text-sm font-medium text-gray-700 mb-1">Përshkrim (opsional)</label>
                        <textarea name="pershkrimi" id="pershkrimi_faze" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                    </div>
                    <div class="mt-4 flex justify-end space-x-3">
                        <button type="button" onclick="document.getElementById('addPhaseModal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Anulo
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Ruaj
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Material Modal -->
    <div id="addMaterialModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-10 mx-auto p-4 max-w-md w-full">
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Shto Material të Ri</h3>
                        <button type="button" onclick="document.getElementById('addMaterialModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <form id="addMaterialForm" action="{{ route('projekte.materiale.store', $projekt->projekt_id) }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    <input type="hidden" name="projekt_id" value="{{ $projekt->projekt_id }}">
                    
                    <div class="mb-4">
                        <label for="material_id" class="block text-sm font-medium text-gray-700 mb-2">Zgjidh Materialin:</label>
                        <div class="relative">
                            <select id="material_id" name="material_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-base appearance-none" required>
                                <option value="">Zgjidh një material</option>
                                @if(isset($materialet) && $materialet->count() > 0)
                                    @foreach($materialet as $material)
                                        @if($material)
                                            <option value="{{ $material->material_id }}" data-njesia="{{ $material->njesia_matese }}">
                                                {{ $material->emri_materialit }} ({{ $material->njesia_matese }})
                                            </option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        @error('material_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="sasia_perdorur" class="block text-sm font-medium text-gray-700 mb-2">Sasia:</label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="number" step="0.01" min="0.01" name="sasia_perdorur" id="sasia_perdorur" 
                                class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-base" 
                                placeholder="0.00" required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span id="njesia_shkurtesa" class="text-gray-500 text-base font-medium">
                                    copë
                                </span>
                            </div>
                        </div>
                        @error('sasia_perdorur')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="pershkrimi" class="block text-sm font-medium text-gray-700 mb-2">Shënime (opsionale):</label>
                        <textarea name="pershkrimi" id="pershkrimi" rows="3" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-base"
                            placeholder="Shto shënime shtesë për materialin..."></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-4 pt-4 border-t border-gray-100">
                        <button type="button" onclick="document.getElementById('addMaterialModal').classList.add('hidden')" 
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            Anulo
                        </button>
                        <button type="submit" 
                            class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            Shto Materialin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Email dropdown styles */
        .dropdown-item-form {
            width: 100%;
            padding: 0;
            margin: 0;
        }
        
        /* Compact and touch-friendly styles */
        .preview-image {
            min-height: 120px;
            cursor: pointer;
            transition: all 0.2s ease;
            border-radius: 0.375rem;
            object-fit: cover;
        }
        .preview-image:hover {
            opacity: 0.9;
        }
        .preview-image:active {
            transform: scale(0.98);
        }
        .document-actions a, 
        .document-actions button {
            min-width: 36px;
            min-height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.25rem;
        }
        .document-card {
            transition: all 0.2s ease;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .document-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }
        @media (max-width: 640px) {
            .preview-image {
                min-height: 160px;
            }
        }
        /* Compact form styles */
        .form-input-compact {
            padding: 0.375rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
        }
        .form-select-compact {
            padding: 0.375rem 1.75rem 0.375rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle form submission
            const form = document.getElementById('addMaterialForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(form);
                    const submitButton = form.querySelector('button[type="submit"]');
                    const originalButtonText = submitButton.innerHTML;
                    
                    // Disable submit button and show loading state
                    submitButton.disabled = true;
                    submitButton.innerHTML = 'Duke shtuar...';
                    
                    // Get CSRF token from meta tag
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    // Convert FormData to URL-encoded string
                    const urlEncodedData = new URLSearchParams();
                    for (const [key, value] of formData.entries()) {
                        urlEncodedData.append(key, value);
                    }
                    
                    // Send the request
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: urlEncodedData,
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.message || 'Ndodhi një gabim gjatë përpunimit');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            alert(data.message || 'Materiali u shtua me sukses!');
                            // Close the modal
                            const modal = document.getElementById('addMaterialModal');
                            if (modal) modal.classList.add('hidden');
                            // Reload the page to show the new material
                            window.location.reload();
                        } else {
                            throw new Error(data.message || 'Diçka shkoi keq');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Show error message
                        alert('Gabim: ' + (error.message || 'Ndodhi një gabim gjatë shtimit të materialit'));
                    })
                    .finally(() => {
                        // Re-enable submit button
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;
                    });
                });
            }
            
            // Update unit of measurement when material changes
            const materialSelect = document.getElementById('material_id');
            const sasiaInput = document.getElementById('sasia_perdorur');
            const njesiaSpan = document.getElementById('njesia_shkurtesa');
            
            if (materialSelect) {
                // Initialize with default value
                const initialOption = materialSelect.options[materialSelect.selectedIndex];
                if (initialOption) {
                    const initialNjesia = initialOption.getAttribute('data-njesia') || 'copë';
                    njesiaSpan.textContent = initialNjesia;
                }
                
                // Add change event listener
                materialSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.value) {
                        // Update unit display
                        const njesia = selectedOption.getAttribute('data-njesia') || 'copë';
                        njesiaSpan.textContent = njesia;
                        
                        // Add visual feedback
                        materialSelect.classList.add('border-green-500');
                        setTimeout(() => {
                            materialSelect.classList.remove('border-green-500');
                        }, 1000);
                        
                        // Focus on quantity input after selecting material
                        if (sasiaInput) {
                            sasiaInput.focus();
                        }
                    }
                });
            }
            
            // Add validation for quantity input
            if (sasiaInput) {
                sasiaInput.addEventListener('input', function() {
                    const value = parseFloat(this.value);
                    if (isNaN(value) || value <= 0) {
                        this.classList.add('border-red-500');
                        this.classList.remove('border-green-500');
                    } else {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-green-500');
                        setTimeout(() => {
                            this.classList.remove('border-green-500');
                        }, 1000);
                    }
                });
            }
        }); // Close the first DOMContentLoaded event listener
        
        // File Upload Validation
        document.addEventListener('DOMContentLoaded', function() {
            // Email Dropdown Functionality
            const emailDropdownButton = document.getElementById('emailDropdownButton');
            const emailDropdownMenu = document.getElementById('emailDropdownMenu');
            const emailDropdownContainer = document.getElementById('emailDropdownContainer');
            
            if (emailDropdownButton && emailDropdownMenu && emailDropdownContainer) {
                // Toggle dropdown when button is clicked
                emailDropdownButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    emailDropdownMenu.classList.toggle('show');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!emailDropdownContainer.contains(e.target)) {
                        emailDropdownMenu.classList.remove('show');
                    }
                });
            }
            
            // Display success or error messages
            const urlParams = new URLSearchParams(window.location.search);
            const successMessage = urlParams.get('success');
            const errorMessage = urlParams.get('error');
            
            if (successMessage) {
                Swal.fire({
                    title: 'Sukses!',
                    text: decodeURIComponent(successMessage),
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            } else if (errorMessage) {
                Swal.fire({
                    title: 'Gabim!',
                    text: decodeURIComponent(errorMessage),
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
            
            // File Upload Validation
            const uploadForm = document.getElementById('uploadForm');
            if (uploadForm) {
                const fileInput = document.getElementById('dokument');
                const uploadButton = document.getElementById('uploadButton');
                const errorContainer = document.getElementById('uploadError');

                fileInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (!file) return;

                    // Validate file type - allow most common file types
                    const allowedExtensions = [
                        // Images
                        'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'tiff', 'ico',
                        // Documents
                        'pdf', 'doc', 'docx', 'txt', 'rtf', 'odt',
                        // Spreadsheets
                        'xls', 'xlsx', 'csv', 'ods',
                        // CAD files
                        'dwg', 'dxf', 'dwf',
                        // 3D files
                        'stl', 'obj', 'fbx', 'step', 'stp', 'iges', 'igs', '3ds', 'blend', 'dae', 'gltf', 'glb',
                        // Archives
                        'zip', 'rar', '7z', 'tar', 'gz',
                        // Other
                        'ppt', 'pptx', 'xml', 'json'
                    ];
                    
                    const fileExtension = file.name.split('.').pop().toLowerCase();
                    if (!allowedExtensions.includes(fileExtension)) {
                        showError('Lloji i skedarit "' + fileExtension + '" nuk lejohet. Shiko formatet e lejuara më poshtë.');
                        return;
                    }

                    // Validate file size (200MB max)
                    const maxSize = 200 * 1024 * 1024; // 200MB
                    if (file.size > maxSize) {
                        showError('Madhësia maksimale e lejuar për skedarët është 200MB.');
                        return;
                    }

                    // All checks passed
                    errorContainer.textContent = '';
                    uploadButton.disabled = false;
                    uploadButton.classList.remove('opacity-50');
                });

                function showError(message) {
                    if (errorContainer) {
                        errorContainer.textContent = message;
                    }
                    if (uploadButton) {
                        uploadButton.disabled = true;
                        uploadButton.classList.add('opacity-50');
                    }
                    if (fileInput) {
                        fileInput.value = ''; // Reset file input
                    }
                }
            }


            // Document Tab Filtering
            const docTabs = document.getElementById('docTabs');
            if (docTabs) {
                const tabs = docTabs.querySelectorAll('.tab-link');
                const docItems = document.querySelectorAll('.dokument-item');
                const noDocsMessage = document.getElementById('no-docs-message');

                tabs.forEach(tab => {
                    tab.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        // Update active tab styling
                        tabs.forEach(t => {
                            t.classList.remove('active', 'border-indigo-600', 'text-indigo-600');
                            t.classList.add('border-transparent');
                        });
                        this.classList.add('active', 'border-indigo-600', 'text-indigo-600');
                        this.classList.remove('border-transparent');
                        
                        // Filter documents by category
                        const category = this.getAttribute('data-kategoria');
                        let visibleCount = 0;

                        docItems.forEach(item => {
                            const itemCategory = item.getAttribute('data-kategoria');
                            if (category === 'all' || itemCategory === category) {
                                item.style.display = 'block';
                                visibleCount++;
                            } else {
                                item.style.display = 'none';
                            }
                        });
                        
                        // Update no documents message
                        if (noDocsMessage) {
                            if (visibleCount === 0 && docItems.length > 0) {
                                noDocsMessage.style.display = 'block';
                                noDocsMessage.textContent = `Nuk ka dokumente në kategorinë '${this.textContent.trim()}'.`;
                            } else {
                                noDocsMessage.style.display = 'none';
                            }
                        }
                    });
                });
                
                // Initialize first tab as active
                if (tabs.length > 0) {
                    tabs[0].click();
                }
            }
            
            // Initialize document gallery if there are any preview images
            if (document.querySelector('.preview-image')) {
                const gallery = new DocumentGallery({
                    container: document.body,
                    selector: '.preview-image',
                    showThumbnails: true,
                    showDownload: true
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
