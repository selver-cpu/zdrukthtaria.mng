<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Krijo Klient të Ri') }}
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
                    <form method="POST" action="{{ route('klientet.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Emri i Klientit/Kompanisë -->
                            <div>
                                <x-input-label for="emri" :value="__('Emri i Klientit/Kompanisë')" />
                                <x-text-input id="emri" class="block mt-1 w-full" type="text" name="emri" :value="old('emri')" required autofocus />
                                <x-input-error :messages="$errors->get('emri')" class="mt-2" />
                            </div>

                            <!-- Person Kontakti -->
                            <div>
                                <x-input-label for="person_kontakti" :value="__('Personi i Kontaktit (Opsional)')" />
                                <x-text-input id="person_kontakti" class="block mt-1 w-full" type="text" name="person_kontakti" :value="old('person_kontakti')" />
                                <x-input-error :messages="$errors->get('person_kontakti')" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Telefon -->
                            <div>
                                <x-input-label for="telefon" :value="__('Telefon')" />
                                <x-text-input id="telefon" class="block mt-1 w-full" type="text" name="telefon" :value="old('telefon')" required />
                                <x-input-error :messages="$errors->get('telefon')" class="mt-2" />
                            </div>

                            <!-- Adresa Faturimit -->
                            <div>
                                <x-input-label for="adresa_faturimit" :value="__('Adresa e Faturimit')" />
                                <x-text-input id="adresa_faturimit" class="block mt-1 w-full" type="text" name="adresa_faturimit" :value="old('adresa_faturimit')" required />
                                <x-input-error :messages="$errors->get('adresa_faturimit')" class="mt-2" />
                            </div>

                            <!-- Qyteti -->
                            <div>
                                <x-input-label for="qyteti" :value="__('Qyteti')" />
                                <x-text-input id="qyteti" class="block mt-1 w-full" type="text" name="qyteti" :value="old('qyteti')" required />
                                <x-input-error :messages="$errors->get('qyteti')" class="mt-2" />
                            </div>

                            <!-- Kodi Postar -->
                            <div>
                                <x-input-label for="kodi_postar" :value="__('Kodi Postar')" />
                                <x-text-input id="kodi_postar" class="block mt-1 w-full" type="text" name="kodi_postar" :value="old('kodi_postar')" required />
                                <x-input-error :messages="$errors->get('kodi_postar')" class="mt-2" />
                            </div>

                            <!-- Shteti -->
                            <div class="md:col-span-2">
                                <x-input-label for="shteti" :value="__('Shteti')" />
                                <x-text-input id="shteti" class="block mt-1 w-full" type="text" name="shteti" :value="old('shteti')" required />
                                <x-input-error :messages="$errors->get('shteti')" class="mt-2" />
                            </div>

                            <!-- Shenime -->
                            <div class="md:col-span-2">
                                <x-input-label for="shenime" :value="__('Shënime')" />
                                <textarea id="shenime" name="shenime" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('shenime') }}</textarea>
                                <x-input-error :messages="$errors->get('shenime')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="ml-4">
                                {{ __('Ruaj Klientin') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
