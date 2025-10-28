<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Shto Staf të Ri') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('stafi.store') }}">
                        @csrf

                        <!-- Emri -->
                        <div>
                            <x-input-label for="emri" :value="__('Emri')" />
                            <x-text-input id="emri" class="block mt-1 w-full" type="text" name="emri" :value="old('emri')" required autofocus />
                            <x-input-error :messages="$errors->get('emri')" class="mt-2" />
                        </div>

                        <!-- Mbiemri -->
                        <div class="mt-4">
                            <x-input-label for="mbiemri" :value="__('Mbiemri')" />
                            <x-text-input id="mbiemri" class="block mt-1 w-full" type="text" name="mbiemri" :value="old('mbiemri')" required />
                            <x-input-error :messages="$errors->get('mbiemri')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Roli -->
                        <div class="mt-4">
                            <x-input-label for="rol_id" :value="__('Roli')" />
                            <select id="rol_id" name="rol_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach(\App\Models\Rolet::all() as $rol)
                                    <option value="{{ $rol->rol_id }}" {{ old('rol_id') == $rol->rol_id ? 'selected' : '' }}>{{ ucfirst($rol->emri_rolit) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('rol_id')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Fjalëkalimi')" />
                            <x-text-input id="password" class="block mt-1 w-full"
                                            type="password"
                                            name="password"
                                            required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Konfirmo Fjalëkalimin')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                            type="password"
                                            name="password_confirmation" required />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('stafi.index') }}">
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
