<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Menaxhimi i Stafit') }}
            </h2>
            <a href="{{ route('stafi.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Shto Staf të Ri</a>
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
                                        Emri
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Roli
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Veprime</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($stafi as $antar)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $antar->emri }} {{ $antar->mbiemri }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $antar->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if(isset($antar->emri_rolit))
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $antar->emri_rolit == 'administrator' ? 'bg-red-100 text-red-800' : ($antar->emri_rolit == 'menaxher' ? 'bg-blue-100 text-blue-800' : ($antar->emri_rolit == 'mjeshtër' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                                    {{ ucfirst($antar->emri_rolit) }}
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    I pacaktuar
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <x-action-buttons 
                                                edit-route="{{ route('stafi.edit', $antar->perdorues_id) }}" 
                                                delete-route="{{ route('stafi.destroy', $antar->perdorues_id) }}" 
                                                delete-id="delete-staf-{{ $antar->perdorues_id }}" 
                                            />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            Nuk ka anëtarë stafi të regjistruar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $stafi->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
