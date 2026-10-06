<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edito Makinën') }}: {{ $makina->emri }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('makinat.update', $makina) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('makinat._form')
                    <div class="flex justify-end gap-3 pt-4">
                        <a href="{{ route('makinat.show', $makina) }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">Anulo</a>
                        <x-primary-button>Përditëso</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
