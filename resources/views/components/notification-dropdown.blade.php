@props(['unreadCount' => 0])

<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" type="button" class="relative p-3 md:p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 transition duration-150 ease-in-out touch-manipulation min-h-[44px] min-w-[44px] flex items-center justify-center">
        <svg class="h-8 w-8 md:h-6 md:w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                {{ $unreadCount }}
            </span>
        @endif
        <span class="sr-only">Njoftimet</span>
    </button>

    <div x-show="open" 
         x-cloak
         @click.away="open = false"
         @keydown.escape.window="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-full sm:w-96 bg-white rounded-lg shadow-xl py-2 z-50">
        <div class="max-h-[70vh] overflow-y-auto overscroll-contain">
            <h3 class="px-4 py-2 text-lg font-medium text-gray-900 border-b border-gray-100">Njoftimet</h3>
            @forelse(Auth::user()->njoftimet()->latest('data_krijimit')->take(5)->get() as $njoftim)
                <a href="{{ route('njoftimet.markAsRead', $njoftim->njoftim_id) }}" 
                   class="block px-4 py-4 hover:bg-gray-50 active:bg-gray-100 transition ease-in-out duration-150 {{ !$njoftim->lexuar ? 'bg-blue-50' : '' }} border-b border-gray-100 last:border-b-0">
                    <div class="flex items-center">
                        @if(!$njoftim->lexuar)
                            <span class="flex h-3 w-3 relative mr-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                            </span>
                        @else
                            <span class="h-3 w-3 mr-4"></span>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-base text-gray-900 truncate">
                                {{ $njoftim->mesazhi }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $njoftim->data_krijimit->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-4 py-6 text-base text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="mt-2">Nuk keni asnjë njoftim</p>
                </div>
            @endforelse
        </div>
        
        <div class="border-t border-gray-100">
            <div class="flex">
                <a href="{{ route('njoftimet.index') }}" class="flex-1 block px-4 py-4 text-center text-base font-medium text-blue-600 hover:bg-blue-50 active:bg-blue-100 transition-colors">
                    <span class="flex items-center justify-center">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Shiko të gjitha
                    </span>
                </a>
                <form action="{{ route('njoftimet.markAllAsRead') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full block px-4 py-4 text-center text-base font-medium text-blue-600 hover:bg-blue-50 active:bg-blue-100 transition-colors border-l border-gray-100">
                        <span class="flex items-center justify-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Shëno të lexuara
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
