{{-- 
    Komponent për butonat e veprimit (Shiko/Modifiko/Fshij)
    Përdorimi: 
    <x-action-buttons-with-view 
        view-route="route('emri.show', $id)" 
        edit-route="route('emri.edit', $id)" 
        delete-route="route('emri.destroy', $id)" 
        delete-id="delete-form-id" 
    />
--}}

@props(['viewRoute', 'editRoute', 'deleteRoute', 'deleteId'])

<div class="flex items-center space-x-3">
    <a href="{{ $viewRoute }}" class="text-blue-600 hover:text-blue-900 flex items-center p-2" title="Shiko">
        <i class="fas fa-eye"></i>
    </a>
    
    <a href="{{ $editRoute }}" class="text-indigo-600 hover:text-indigo-900 flex items-center p-2" title="Modifiko">
        <i class="fas fa-edit"></i>
    </a>
    
    <form id="{{ $deleteId }}" action="{{ $deleteRoute }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" 
                onclick="event.preventDefault(); if(confirm('Jeni i sigurt që dëshironi të fshini këtë?')) { document.getElementById('{{ $deleteId }}').submit(); }" 
                class="text-red-600 hover:text-red-900 flex items-center p-2" title="Fshij">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
</div>
