<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Paneli Kryesor') }}
        </h2>
    </x-slot>
    
    @php
        // Ensure variables are always defined
        $stats = $stats ?? [];
        $role = $role ?? 'undefined';
        $projektet_e_fundit = $projektet_e_fundit ?? collect();
        $projektet_e_mia = $projektet_e_mia ?? collect();
        $detyrat_e_ardhshme = $detyrat_e_ardhshme ?? collect();
        
        // Helper function to get status color
        function getStatusColor($status) {
            if (!$status) return 'bg-gray-100 text-gray-800';
            
            return match(strtolower($status)) {
                'në proces' => 'bg-blue-100 text-blue-800',
                'përfunduar' => 'bg-green-100 text-green-800',
                'në pritje' => 'bg-yellow-100 text-yellow-800',
                'anuluar' => 'bg-red-100 text-red-800',
                default => 'bg-gray-100 text-gray-800'
            };
        }
    @endphp

    <style>
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .tab-button {
        transition: all 0.3s ease;
    }
    .tab-button:hover {
        transform: translateY(-2px);
    }
    .status-badge {
        padding: 0.35rem 0.65rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 0.25rem;
    }
</style>

<div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Global Search Bar - Touch Optimized -->
            <div class="mb-6">
                <div class="relative">
                    <input type="text" id="global-search" placeholder="Kërko projekte, klientë, detyra..." 
                           class="w-full h-14 pl-14 pr-12 text-lg bg-white border-2 border-gray-200 rounded-2xl focus:border-blue-500 focus:ring-0 transition-all duration-200 touch-manipulation shadow-sm">
                    <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <div class="absolute right-3 top-1/2 transform -translate-y-1/2 flex items-center space-x-1">
                        <button id="clear-search" class="p-2 text-gray-400 hover:text-gray-600 transition-colors touch-manipulation" title="Pastro">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <button id="voice-search" class="p-2 text-gray-400 hover:text-blue-500 transition-colors touch-manipulation" title="Kërkim me zë">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <!-- Search Results Dropdown -->
                <div id="search-results" class="hidden absolute z-50 w-full mt-2 bg-white rounded-xl shadow-lg border border-gray-200 max-h-96 overflow-y-auto">
                    <div class="p-4">
                        <div class="text-sm text-gray-500 mb-2">Rezultatet e kërkimit</div>
                        <div id="search-content" class="space-y-2"></div>
                    </div>
                </div>
            </div>

            <!-- Touch-Optimized Navigation Tabs -->
            <div class="mb-6 overflow-x-auto touch-pan-x">
                <div class="flex space-x-3 p-3 bg-white rounded-2xl shadow-lg border border-gray-100">
                    <button id="tab-overview" class="tab-button active flex flex-col items-center min-h-[80px] px-6 py-4 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white border-0 flex-1 min-w-[120px] transition-all duration-300 transform active:scale-95 shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="text-base font-semibold">Përmbledhje</span>
                    </button>
                    <button id="tab-projects" class="tab-button flex flex-col items-center min-h-[80px] px-6 py-4 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200 flex-1 min-w-[120px] transition-all duration-300 transform active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span class="text-base font-semibold">Projektet</span>
                    </button>
                    <button id="tab-tasks" class="tab-button flex flex-col items-center min-h-[80px] px-6 py-4 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200 flex-1 min-w-[120px] transition-all duration-300 transform active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span class="text-base font-semibold">Detyrat</span>
                    </button>
                    <button id="tab-stats" class="tab-button flex flex-col items-center min-h-[80px] px-6 py-4 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200 flex-1 min-w-[120px] transition-all duration-300 transform active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="text-base font-semibold">Statistikat</span>
                    </button>
                </div>
            </div>

            <!-- Përmbajtja e Tab-eve -->
            <div id="content-overview" class="tab-content block">
                <x-role-dashboard :role="$role ?? 'administrator'" :stats="$stats ?? []" />
            </div>

            <!-- Recent Projects - Touch Optimized Cards -->
            <div class="bg-white overflow-hidden shadow-lg rounded-2xl mb-6 border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">Projektet e Fundit</h3>
                        <div class="flex space-x-2">
                            <button id="table-view" class="p-3 rounded-xl bg-blue-500 text-white touch-manipulation transition-all active:scale-95">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                            </button>
                            <button id="card-view" class="p-3 rounded-xl bg-gray-200 text-gray-600 touch-manipulation transition-all active:scale-95">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Table View -->
                    <div id="projects-table" class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="text-left py-6 px-6 text-lg font-semibold text-gray-700 cursor-pointer hover:bg-gray-200 transition-colors touch-manipulation" data-sort="name">
                                        <div class="flex items-center space-x-2">
                                            <span>Emri Projektit</span>
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                            </svg>
                                        </div>
                                    </th>
                                    <th class="text-left py-6 px-6 text-lg font-semibold text-gray-700 cursor-pointer hover:bg-gray-200 transition-colors touch-manipulation" data-sort="client">
                                        <div class="flex items-center space-x-2">
                                            <span>Klienti</span>
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                            </svg>
                                        </div>
                                    </th>
                                    <th class="text-left py-6 px-6 text-lg font-semibold text-gray-700 cursor-pointer hover:bg-gray-200 transition-colors touch-manipulation" data-sort="status">
                                        <div class="flex items-center space-x-2">
                                            <span>Statusi</span>
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                            </svg>
                                        </div>
                                    </th>
                                    <th class="text-left py-6 px-6 text-lg font-semibold text-gray-700 cursor-pointer hover:bg-gray-200 transition-colors touch-manipulation" data-sort="date">
                                        <div class="flex items-center space-x-2">
                                            <span>Data</span>
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                            </svg>
                                        </div>
                                    </th>
                                    <th class="text-center py-6 px-6 text-lg font-semibold text-gray-700">Veprime</th>
                                </tr>
                            </thead>
                            <tbody id="projects-tbody">
                                @forelse ($projektet_e_fundit as $projekt)
                                    @php
                                        $projekt = $projekt ?? (object)['emri_projektit' => 'Projekt i panjohur'];
                                        $klient = $projekt->klient ?? (object)['emri' => 'Klient i panjohur'];
                                        $status = $projekt->statusi_projektit ?? (object)[
                                            'emri_statusit' => 'N/A',
                                            'ngjyra_statusit' => '#808080'
                                        ];
                                    @endphp
                                    <tr class="border-b border-gray-100 hover:bg-blue-50 touch-manipulation transition-all duration-200 transform hover:scale-[1.01]">
                                        <td class="py-6 px-6">
                                            <a href="{{ route('projektet.show', $projekt) }}" class="text-blue-600 hover:text-blue-800 text-lg font-semibold transition-colors">
                                                {{ $projekt->emri_projektit }}
                                            </a>
                                        </td>
                                        <td class="py-6 px-6 text-lg text-gray-700">{{ $klient->emri ?? 'Klient i panjohur' }}</td>
                                        <td class="py-6 px-6">
                                            <span class="px-4 py-2 inline-flex text-base leading-5 font-semibold rounded-full {{ getStatusColor($status->emri_statusit) }} shadow-sm">
                                                {{ $status->emri_statusit }}
                                            </span>
                                        </td>
                                        <td class="py-6 px-6 text-lg text-gray-700">{{ $projekt->created_at ? $projekt->created_at->format('d/m/Y') : 'E pacaktuar' }}</td>
                                        <td class="py-6 px-6 text-center">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('projektet.show', $projekt) }}" class="p-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors touch-manipulation transform active:scale-95">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('projektet.edit', $projekt) }}" class="p-3 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-colors touch-manipulation transform active:scale-95">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-12 px-6 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                <p class="text-xl text-gray-500 mb-4">Nuk ka projekte të fundit</p>
                                                <a href="{{ route('projektet.create') }}" class="px-6 py-3 bg-blue-500 text-white rounded-xl font-semibold hover:bg-blue-600 transition-colors touch-manipulation transform active:scale-95">
                                                    Shto Projekt të Ri
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Card View (Hidden by default) -->
                    <div id="projects-cards" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($projektet_e_fundit as $projekt)
                            @php
                                $projekt = $projekt ?? (object)['emri_projektit' => 'Projekt i panjohur'];
                                $klient = $projekt->klient ?? (object)['emri' => 'Klient i panjohur'];
                                $status = $projekt->statusi_projektit ?? (object)[
                                    'emri_statusit' => 'N/A',
                                    'ngjyra_statusit' => '#808080'
                                ];
                            @endphp
                            <div class="bg-white border border-gray-200 rounded-2xl p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 touch-manipulation">
                                <div class="flex items-start justify-between mb-4">
                                    <h4 class="text-xl font-bold text-gray-800 line-clamp-2">{{ $projekt->emri_projektit }}</h4>
                                    <span class="px-3 py-1 inline-flex text-base leading-5 font-semibold rounded-full {{ getStatusColor($status->emri_statusit) }} ml-2 flex-shrink-0">
                                        {{ $status->emri_statusit }}
                                    </span>
                                </div>
                                <div class="space-y-3 mb-6">
                                    <div class="flex items-center text-gray-600">
                                        <svg class="h-5 w-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span class="text-base">{{ $klient->emri ?? 'Klient i panjohur' }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <svg class="h-5 w-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-2 2m8-2l2 2m-2-2v6a2 2 0 01-2 2H8a2 2 0 01-2-2v-6"></path>
                                        </svg>
                                        <span class="text-base">{{ $projekt->created_at ? $projekt->created_at->format('d/m/Y') : 'E pacaktuar' }}</span>
                                    </div>
                                </div>
                                <div class="flex space-x-3">
                                    <a href="{{ route('projektet.show', $projekt) }}" class="flex-1 py-3 px-4 bg-blue-500 text-white text-center rounded-xl font-semibold hover:bg-blue-600 transition-colors touch-manipulation transform active:scale-95">
                                        Shiko
                                    </a>
                                    <a href="{{ route('projektet.edit', $projekt) }}" class="flex-1 py-3 px-4 bg-green-500 text-white text-center rounded-xl font-semibold hover:bg-green-600 transition-colors touch-manipulation transform active:scale-95">
                                        Ndrysho
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <svg class="h-16 w-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-xl text-gray-500 mb-4">Nuk ka projekte të fundit</p>
                                <a href="{{ route('projektet.create') }}" class="px-6 py-3 bg-blue-500 text-white rounded-xl font-semibold hover:bg-blue-600 transition-colors touch-manipulation transform active:scale-95">
                                    Shto Projekt të Ri
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Projects Tab Content -->
            <div id="content-projects" class="tab-content hidden">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-2xl font-semibold">Projektet e Mia</h3>
                            <a href="{{ route('projektet.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white py-3 px-6 rounded-lg text-lg font-medium flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Shiko të Gjitha
                            </a>
                        </div>
                        
                        @if(!$projektet_e_mia->isEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emri Projektit</th>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klienti</th>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statusi</th>
                                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roli im</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($projektet_e_mia as $projekt)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-4 px-4 text-sm font-medium text-gray-900"><a href="{{ route('projektet.show', $projekt) }}" class="text-blue-600 hover:text-blue-800">{{ $projekt->emri_projektit }}</a></td>
                                        <td class="py-4 px-4 text-sm text-gray-500">{{ optional($projekt->klient)->emri ?? 'N/A' }}</td>
                                        <td class="py-4 px-4 text-sm text-gray-500">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ getStatusColor(optional($projekt->statusi_projektit)->emri_statusit) }}">
                                                {{ optional($projekt->statusi_projektit)->emri_statusit ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-sm text-gray-500">
                                            @if(isset($projekt->mjeshtri_caktuar_id) && $projekt->mjeshtri_caktuar_id == Auth::id())
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Mjeshtër</span>
                                            @elseif(isset($projekt->montuesi_caktuar_id) && $projekt->montuesi_caktuar_id == Auth::id())
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">Montues</span>
                                            @elseif(Auth::user()->rol && in_array(Auth::user()->rol->emri_rolit, ['administrator', 'menaxher']))
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ Auth::user()->rol->emri_rolit }}</span>
                                            @else
                                                <span class="text-gray-500">Pa rol të caktuar</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        </div>
                        @else
                        <div class="bg-white p-8 rounded-lg border text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-xl text-gray-600">Nuk keni projekte të caktuara.</p>
                            @if(auth()->user()->rol->emri_rolit === 'administrator' || auth()->user()->rol->emri_rolit === 'menaxher')
                                <a href="{{ route('projektet.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                    Shto Projekt të Ri
                                </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Tasks Tab Content -->
            <div id="content-tasks" class="tab-content hidden">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center justify-between mb-4">
                                <h4 class="text-xl font-semibold text-gray-800">Detyrat e Ardhshme</h4>
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ isset($detyrat_e_ardhshme) && is_countable($detyrat_e_ardhshme) ? count($detyrat_e_ardhshme) : 0 }} Detyra
                                </span>
                            </div>
                        
                        @if(!$projektet_e_mia->isEmpty())
                        <div class="space-y-6">
                            @foreach($projektet_e_mia as $projekt)
                                @php
                                    $projekt = $projekt ?? (object)[
                                        'emri_projektit' => 'Projekt i panjohur',
                                        'projekt_id' => 0,
                                        'klient' => (object)['emri' => 'Klient i panjohur'],
                                        'statusi_projektit' => (object)['emri_statusit' => 'N/A'],
                                        'fazat' => collect(),
                                        'data_fillimit_parashikuar' => null,
                                        'data_perfundimit_parashikuar' => null
                                    ];
                                @endphp
                            <div class="bg-white border rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow touch-manipulation p-5">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-xl font-semibold text-gray-800">{{ $projekt->emri_projektit ?? 'Projekt i panjohur' }}</h4>
                                    <span class="px-3 py-1 inline-flex text-base leading-5 font-semibold rounded-full {{ match($projekt->statusi_projektit->emri_statusit) {
                                        'Përfunduar' => 'bg-green-100 text-green-800',
                                        'Në Proces' => 'bg-blue-100 text-blue-800',
                                        'Në Pritje' => 'bg-yellow-100 text-yellow-800',
                                        'Anuluar' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    } }}">{{ $projekt->statusi_projektit->emri_statusit }}</span>
                                </div>
                                
                                <div class="mb-4">
                                    <h5 class="text-lg font-medium mb-3">Fazat e Projektit:</h5>
                                    <div class="space-y-3">
                                        @forelse($projekt->fazat as $faza)
                                        <div class="flex items-start p-4 border border-gray-100 rounded-lg hover:bg-blue-50 transition-all duration-200 transform hover:-translate-x-1 hover:shadow-sm">
                                            <div class="mr-3">
                                                @if($faza->pivot->statusi_fazes === 'perfunduar')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-lg font-medium">{{ $faza->emri_fazes }}</p>
                                                <p class="text-gray-600">{{ $faza->pershkrimi }}</p>
                                            </div>
                                            <div>
                                                <a href="{{ route('projektet.show', $projekt) }}#fazat" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg text-base font-medium">
                                                    Shiko
                                                </a>
                                            </div>
                                        </div>
                                        @empty
                                        <p class="text-gray-500 p-3">Nuk ka faza të caktuara për këtë projekt.</p>
                                        @endforelse
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <a href="{{ route('projektet.show', $projekt) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 px-6 rounded-lg text-base font-medium">
                                        Shiko të Gjitha Detajet
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="bg-white p-8 rounded-lg border text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <p class="text-xl text-gray-600">Nuk keni detyra aktive.</p>
                            @if(auth()->user()->rol->emri_rolit === 'administrator' || auth()->user()->rol->emri_rolit === 'menaxher')
                                <a href="{{ route('projektet.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                    Shto Projekt të Ri
                                </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Stats Tab Content -->
            <div id="content-stats" class="tab-content hidden">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-2xl font-semibold mb-6">Statistikat e Projekteve</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                            <!-- Projektet Aktive -->
                            <div class="bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl shadow-sm border border-blue-100 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-sm font-medium text-blue-700 uppercase tracking-wider">Projekte Aktive</h4>
                                    <div class="p-2 bg-blue-100 rounded-lg">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-3xl font-bold text-gray-800">{{ $stats['projektet_aktive'] ?? 0 }}</div>
                                <p class="mt-2 text-sm text-gray-500">Projekte të përfunduara këtë muaj: {{ $stats['projektet_muaji'] ?? 0 }}</p>
                            </div>

                            <!-- Detyra Për Sot -->
                            <div class="bg-gradient-to-br from-purple-50 to-white p-6 rounded-xl shadow-sm border border-purple-100 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-sm font-medium text-purple-700 uppercase tracking-wider">Detyra për Sot</h4>
                                    <div class="p-2 bg-purple-100 rounded-lg">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex items-baseline">
                                    <span class="text-3xl font-bold text-gray-900">{{ $stats['detyrat_sot'] ?? 0 }}</span>
                                    <span class="ml-2 text-sm font-medium text-green-600">+{{ rand(1, 10) }}%</span>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Në pritje: {{ $stats['detyrat_ne_pritje'] ?? 0 }}</p>
                            </div>

                            <!-- Klientë të Rinj -->
                            <div class="bg-gradient-to-br from-green-50 to-white p-6 rounded-xl shadow-sm border border-green-100 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-sm font-medium text-green-700 uppercase tracking-wider">Klientë të Rinj</h4>
                                    <div class="p-2 bg-green-100 rounded-lg">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex items-baseline">
                                    <span class="text-3xl font-bold text-gray-900">{{ $stats['klientet_e_rij'] ?? 0 }}</span>
                                    <span class="ml-2 text-sm font-medium text-green-600">+{{ rand(5, 20) }}%</span>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Gjithsej: {{ $stats['klientet_gjithsej'] ?? 0 }}</p>
                            </div>

                            <!-- Të Ardhurat -->
                            <div class="bg-gradient-to-br from-amber-50 to-white p-6 rounded-xl shadow-sm border border-amber-100 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-sm font-medium text-amber-700 uppercase tracking-wider">Të Ardhurat</h4>
                                    <div class="p-2 bg-amber-100 rounded-lg">
                                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex items-baseline">
                                    <span class="text-3xl font-bold text-gray-900">{{ number_format($stats['te_ardhurat'] ?? 0, 0, ',', '.') }}$</span>
                                    <span class="ml-2 text-sm font-medium text-green-600">+{{ rand(5, 15) }}%</span>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Këtë muaj</p>
                            </div>
                        </div>
                            <div class="bg-white p-6 rounded-lg shadow-md border touch-manipulation">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-xl font-semibold text-gray-800">Projektet e Mia sipas Statusit</h4>
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        Total: {{ isset($stats['projektet_sipas_statusit']) ? array_sum($stats['projektet_sipas_statusit']) : 0 }}
                                    </span>
                                </div>
                                <div class="space-y-4">
                                    @php
                                        $statuset = App\Models\StatusetProjektit::all();
                                        $user_id = Auth::id();
                                        // Numëro projektet pa status duke përdorur kolonën e saktë status_id
                                        $projektet_pa_status = App\Models\Projektet::where(function($query) use ($user_id) {
                                            $query->where('mjeshtri_caktuar_id', $user_id)
                                                  ->orWhere('montuesi_caktuar_id', $user_id);
                                        })->whereNull('status_id')->count();
                                    @endphp
                                    
                                    @php
                                        $maxStatus = 0;
                                        if (!empty($stats['projektet_sipas_statusit']) && is_array($stats['projektet_sipas_statusit'])) {
                                            $filtered = array_filter($stats['projektet_sipas_statusit'], fn($v) => is_numeric($v));
                                            $maxStatus = !empty($filtered) ? max($filtered) : 0;
                                        }
                                        $width_pa_status = $maxStatus > 0 ? min(100, round(($projektet_pa_status / $maxStatus) * 100)) : 0;
                                    @endphp
                                    @if($projektet_pa_status > 0)
                                    <div class="flex items-center mb-4">
                                        <div class="w-full bg-gray-200 rounded-full h-8">
                                            @php
                                                $safe_width = isset($width_pa_status) && is_numeric($width_pa_status) ? $width_pa_status : 0;
                                            @endphp
                                            <div class="h-2.5 rounded-full transition-all duration-500 ease-out bg-gray-500" style="width: <?php echo $safe_width; ?>%;"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6 col-xl-3">
                                                <div class="info-box shadow-sm">
                                                    <span class="info-box-icon bg-primary"><i class="fas fa-project-diagram"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text font-weight-bold">Projekte Aktive</span>
                                                        <span class="info-box-number display-6">{{ $totalActiveProjects }}</span>
                                                        <div class="progress mt-2" style="height: 3px;">
                                                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @foreach($statuset as $status)
                                        @php
                                            $count = App\Models\Projektet::where(function($query) use ($user_id) {
                                                $query->where('mjeshtri_caktuar_id', $user_id)
                                                      ->orWhere('montuesi_caktuar_id', $user_id);
                                            })->where('status_id', $status->id)->count();

                                        @endphp
                                        
                                        @if($count > 0)
                                        <div class="flex items-center">
                                            <div class="w-full bg-gray-200 rounded-full h-8">
                                                @php
                                                    $safe_count_width = isset($count) && is_numeric($count) ? min(100, $count * 10) : 0;
                                                    $safe_bg_color = isset($status->ngjyra_statusit) ? $status->ngjyra_statusit : '#808080';
                                                @endphp
                                                <div class="h-8 rounded-full" 
                                                     style="width: <?php echo $safe_count_width; ?>%; background-color: <?php echo $safe_bg_color; ?>;"></div>
                                            </div>
                                            <div class="ml-4 min-w-[100px]">
                                                @php
                                                    $safe_text_color = isset($status->ngjyra_statusit) ? $status->ngjyra_statusit : '#808080';
                                                    $safe_status_name = isset($status->emri_statusit) ? $status->emri_statusit : 'Status';
                                                @endphp
                                                <span class="text-lg font-medium" 
                                                      style="color: <?php echo $safe_text_color; ?>;"><?php echo $safe_status_name; ?></span>
                                                <span class="text-lg font-bold ml-2">{{ $count }}</span>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Seksioni i aktivitetit u hoq përkohësisht derisa të instalohet/konfigurohet paketa spatie/laravel-activitylog -->
                            <div class="bg-white p-6 rounded-lg shadow-md border touch-manipulation">
                                <h4 class="text-xl font-semibold mb-4">Aktiviteti im i Fundit</h4>
                                <div class="space-y-4">
                                    <div class="p-4 text-center text-gray-500">
                                        Nuk ka aktivitet të regjistruar.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>


    {{-- @vite(['resources/js/dashboard-enhancements.js']) --}}
    <script>
        // Tab navigation functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-gradient-to-br', 'from-blue-500', 'to-blue-600', 'text-white', 'border-0', 'shadow-md');
                        btn.classList.add('bg-gray-50', 'hover:bg-gray-100', 'text-gray-700', 'border', 'border-gray-200');
                    });
                    
                    // Add active class to clicked button
                    this.classList.add('active', 'bg-gradient-to-br', 'from-blue-300', 'to-blue-600', 'text-white', 'border-0', 'shadow-md');
                    this.classList.remove('bg-gray-50', 'hover:bg-gray-100', 'text-gray-700', 'border', 'border-gray-200');
                    
                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                        content.classList.remove('block');
                    });
                    
                    // Show corresponding tab content
                    const tabId = this.id.replace('tab-', 'content-');
                    const contentElement = document.getElementById(tabId);
                    if (contentElement) {
                        contentElement.classList.remove('hidden');
                        contentElement.classList.add('block');
                        
                        // Initialize charts if stats tab is selected
                        if (tabId === 'content-stats') {
                            initializeCharts();
                        }
                    } else {
                        console.error('Tab content not found for ID: ' + tabId);
                    }
                });
            });
            
            // Initialize charts function
            function initializeCharts() {
                // Project Status Chart
                const statusCtx = document.getElementById('projectStatusChart');
                if (statusCtx) {
                    // Destroy existing chart if it exists
                    if (window.projectStatusChart instanceof Chart) {
                        window.projectStatusChart.destroy();
                    }
                    
                    // Sample data - replace with actual data from your controller
                    const statusData = {
                        labels: ['Në Proces', 'Përfunduar', 'Në Pritje', 'Anuluar', 'Në Pauzë'],
                        datasets: [{
                            label: 'Numri i Projekteve',
                            data: [
                                <?php echo isset($stats['projektet_aktive']) && is_numeric($stats['projektet_aktive']) ? $stats['projektet_aktive'] : 0; ?>,
                                <?php echo isset($stats['projektet_perfunduara_muaji']) && is_numeric($stats['projektet_perfunduara_muaji']) ? $stats['projektet_perfunduara_muaji'] : 0; ?>,
                                <?php echo isset($stats['projektet_ne_pritje']) && is_numeric($stats['projektet_ne_pritje']) ? $stats['projektet_ne_pritje'] : 0; ?>,
                                <?php echo isset($stats['projektet_anuluar']) && is_numeric($stats['projektet_anuluar']) ? $stats['projektet_anuluar'] : 0; ?>,
                                <?php echo isset($stats['projektet_ne_pauze']) && is_numeric($stats['projektet_ne_pauze']) ? $stats['projektet_ne_pauze'] : 0; ?>
                            ],
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.6)',
                                'rgba(75, 192, 192, 0.6)',
                                'rgba(255, 206, 86, 0.6)',
                                'rgba(255, 99, 132, 0.6)',
                                'rgba(153, 102, 255, 0.6)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(153, 102, 255, 1)'
                            ],
                            borderWidth: 1
                        }]
                    };
                    
                    window.projectStatusChart = new Chart(statusCtx, {
                        type: 'doughnut',
                        data: statusData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                }
                            }
                        }
                    });
                }
                
                // Monthly Projects Chart
                const monthlyCtx = document.getElementById('projectMonthlyChart');
                if (monthlyCtx) {
                    // Destroy existing chart if it exists
                    if (window.projectMonthlyChart instanceof Chart) {
                        window.projectMonthlyChart.destroy();
                    }
                    
                    // Get last 6 months
                    const months = [];
                    const currentDate = new Date();
                    for (let i = 5; i >= 0; i--) {
                        const month = new Date(currentDate.getFullYear(), currentDate.getMonth() - i, 1);
                        months.push(month.toLocaleString('sq', { month: 'long' }));
                    }
                    
                    // Sample data - replace with actual data from your controller
                    const monthlyData = {
                        labels: months,
                        datasets: [{
                            label: 'Projekte të Reja',
                            data: [4, 6, 8, 5, 7, <?php echo isset($stats['projektet_muaji']) && is_numeric($stats['projektet_muaji']) ? $stats['projektet_muaji'] : 9; ?>],
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2,
                            tension: 0.3
                        }, {
                            label: 'Projekte të Përfunduara',
                            data: [3, 5, 7, 4, 6, <?php echo isset($stats['projektet_perfunduara_muaji']) && is_numeric($stats['projektet_perfunduara_muaji']) ? $stats['projektet_perfunduara_muaji'] : 8; ?>],
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            tension: 0.3
                        }]
                    };
                    
                    window.projectMonthlyChart = new Chart(monthlyCtx, {
                        type: 'line',
                        data: monthlyData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                }
            }
            
            // Initialize charts when the page loads if stats tab is active
            if (document.getElementById('tab-stats').classList.contains('active')) {
                initializeCharts();
            }
            
        });
    </script>
</x-app-layout>
