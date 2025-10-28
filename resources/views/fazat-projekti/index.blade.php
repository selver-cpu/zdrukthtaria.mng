<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Fazat e Projektit') }}
            </h2>
            <a href="{{ route('fazat-projekti.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Shto Fazë të Re</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Renditja
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Emri i Fazës
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Përshkrimi
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Veprime</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($fazat as $faza)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $faza->renditja }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $faza->emri_fazes }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $faza->pershkrimi ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <x-action-buttons 
                                                edit-route="{{ route('fazat-projekti.edit', $faza) }}" 
                                                delete-route="{{ route('fazat-projekti.destroy', $faza) }}" 
                                                delete-id="delete-faza-{{ $faza->id }}" 
                                            />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            Nuk ka faza të regjistruara.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $fazat->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
