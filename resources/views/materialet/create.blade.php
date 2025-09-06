<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Krijo Material të Ri') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('materialet.store') }}">
                        @csrf

                        <!-- Emri -->
                        <div>
                            <x-input-label for="emri" :value="__('Emri i Materialit')" />
                            <x-text-input id="emri" class="block mt-1 w-full" type="text" name="emri" :value="old('emri')" required autofocus />
                            <x-input-error :messages="$errors->get('emri')" class="mt-2" />
                        </div>

                        <!-- Njësia Matëse -->
                        <div class="mt-4">
                            <x-input-label for="njesia_matese" :value="__('Njësia Matëse (p.sh., m2, ml, copë)')" />
                            <x-text-input id="njesia_matese" class="block mt-1 w-full" type="text" name="njesia_matese" :value="old('njesia_matese')" required />
                            <x-input-error :messages="$errors->get('njesia_matese')" class="mt-2" />
                        </div>

                        <!-- Përshkrimi -->
                        <div class="mt-4">
                            <x-input-label for="pershkrimi" :value="__('Përshkrimi (Opsional)')" />
                            <textarea id="pershkrimi" name="pershkrimi" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('pershkrimi') }}</textarea>
                            <x-input-error :messages="$errors->get('pershkrimi')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Ruaj') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
