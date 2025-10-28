<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Shto Fazë të Re') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('fazat-projekti.store') }}">
                        @csrf

                        <!-- Emri i Fazës -->
                        <div>
                            <x-input-label for="emri_fazes" :value="__('Emri i Fazës')" />
                            <x-text-input id="emri_fazes" class="block mt-1 w-full" type="text" name="emri_fazes" :value="old('emri_fazes')" required autofocus />
                            <x-input-error :messages="$errors->get('emri_fazes')" class="mt-2" />
                        </div>

                        <!-- Përshkrimi -->
                        <div class="mt-4">
                            <x-input-label for="pershkrimi" :value="__('Përshkrimi')" />
                            <textarea id="pershkrimi" name="pershkrimi" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('pershkrimi') }}</textarea>
                            <x-input-error :messages="$errors->get('pershkrimi')" class="mt-2" />
                        </div>

                        <!-- Renditja -->
                        <div class="mt-4">
                            <x-input-label for="renditja" :value="__('Renditja')" />
                            <x-text-input id="renditja" class="block mt-1 w-full" type="number" name="renditja" :value="old('renditja', 0)" required />
                            <x-input-error :messages="$errors->get('renditja')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('fazat-projekti.index') }}">
                                {{ __('Anulo') }}
                            </a>

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
