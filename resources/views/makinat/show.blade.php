<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $makina->emri }}</h2>
                <p class="text-sm text-gray-500">{{ $makina->lloji_label }} · {{ $makina->marka }} {{ $makina->modeli }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('makinat.index') }}" class="px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">← Lista</a>
                @if($canManage)
                    <a href="{{ route('makinat.edit', $makina) }}" class="px-3 py-2 bg-gray-800 text-white rounded-md text-sm hover:bg-gray-700">Edito</a>
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

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="text-xs text-gray-500 uppercase">Statusi</div>
                    <div class="mt-1 font-semibold">{{ $makina->statusi_label }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="text-xs text-gray-500 uppercase">Kritikaliteti</div>
                    <div class="mt-1 font-semibold">{{ $makina->kritikaliteti }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="text-xs text-gray-500 uppercase">Orë pune</div>
                    <div class="mt-1 font-semibold">{{ number_format($makina->ore_pune, 1) }} h</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="text-xs text-gray-500 uppercase">Lokacioni</div>
                    <div class="mt-1 font-semibold">{{ $makina->lokacioni ?: '—' }}</div>
                </div>
            </div>

            @if($makina->shenime)
                <div class="bg-white rounded-lg shadow-sm p-4 text-sm text-gray-700">{{ $makina->shenime }}</div>
            @endif

            {{-- PM Plans --}}
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-900">Planet e mirëmbajtjes (PM)</h3>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($makina->planet as $plan)
                        <div class="border rounded-lg p-4 {{ $plan->eshteVonuar() ? 'border-red-300 bg-red-50' : ($plan->eshteSot() ? 'border-amber-300 bg-amber-50' : 'border-gray-200') }}">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-3">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $plan->titulli }}</div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        {{ $plan->frekuenca_label }}
                                        · Radha: {{ optional($plan->data_radhese)->format('d.m.Y') ?? '—' }}
                                        @if(!$plan->aktiv)
                                            <span class="ml-2 text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded">Jo aktiv</span>
                                        @endif
                                        @if($plan->eshteVonuar())
                                            <span class="ml-2 text-xs bg-red-200 text-red-800 px-2 py-0.5 rounded">Vonuar</span>
                                        @elseif($plan->eshteSot())
                                            <span class="ml-2 text-xs bg-amber-200 text-amber-900 px-2 py-0.5 rounded">Sot</span>
                                        @endif
                                    </div>
                                    @if(count($plan->checklist_items))
                                        <ul class="mt-2 text-sm text-gray-700 list-disc list-inside space-y-0.5">
                                            @foreach($plan->checklist_items as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                                @if($canManage && $plan->aktiv)
                                    <form method="POST" action="{{ route('mirembajtje.complete-plan', $plan) }}" class="shrink-0 space-y-2 min-w-[220px]">
                                        @csrf
                                        <textarea name="gjetjet" rows="2" class="w-full text-sm border-gray-300 rounded-md" placeholder="Gjetje / shënime (opsionale)"></textarea>
                                        <input type="number" step="0.1" name="ore_pune" class="w-full text-sm border-gray-300 rounded-md" placeholder="Orë pune aktuale" value="{{ $makina->ore_pune }}">
                                        <button class="w-full px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-500">✓ Shëno si të kryer</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Nuk ka plane PM. Shto planin e parë më poshtë.</p>
                    @endforelse

                    @if($canManage)
                        <details class="border border-dashed border-gray-300 rounded-lg p-4">
                            <summary class="cursor-pointer font-medium text-gray-800">+ Shto plan PM</summary>
                            <form method="POST" action="{{ route('mirembajtje.store-plan', $makina) }}" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                                @csrf
                                <div class="md:col-span-2">
                                    <x-input-label for="titulli" value="Titulli" />
                                    <x-text-input id="titulli" name="titulli" class="block mt-1 w-full" required />
                                </div>
                                <div>
                                    <x-input-label for="frekuenca" value="Frekuenca" />
                                    <select name="frekuenca" id="frekuenca" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                        @foreach($frekuencat as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="intervali" value="Intervali (çdo N)" />
                                    <x-text-input id="intervali" type="number" min="1" name="intervali" class="block mt-1 w-full" value="1" required />
                                </div>
                                <div>
                                    <x-input-label for="data_radhese" value="Data e radhës" />
                                    <x-text-input id="data_radhese" type="date" name="data_radhese" class="block mt-1 w-full" value="{{ now()->toDateString() }}" />
                                </div>
                                <div class="flex items-end">
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="aktiv" value="1" checked class="rounded border-gray-300"> Aktiv
                                    </label>
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="checklist" value="Checklist (një hap për rresht)" />
                                    <textarea id="checklist" name="checklist" rows="4" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" placeholder="Pastro pluhurin&#10;Kontrollo mbrojtësit&#10;Testo E-Stop"></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <x-primary-button>Ruaj planin</x-primary-button>
                                </div>
                            </form>
                        </details>
                    @endif
                </div>
            </div>

            {{-- Work orders / evidence --}}
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Historia / Punët e mirëmbajtjes</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($canManage)
                        <details class="border border-dashed border-gray-300 rounded-lg p-4 mb-4">
                            <summary class="cursor-pointer font-medium text-gray-800">+ Regjistro punë / riparim</summary>
                            <form method="POST" action="{{ route('mirembajtje.store-evidenca', $makina) }}" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                                @csrf
                                <div class="md:col-span-2">
                                    <x-input-label for="ev_titulli" value="Titulli" />
                                    <x-text-input id="ev_titulli" name="titulli" class="block mt-1 w-full" required />
                                </div>
                                <div>
                                    <x-input-label for="lloji" value="Lloji" />
                                    <select name="lloji" id="lloji" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                        @foreach($llojetEvidenca as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="prioriteti" value="Prioriteti" />
                                    <select name="prioriteti" id="prioriteti" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                        @foreach($prioritetet as $key => $label)
                                            <option value="{{ $key }}" @selected($key === 'mesatar')>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="statusi" value="Statusi" />
                                    <select name="statusi" id="statusi" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                        @foreach($statusetEvidenca as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="plan_id" value="Lidhur me plan PM (opsionale)" />
                                    <select name="plan_id" id="plan_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">— Asnjë —</option>
                                        @foreach($makina->planet as $plan)
                                            <option value="{{ $plan->plan_id }}">{{ $plan->titulli }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="pershkrimi" value="Përshkrimi" />
                                    <textarea name="pershkrimi" id="pershkrimi" rows="2" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <x-primary-button>Ruaj</x-primary-button>
                                </div>
                            </form>
                        </details>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Titulli</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Lloji</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statusi</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kush</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($makina->evidenca as $ev)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap">{{ optional($ev->data_kryerjes ?? $ev->data_krijimit)->format('d.m.Y H:i') }}</td>
                                        <td class="px-3 py-2">{{ $ev->titulli }}</td>
                                        <td class="px-3 py-2">{{ $ev->lloji_label }}</td>
                                        <td class="px-3 py-2">
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                                                {{ $ev->statusi === 'perfunduar' ? 'bg-green-100 text-green-800' : ($ev->statusi === 'ne_progres' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ $ev->statusi_label }}
                                            </span>
                                            @if($canManage && $ev->statusi !== 'perfunduar')
                                                <form method="POST" action="{{ route('mirembajtje.update-evidenca', $ev) }}" class="inline ml-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="statusi" value="perfunduar">
                                                    <button class="text-xs text-green-700 hover:underline">Përfundo</button>
                                                </form>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2">{{ $ev->perdoruesi?->emri ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-6 text-center text-gray-500">Nuk ka evidencë ende.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
