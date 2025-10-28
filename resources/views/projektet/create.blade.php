<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Krijo Projekt të Ri') }}
        </h2>
    </x-slot>

    <div class="py-12">
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('form');
                form.addEventListener('submit', function() {
                    // Disable the submit button
                    const submitButton = this.querySelector('button[type="submit"]');
                    submitButton.disabled = true;
                    submitButton.innerHTML = 'Duke ruajtur...';
                });
            });
        </script>
        @endpush
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-4 font-medium text-sm text-red-600">
                            <div class="font-bold text-red-700">{{ __('Whoops! Diçka shkoi keq.') }}</div>
                            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('projektet.store') }}">
                        @csrf

                        <!-- Emri i Projektit -->
                        <div>
                            <x-input-label for="emri_projektit" :value="__('Emri i Projektit')" />
                            <x-text-input id="emri_projektit" class="block mt-1 w-full" type="text" name="emri_projektit" :value="old('emri_projektit')" required autofocus />
                            <x-input-error :messages="$errors->get('emri_projektit')" class="mt-2" />
                        </div>

                        <!-- Klienti -->
                        <div class="mt-4">
                            <x-input-label for="klient_id" :value="__('Klienti')" />
                            <select id="klient_id" name="klient_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Zgjidh një klient</option>
                                @foreach ($klientet as $klient)
                                    <option value="{{ $klient->klient_id }}">{{ $klient->emri_klientit }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('klient_id')" class="mt-2" />
                        </div>

                        <!-- Statusi -->
                        <div class="mt-4">
                            <x-input-label for="status_id" :value="__('Statusi')" />
                            <select id="status_id" name="status_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Zgjidh një status</option>
                                @foreach ($statuset as $status)
                                    <option value="{{ $status->status_id }}">{{ $status->emri_statusit }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status_id')" class="mt-2" />
                        </div>

                        <!-- Pershkrimi -->
                        <div class="mt-4">
                            <x-input-label for="pershkrimi" :value="__('Përshkrimi')" />
                            <textarea id="pershkrimi" name="pershkrimi" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('pershkrimi') }}</textarea>
                            <x-input-error :messages="$errors->get('pershkrimi')" class="mt-2" />
                        </div>

                        <!-- Mjeshtri -->
                        <div class="mt-4">
                            <x-input-label for="mjeshtri_caktuar_id" :value="__('Mjeshtri i Caktuar')" />
                            <select id="mjeshtri_caktuar_id" name="mjeshtri_caktuar_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Zgjidh një mjeshtër (opsionale)</option>
                                @foreach ($mjeshtrat as $mjeshter)
                                    <option value="{{ $mjeshter->perdorues_id }}" {{ old('mjeshtri_caktuar_id') == $mjeshter->perdorues_id ? 'selected' : '' }}>{{ $mjeshter->emri }} {{ $mjeshter->mbiemri }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('mjeshtri_caktuar_id')" class="mt-2" />
                        </div>

                        <!-- Montuesi -->
                        <div class="mt-4">
                            <x-input-label for="montuesi_caktuar_id" :value="__('Montuesi i Caktuar')" />
                            <select id="montuesi_caktuar_id" name="montuesi_caktuar_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Zgjidh një montues (opsionale)</option>
                                @foreach ($montuesit as $montues)
                                    <option value="{{ $montues->perdorues_id }}" {{ old('montuesi_caktuar_id') == $montues->perdorues_id ? 'selected' : '' }}>{{ $montues->emri }} {{ $montues->mbiemri }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('montuesi_caktuar_id')" class="mt-2" />
                        </div>

                        <!-- Data e Fillimit -->
                        <div class="mt-4">
                            <x-input-label for="data_fillimit_parashikuar" :value="__('Data e Fillimit (Parashikuar)')" />
                            <x-text-input id="data_fillimit_parashikuar" class="block mt-1 w-full" type="date" name="data_fillimit_parashikuar" :value="old('data_fillimit_parashikuar')" />
                            <x-input-error :messages="$errors->get('data_fillimit_parashikuar')" class="mt-2" />
                        </div>

                        <!-- Data e Perfundimit -->
                        <div class="mt-4">
                            <x-input-label for="data_perfundimit_parashikuar" :value="__('Data e Përfundimit (Parashikuar)')" />
                            <x-text-input id="data_perfundimit_parashikuar" class="block mt-1 w-full" type="date" name="data_perfundimit_parashikuar" :value="old('data_perfundimit_parashikuar')" />
                            <x-input-error :messages="$errors->get('data_perfundimit_parashikuar')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Ruaj Projektin') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
