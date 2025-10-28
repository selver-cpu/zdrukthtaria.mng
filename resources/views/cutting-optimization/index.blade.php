<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Optimizimi i Prerjes - Cutting Optimization') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Project Selection -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Zgjidh Projektin</h3>
                    <form method="GET" action="{{ route('cutting-optimization.index') }}">
                        <div class="flex gap-4">
                            <select name="projekt_id" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                                <option value="">-- Zgjidh një projekt --</option>
                                @foreach($projektet as $p)
                                    <option value="{{ $p->projekt_id }}" {{ $projekt && $projekt->projekt_id == $p->projekt_id ? 'selected' : '' }}>
                                        {{ $p->emri_projektit }} - {{ $p->klient->emri_klientit ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            @if($projekt)
                <!-- Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Veprime</h3>
                        <div class="flex flex-wrap gap-4">
                            <!-- Export to XML -->
                            <a href="{{ route('cutting-optimization.export', $projekt->projekt_id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Eksporto XML
                            </a>
                            
                            <!-- Import XML -->
                            <button onclick="document.getElementById('importModal').classList.remove('hidden')"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                Importo XML
                            </button>
                            
                            <!-- Visualize -->
                            <a href="{{ route('cutting-optimization.visualize', $projekt->projekt_id) }}" 
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-white hover:bg-purple-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Vizualizo Planin
                            </a>
                            
                            <!-- Go to Dimensions -->
                            <a href="{{ route('projektet-dimensions.index', ['projekt_id' => $projekt->projekt_id]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-white hover:bg-gray-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                                Shiko Dimensionet
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Visualization Preview -->
                @if(!empty($visualization))
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Paraqitje e Shpejtë</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($visualization as $piece)
                                    <div class="border rounded-lg p-4" style="border-left: 4px solid {{ $piece['color'] }}">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-semibold">{{ $piece['label'] }}</h4>
                                            <span class="text-xs bg-gray-100 px-2 py-1 rounded">x{{ $piece['quantity'] }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2">{{ $piece['material'] }}</p>
                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                            <div>
                                                <span class="text-gray-500">Gjatësia:</span>
                                                <span class="font-medium">{{ $piece['length'] }} mm</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Gjerësia:</span>
                                                <span class="font-medium">{{ $piece['width'] }} mm</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Trashësia:</span>
                                                <span class="font-medium">{{ $piece['thickness'] }} mm</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Sipërfaqja:</span>
                                                <span class="font-medium">{{ number_format($piece['area'], 2) }} m²</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                                <h4 class="font-semibold mb-2">Përmbledhje</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Copë totale:</span>
                                        <span class="font-bold text-lg block">{{ count($visualization) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Sasi totale:</span>
                                        <span class="font-bold text-lg block">{{ array_sum(array_column($visualization, 'quantity')) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Sipërfaqja totale:</span>
                                        <span class="font-bold text-lg block">{{ number_format(array_sum(array_column($visualization, 'area')), 2) }} m²</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Materiale:</span>
                                        <span class="font-bold text-lg block">{{ count(array_unique(array_column($visualization, 'material'))) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p>Nuk ka dimensione për këtë projekt.</p>
                            <a href="{{ route('projektet-dimensions.create', ['projekt_id' => $projekt->projekt_id]) }}" 
                               class="inline-block mt-4 text-blue-600 hover:text-blue-800">
                                Shto dimensione →
                            </a>
                        </div>
                    </div>
                @endif
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        <p>Zgjidh një projekt për të filluar.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Importo XML</h3>
                <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('cutting-optimization.import', $projekt->projekt_id ?? 0) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Zgjidh file XML</label>
                    <input type="file" name="xml_file" accept=".xml" required
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-xs text-gray-500">Formati: XML (max 10MB)</p>
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Anulo
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Importo
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
