@php
    $makina = $makina ?? null;
@endphp

<div>
    <x-input-label for="emri" value="Emri i makinës" />
    <x-text-input id="emri" name="emri" class="block mt-1 w-full" :value="old('emri', $makina->emri ?? '')" required />
    <x-input-error :messages="$errors->get('emri')" class="mt-2" />
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="lloji" value="Lloji" />
        <select id="lloji" name="lloji" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            @foreach($llojet as $key => $label)
                <option value="{{ $key }}" @selected(old('lloji', $makina->lloji ?? '') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('lloji')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="statusi" value="Statusi" />
        <select id="statusi" name="statusi" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            @foreach($statuset as $key => $label)
                <option value="{{ $key }}" @selected(old('statusi', $makina->statusi ?? 'aktive') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="marka" value="Marka" />
        <x-text-input id="marka" name="marka" class="block mt-1 w-full" :value="old('marka', $makina->marka ?? '')" />
    </div>
    <div>
        <x-input-label for="modeli" value="Modeli" />
        <x-text-input id="modeli" name="modeli" class="block mt-1 w-full" :value="old('modeli', $makina->modeli ?? '')" />
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="numri_serik" value="Numri serik" />
        <x-text-input id="numri_serik" name="numri_serik" class="block mt-1 w-full" :value="old('numri_serik', $makina->numri_serik ?? '')" />
    </div>
    <div>
        <x-input-label for="lokacioni" value="Lokacioni" />
        <x-text-input id="lokacioni" name="lokacioni" class="block mt-1 w-full" :value="old('lokacioni', $makina->lokacioni ?? '')" />
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
        <x-input-label for="kritikaliteti" value="Kritikaliteti" />
        <select id="kritikaliteti" name="kritikaliteti" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            @foreach(['A' => 'A – Kritike', 'B' => 'B – E rëndësishme', 'C' => 'C – Standarde'] as $key => $label)
                <option value="{{ $key }}" @selected(old('kritikaliteti', $makina->kritikaliteti ?? 'B') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <x-input-label for="data_blerjes" value="Data e blerjes" />
        <x-text-input id="data_blerjes" type="date" name="data_blerjes" class="block mt-1 w-full" :value="old('data_blerjes', optional($makina->data_blerjes ?? null)->format('Y-m-d'))" />
    </div>
    <div>
        <x-input-label for="ore_pune" value="Orë pune (matës)" />
        <x-text-input id="ore_pune" type="number" step="0.1" min="0" name="ore_pune" class="block mt-1 w-full" :value="old('ore_pune', $makina->ore_pune ?? 0)" />
    </div>
</div>

<div>
    <x-input-label for="shenime" value="Shënime" />
    <textarea id="shenime" name="shenime" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('shenime', $makina->shenime ?? '') }}</textarea>
</div>
