<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dërgo Email për Projektin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Zgjidhni përdoruesin për të dërguar email</h3>
                    
                    <form action="{{ route('projektet.send-email', $projekt->projekt_id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="user-select" class="block text-sm font-medium text-gray-700 mb-2">Zgjidhni përdoruesin:</label>
                            
                            <select id="user-select" name="user_id" class="form-select block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">-- Zgjidhni një përdorues --</option>
                                
                                @if(count($admins) > 0)
                                    <optgroup label="Administratorët">
                                        @foreach($admins as $admin)
                                            <option value="{{ $admin->perdorues_id }}">{{ $admin->emri }} {{ $admin->mbiemri }} (Admin)</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                
                                @if(count($managers) > 0)
                                    <optgroup label="Menaxherët">
                                        @foreach($managers as $manager)
                                            <option value="{{ $manager->perdorues_id }}">{{ $manager->emri }} {{ $manager->mbiemri }} (Menaxher)</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                
                                @if(count($craftsmen) > 0)
                                    <optgroup label="Mjeshtrit">
                                        @foreach($craftsmen as $craftsman)
                                            <option value="{{ $craftsman->perdorues_id }}" {{ $projekt->mjeshtri_caktuar_id == $craftsman->perdorues_id ? 'selected' : '' }}>
                                                {{ $craftsman->emri }} {{ $craftsman->mbiemri }} (Mjeshtër)
                                                {{ $projekt->mjeshtri_caktuar_id == $craftsman->perdorues_id ? '- I caktuar për këtë projekt' : '' }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                
                                @if(count($installers) > 0)
                                    <optgroup label="Montuesit">
                                        @foreach($installers as $installer)
                                            <option value="{{ $installer->perdorues_id }}" {{ $projekt->montuesi_caktuar_id == $installer->perdorues_id ? 'selected' : '' }}>
                                                {{ $installer->emri }} {{ $installer->mbiemri }} (Montues)
                                                {{ $projekt->montuesi_caktuar_id == $installer->perdorues_id ? '- I caktuar për këtë projekt' : '' }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        </div>
                        
                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('projektet.show', $projekt->projekt_id) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Kthehu
                            </a>
                            
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Dërgo Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
