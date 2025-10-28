<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifiko Projektin') }}: {{ $projekt->emri_projektit }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('projektet.update', $projekt) }}">
                        @csrf
                        @method('PUT')

                        <!-- Emri i Projektit -->
                        <div>
                            <x-input-label for="emri_projektit" :value="__('Emri i Projektit')" />
                            <x-text-input id="emri_projektit" class="block mt-1 w-full" type="text" name="emri_projektit" :value="old('emri_projektit', $projekt->emri_projektit)" required autofocus />
                            <x-input-error :messages="$errors->get('emri_projektit')" class="mt-2" />
                        </div>

                        <!-- Klienti -->
                        <div class="mt-4">
                            <x-input-label for="klient_id" :value="__('Klienti')" />
                            <select id="klient_id" name="klient_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach ($klientet as $klient)
                                    <option value="{{ $klient->klient_id }}" @selected(old('klient_id', $projekt->klient_id) == $klient->klient_id)>{{ $klient->emri_klientit }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('klient_id')" class="mt-2" />
                        </div>

                        <!-- Statusi -->
                        <div class="mt-4">
                            <x-input-label for="status_id" :value="__('Statusi')" />
                            <select id="status_id" name="status_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach ($statuset as $status)
                                    <option value="{{ $status->status_id }}" @selected(old('status_id', $projekt->status_id) == $status->status_id)>{{ $status->emri_statusit }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status_id')" class="mt-2" />
                        </div>

                        <!-- Pershkrimi -->
                        <div class="mt-4">
                            <x-input-label for="pershkrimi" :value="__('Përshkrimi')" />
                            <textarea id="pershkrimi" name="pershkrimi" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('pershkrimi', $projekt->pershkrimi) }}</textarea>
                            <x-input-error :messages="$errors->get('pershkrimi')" class="mt-2" />
                        </div>

                        <!-- Data e Fillimit -->
                        <div class="mt-4">
                            <x-input-label for="data_fillimit_parashikuar" :value="__('Data e Fillimit (Parashikuar)')" />
                            <x-text-input id="data_fillimit_parashikuar" class="block mt-1 w-full" type="date" name="data_fillimit_parashikuar" :value="old('data_fillimit_parashikuar', $projekt->data_fillimit_parashikuar ? $projekt->data_fillimit_parashikuar->format('Y-m-d') : '')" />
                            <x-input-error :messages="$errors->get('data_fillimit_parashikuar')" class="mt-2" />
                        </div>

                        <!-- Data e Perfundimit -->
                        <div class="mt-4">
                            <x-input-label for="data_perfundimit_parashikuar" :value="__('Data e Përfundimit (Parashikuar)')" />
                            <x-text-input id="data_perfundimit_parashikuar" class="block mt-1 w-full" type="date" name="data_perfundimit_parashikuar" :value="old('data_perfundimit_parashikuar', $projekt->data_perfundimit_parashikuar ? $projekt->data_perfundimit_parashikuar->format('Y-m-d') : '')" />
                            <x-input-error :messages="$errors->get('data_perfundimit_parashikuar')" class="mt-2" />
                        </div>

                        <!-- Mjeshtri -->
                        <div class="mt-4">
                            <x-input-label for="mjeshtri_id" :value="__('Mjeshtri')" />
                            <select id="mjeshtri_id" name="mjeshtri_caktuar_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Zgjidh Mjeshtrin</option>
                                @foreach ($mjeshtre as $mjeshtri)
                                    <option value="{{ $mjeshtri->perdorues_id }}" @selected(old('mjeshtri_caktuar_id', $projekt->mjeshtri_caktuar_id) == $mjeshtri->perdorues_id)>{{ $mjeshtri->emri }} {{ $mjeshtri->mbiemri }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('mjeshtri_caktuar_id')" class="mt-2" />
                        </div>

                        <!-- Montuesi -->
                        <div class="mt-4">
                            <x-input-label for="montuesi_id" :value="__('Montuesi')" />
                            <select id="montuesi_id" name="montuesi_caktuar_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Zgjidh Montuesin</option>
                                @foreach ($montues as $montuesi)
                                    <option value="{{ $montuesi->perdorues_id }}" @selected(old('montuesi_caktuar_id', $projekt->montuesi_caktuar_id) == $montuesi->perdorues_id)>{{ $montuesi->emri }} {{ $montuesi->mbiemri }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('montuesi_caktuar_id')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Përditëso Projektin') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
