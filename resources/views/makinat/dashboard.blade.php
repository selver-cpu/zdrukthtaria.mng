<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Planifikimi i Mirëmbajtjes (PM)') }}</h2>
            <a href="{{ route('makinat.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">← Makinat</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-sm border-t-4 border-red-500">
                    <div class="px-5 py-4 border-b">
                        <h3 class="font-semibold text-red-700">Të vonuara ({{ $planetVonuar->count() }})</h3>
                    </div>
                    <ul class="divide-y">
                        @forelse($planetVonuar as $plan)
                            <li class="px-5 py-3">
                                <a href="{{ route('makinat.show', $plan->makina) }}" class="font-medium text-indigo-700 hover:underline">{{ $plan->makina->emri }}</a>
                                <div class="text-sm text-gray-700">{{ $plan->titulli }}</div>
                                <div class="text-xs text-red-600">Radha: {{ optional($plan->data_radhese)->format('d.m.Y') }}</div>
                            </li>
                        @empty
                            <li class="px-5 py-6 text-sm text-gray-500">Asnjë PM e vonuar — mirë!</li>
                        @endforelse
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-sm border-t-4 border-amber-500">
                    <div class="px-5 py-4 border-b">
                        <h3 class="font-semibold text-amber-700">Sot ({{ $planetSot->count() }})</h3>
                    </div>
                    <ul class="divide-y">
                        @forelse($planetSot as $plan)
                            <li class="px-5 py-3">
                                <a href="{{ route('makinat.show', $plan->makina) }}" class="font-medium text-indigo-700 hover:underline">{{ $plan->makina->emri }}</a>
                                <div class="text-sm text-gray-700">{{ $plan->titulli }}</div>
                                <div class="text-xs text-gray-500">{{ $plan->frekuenca_label }}</div>
                            </li>
                        @empty
                            <li class="px-5 py-6 text-sm text-gray-500">Asnjë detyrë sot.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-sm border-t-4 border-blue-500">
                    <div class="px-5 py-4 border-b">
                        <h3 class="font-semibold text-blue-700">7 ditët e ardhshme ({{ $planetSjavore->count() }})</h3>
                    </div>
                    <ul class="divide-y">
                        @forelse($planetSjavore as $plan)
                            <li class="px-5 py-3">
                                <a href="{{ route('makinat.show', $plan->makina) }}" class="font-medium text-indigo-700 hover:underline">{{ $plan->makina->emri }}</a>
                                <div class="text-sm text-gray-700">{{ $plan->titulli }}</div>
                                <div class="text-xs text-gray-500">{{ optional($plan->data_radhese)->format('d.m.Y') }}</div>
                            </li>
                        @empty
                            <li class="px-5 py-6 text-sm text-gray-500">Asgjë e planifikuar këtë javë.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="font-semibold text-gray-900">Punë të hapura / në progres</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Makina</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Titulli</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Prioriteti</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statusi</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Përgjegjësi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($puneTeHapura as $ev)
                                <tr>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('makinat.show', $ev->makina) }}" class="text-indigo-700 hover:underline">{{ $ev->makina->emri }}</a>
                                    </td>
                                    <td class="px-4 py-2">{{ $ev->titulli }}</td>
                                    <td class="px-4 py-2">{{ $ev->prioriteti_label }}</td>
                                    <td class="px-4 py-2">{{ $ev->statusi_label }}</td>
                                    <td class="px-4 py-2">{{ $ev->perdoruesi?->emri ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">Nuk ka punë të hapura.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
