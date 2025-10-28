<div class="bg-white shadow rounded-lg p-4 mb-4">
    <h3 class="text-lg font-medium text-gray-900 mb-2">Ngarko Dokument të Ri</h3>
    
    <form action="{{ route('projektet.dokumentet.store', $projekt) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        
        <div>
            <label for="dokument" class="block text-sm font-medium text-gray-700">Zgjidh Skedarin</label>
            <input type="file" name="dokument" id="dokument" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            <p class="mt-1 text-xs text-gray-500">Formate të lejuara: PDF, Excel, Word, JPG, PNG, ZIP, STL, STEP, SKP, DWG (max 20MB)</p>
        </div>
        
        <div>
            <label for="pershkrimi" class="block text-sm font-medium text-gray-700">Përshkrimi</label>
            <input type="text" name="pershkrimi" id="pershkrimi" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Përshkrim i shkurtër për dokumentin">
        </div>
        
        <div>
            <label for="kategoria" class="block text-sm font-medium text-gray-700">Kategoria</label>
            <select name="kategoria" id="kategoria" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">Zgjidh automatikisht</option>
                <option value="vizatim">Vizatim / Plan teknik</option>
                <option value="dimension">Dimensione</option>
                <option value="material">Materiale</option>
                <option value="3d_model">Model 3D</option>
                <option value="excel">Excel</option>
                <option value="foto">Foto</option>
                <option value="tjeter">Tjetër</option>
            </select>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Ngarko Dokumentin
            </button>
        </div>
    </form>
</div>
