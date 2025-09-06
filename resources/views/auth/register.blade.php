<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Emri -->
        <div>
            <x-input-label for="emri" :value="__('Emri')" />
            <x-text-input id="emri" class="block mt-1 w-full" type="text" name="emri" :value="old('emri')" required autofocus autocomplete="given-name" />
            <x-input-error :messages="$errors->get('emri')" class="mt-2" />
        </div>

        <!-- Mbiemri -->
        <div class="mt-4">
            <x-input-label for="mbiemri" :value="__('Mbiemri')" />
            <x-text-input id="mbiemri" class="block mt-1 w-full" type="text" name="mbiemri" :value="old('mbiemri')" required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('mbiemri')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Roli -->
        <div class="mt-4">
            <x-input-label for="rol_id" :value="__('Roli')" />
            <select id="rol_id" name="rol_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="">{{ __('Zgjidh rolin') }}</option>
                <option value="4" {{ old('rol_id') == 4 ? 'selected' : '' }}>{{ __('Montues') }}</option>
            </select>
            <x-input-error :messages="$errors->get('rol_id')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
