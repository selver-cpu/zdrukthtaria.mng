<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Njoftimet e Mia') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="mb-6 font-medium text-base text-green-600 bg-green-50 p-4 rounded-lg flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg sm:text-xl font-medium text-gray-900">Të gjitha njoftimet</h3>
                        <form action="{{ route('njoftimet.markAllAsRead') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-3 sm:py-2 bg-blue-600 text-white text-base rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors touch-manipulation">
                                <span class="flex items-center">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Shëno të gjitha si të lexuara
                                </span>
                            </button>
                        </form>
                    </div>

                    <div class="space-y-5">
                        @forelse ($njoftimet as $njoftim)
                            <a href="{{ route('njoftimet.markAsRead', $njoftim->njoftim_id) }}" 
                               class="block p-5 rounded-lg flex items-center justify-between transition duration-150 ease-in-out shadow-sm 
                                     {{ !$njoftim->lexuar ? 'bg-blue-50 border-l-4 border-blue-400 font-medium hover:bg-blue-100 active:bg-blue-200' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 active:bg-gray-200' }}">
                                <div class="flex items-center w-full">
                                    @if(!$njoftim->lexuar)
                                        <span class="flex h-4 w-4 relative mr-5 flex-shrink-0">
                                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                          <span class="relative inline-flex rounded-full h-4 w-4 bg-blue-500"></span>
                                        </span>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 mr-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-base sm:text-lg mb-1 {{ !$njoftim->lexuar ? 'text-gray-800' : '' }}">{{ $njoftim->mesazhi }}</p>
                                        <div class="flex flex-wrap items-center gap-2 mt-2">
                                            <span class="px-3 py-1 text-sm rounded-full {{ $njoftim->lloji_njoftimit === 'system' ? 'bg-blue-100 text-blue-800' : ($njoftim->lloji_njoftimit === 'email' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($njoftim->lloji_njoftimit) }}
                                            </span>
                                            <p class="text-sm font-normal {{ !$njoftim->lexuar ? 'text-gray-600' : 'text-gray-400' }}">{{ $njoftim->data_krijimit->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <svg class="h-6 w-6 text-gray-400 ml-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-16 bg-gray-50 rounded-lg">
                                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <p class="mt-4 text-lg text-gray-500">Nuk keni asnjë njoftim për momentin.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8">
                        <div class="pagination-touch-friendly">
                            {{ $njoftimet->links() }}
                        </div>
                    </div>
                    
                    <style>
                        /* Stilizim për pagination në ekranet me prekje */
                        .pagination-touch-friendly nav[role=navigation] span, .pagination-touch-friendly nav[role=navigation] a {
                            padding: 0.75rem 1rem;
                            margin: 0 0.25rem;
                            min-width: 2.5rem;
                            min-height: 2.5rem;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            border-radius: 0.5rem;
                            font-size: 1rem;
                            touch-action: manipulation;
                        }
                        
                        .pagination-touch-friendly svg {
                            width: 1.25rem;
                            height: 1.25rem;
                        }
                    </style>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
