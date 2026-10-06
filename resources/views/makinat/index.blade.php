<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Makinat & Mirëmbajtja') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('mirembajtje.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-500">
                    Planifikimi PM
                </a>
                @if($canManage)
                    <a href="{{ route('makinat.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        + Shto Makinë
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-slate-600">
                    <div class="text-sm text-gray-500">Makina totale</div>
                    <div class="text-2xl font-semibold">{{ $stats['totale'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-green-600">
                    <div class="text-sm text-gray-500">Aktive</div>
                    <div class="text-2xl font-semibold">{{ $stats['aktive'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-red-600">
                    <div class="text-sm text-gray-500">PM të vonuara</div>
                    <div class="text-2xl font-semibold">{{ $stats['vonuara'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 border-l-4 border-amber-500">
                    <div class="text-sm text-gray-500">Punë të hapura</div>
                    <div class="text-2xl font-semibold">{{ $stats['te_hapura'] }}</div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Kërko emër, markë, model..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <select name="lloji" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Të gjitha llojet</option>
                        @foreach($llojet as $key => $label)
                            <option value="{{ $key }}" @selected(request('lloji') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="statusi" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Të gjitha statuset</option>
                        @foreach($statuset as $key => $label)
                            <option value="{{ $key }}" @selected(request('statusi') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="inline-flex justify-center items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-500">Filtro</button>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Makina</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lloji</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statusi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kritik.</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orë pune</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PM</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Veprime</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($makinat as $makina)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('makinat.show', $makina) }}" class="font-medium text-indigo-700 hover:underline">{{ $makina->emri }}</a>
                                        <div class="text-xs text-gray-500">{{ $makina->marka }} {{ $makina->modeli }} · {{ $makina->lokacioni }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $makina->lloji_label }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                                            {{ $makina->statusi === 'aktive' ? 'bg-green-100 text-green-800' : ($makina->statusi === 'ne_mirembajtje' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $makina->statusi_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold">{{ $makina->kritikaliteti }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ number_format($makina->ore_pune, 1) }} h</td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ $makina->planet_aktive_count }} aktive
                                        @if($makina->planet_vonuar_count > 0)
                                            <span class="ml-1 inline-flex px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">{{ $makina->planet_vonuar_count }} vonuar</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm space-x-2">
                                        <a href="{{ route('makinat.show', $makina) }}" class="text-indigo-600 hover:text-indigo-800">Shiko</a>
                                        @if($canManage)
                                            <a href="{{ route('makinat.edit', $makina) }}" class="text-gray-600 hover:text-gray-900">Edito</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">Nuk ka makina. Shto makinën e parë për të filluar mirëmbajtjen.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $makinat->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
