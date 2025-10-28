<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifiko Klientin') }}: {{ $klientet->emri_klientit }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('klientet.update', $klientet) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                             <!-- Emri i Klientit/Kompanisë -->
                            <div>
                                <x-input-label for="emri_klientit" :value="__('Emri i Klientit/Kompanisë')" />
                                <x-text-input id="emri_klientit" class="block mt-1 w-full" type="text" name="emri_klientit" :value="old('emri_klientit', $klientet->emri_klientit)" required autofocus />
                                <x-input-error :messages="$errors->get('emri_klientit')" class="mt-2" />
                            </div>

                            <!-- Person Kontakti -->
                            <div>
                                <x-input-label for="person_kontakti" :value="__('Personi i Kontaktit (Opsional)')" />
                                <x-text-input id="person_kontakti" class="block mt-1 w-full" type="text" name="person_kontakti" :value="old('person_kontakti', $klientet->person_kontakti)" />
                                <x-input-error :messages="$errors->get('person_kontakti')" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-input-label for="email_kontakt" :value="__('Email')" />
                                <x-text-input id="email_kontakt" class="block mt-1 w-full" type="email" name="email_kontakt" :value="old('email_kontakt', $klientet->email_kontakt)" required />
                                <x-input-error :messages="$errors->get('email_kontakt')" class="mt-2" />
                            </div>

                            <!-- Telefon -->
                            <div>
                                <x-input-label for="telefon_kontakt" :value="__('Telefon')" />
                                <x-text-input id="telefon_kontakt" class="block mt-1 w-full" type="text" name="telefon_kontakt" :value="old('telefon_kontakt', $klientet->telefon_kontakt)" required />
                                <x-input-error :messages="$errors->get('telefon_kontakt')" class="mt-2" />
                            </div>

                            <!-- Adresa e Faturimit -->
                            <div>
                                <x-input-label for="adresa_faktura" :value="__('Adresa e Faturimit')" />
                                <x-text-input id="adresa_faktura" class="block mt-1 w-full" type="text" name="adresa_faktura" :value="old('adresa_faktura', $klientet->adresa_faktura)" required />
                                <x-input-error :messages="$errors->get('adresa_faktura')" class="mt-2" />
                            </div>

                            <!-- Qyteti -->
                            <div>
                                <x-input-label for="qyteti" :value="__('Qyteti')" />
                                <x-text-input id="qyteti" class="block mt-1 w-full" type="text" name="qyteti" :value="old('qyteti', $klientet->qyteti)" required />
                                <x-input-error :messages="$errors->get('qyteti')" class="mt-2" />
                            </div>

                            <!-- Kodi Postar -->
                            <div>
                                <x-input-label for="kodi_postal" :value="__('Kodi Postar')" />
                                <x-text-input id="kodi_postal" class="block mt-1 w-full" type="text" name="kodi_postal" :value="old('kodi_postal', $klientet->kodi_postal)" required />
                                <x-input-error :messages="$errors->get('kodi_postal')" class="mt-2" />
                            </div>

                            <!-- Shteti -->
                            <div class="md:col-span-2">
                                <x-input-label for="shteti" :value="__('Shteti')" />
                                <x-text-input id="shteti" class="block mt-1 w-full" type="text" name="shteti" :value="old('shteti', $klientet->shteti)" required />
                                <x-input-error :messages="$errors->get('shteti')" class="mt-2" />
                            </div>

                            <!-- Shenime -->
                            <div class="md:col-span-2">
                                <x-input-label for="shenime" :value="__('Shënime')" />
                                <textarea id="shenime" name="shenime" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('shenime', $klientet->shenime) }}</textarea>
                                <x-input-error :messages="$errors->get('shenime')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="ml-4">
                                {{ __('Përditëso Klientin') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
