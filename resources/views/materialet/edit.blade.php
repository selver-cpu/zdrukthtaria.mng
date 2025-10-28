<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifiko Materialin') }}: {{ $materialet->emri_materialit }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('materialet.update', $materialet) }}">
                        @csrf
                        @method('PUT')

                        <!-- Emri -->
                        <div>
                            <x-input-label for="emri" :value="__('Emri i Materialit')" />
                            <x-text-input id="emri" class="block mt-1 w-full" type="text" name="emri" :value="old('emri', $materialet->emri_materialit)" required autofocus />
                            <x-input-error :messages="$errors->get('emri')" class="mt-2" />
                        </div>

                        <!-- Njësia Matëse -->
                        <div class="mt-4">
                            <x-input-label for="njesia_matese" :value="__('Njësia Matëse (p.sh., m2, ml, copë)')" />
                            <x-text-input id="njesia_matese" class="block mt-1 w-full" type="text" name="njesia_matese" :value="old('njesia_matese', $materialet->njesia_matese)" required />
                            <x-input-error :messages="$errors->get('njesia_matese')" class="mt-2" />
                        </div>

                        <!-- Përshkrimi -->
                        <div class="mt-4">
                            <x-input-label for="pershkrimi" :value="__('Përshkrimi (Opsional)')" />
                            <textarea id="pershkrimi" name="pershkrimi" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('pershkrimi', $materialet->pershkrimi) }}</textarea>
                            <x-input-error :messages="$errors->get('pershkrimi')" class="mt-2" />
                        </div>

                        <!-- Sasia në Stok dhe Minimale -->
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="sasia_stokut" :value="__('Sasia në Stok')" />
                                <x-text-input id="sasia_stokut" class="block mt-1 w-full" type="number" step="0.01" name="sasia_stokut" :value="old('sasia_stokut', $materialet->sasia_stokut)" />
                                <x-input-error :messages="$errors->get('sasia_stokut')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="sasia_minimale" :value="__('Sasia Minimale (Alert)')" />
                                <x-text-input id="sasia_minimale" class="block mt-1 w-full" type="number" step="0.01" name="sasia_minimale" :value="old('sasia_minimale', $materialet->sasia_minimale)" />
                                <x-input-error :messages="$errors->get('sasia_minimale')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Cmimi & Lokacioni -->
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="cmimi_per_njesi" :value="__('Çmimi për Njësi (Opsional)')" />
                                <x-text-input id="cmimi_per_njesi" class="block mt-1 w-full" type="number" step="0.01" name="cmimi_per_njesi" :value="old('cmimi_per_njesi', $materialet->cmimi_per_njesi)" />
                                <x-input-error :messages="$errors->get('cmimi_per_njesi')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="lokacioni" :value="__('Lokacioni në Magazinë (Opsional)')" />
                                <x-text-input id="lokacioni" class="block mt-1 w-full" type="text" name="lokacioni" :value="old('lokacioni', $materialet->lokacioni)" />
                                <x-input-error :messages="$errors->get('lokacioni')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Përditëso') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
