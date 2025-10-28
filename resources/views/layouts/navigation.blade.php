<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 touch-manipulation sticky top-0 z-50 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="block">
                        <img src="{{ asset('img/logo.png') }}" alt="Coli Decor Logo" class="block h-9 w-auto">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-3 sm:-my-px sm:ml-6 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('projektet.index')" :active="request()->routeIs('projektet.*')" class="block">
                        {{ __('Projektet') }}
                    </x-nav-link>
                    <x-nav-link :href="route('klientet.index')" :active="request()->routeIs('klientet.*')" class="block">
                        {{ __('Klientët') }}
                    </x-nav-link>
                    <x-nav-link :href="route('statuset.index')" :active="request()->routeIs('statuset.*')" class="block">
                        {{ __('Statuset') }}
                    </x-nav-link>
                    <x-nav-link :href="route('materialet.index')" :active="request()->routeIs('materialet.index')" class="block">
                        {{ __('Materialet') }}
                    </x-nav-link>
                    @if(auth()->check() && auth()->user()->hasRole('administrator'))
                        <x-nav-link :href="route('stafi.index')" :active="request()->routeIs('stafi.index')" class="block">
                            {{ __('Stafi') }}
                        </x-nav-link>
                    @endif
                    <x-nav-link :href="route('fazat-projekti.index')" :active="request()->routeIs('fazat-projekti.index')" class="block">
                        {{ __('Fazat e Projektit') }}
                    </x-nav-link>
                    <x-nav-link :href="route('raportet.index')" :active="request()->routeIs('raportet.*')" class="block">
                        {{ __('Raportet') }}
                    </x-nav-link>
                    <x-nav-link :href="route('cutting-optimization.index')" :active="request()->routeIs('cutting-optimization.*')" class="block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                        {{ __('Optimizimi') }}
                    </x-nav-link>
                    {{-- Moduli i planifikimit të rafteve u hoq --}}
                    @can('view-ditar')
                    <x-nav-link :href="route('ditar.index')" :active="request()->routeIs('ditar.index')" class="block;">
                        {{ __('Ditari') }}
                    </x-nav-link>
                    @endcan

                    <!-- Notifications Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <x-notification-dropdown :unreadCount="$unreadNotificationsCount ?? 0" />
                    </div>
                </div>
            </div>

            <!-- Empty space for navbar balance -->
            <div></div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Fixed Corner Button -->
    <div class="fixed top-4 right-4 z-[9999]">
        <!-- User Dropdown -->
        <div class="relative">
            <x-dropdown align="right" width="50">
                <x-slot name="trigger">
                    <button class="h-12 w-12 rounded-full bg-blue-600 flex items-center justify-center hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
                        <span class="text-sm font-medium text-white">{{ substr(Auth::user()->name ?? Auth::user()->emri ?? 'U', 0, 1) }}</span>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden touch-scroll">
        <div class="pt-2 pb-3 px-4 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="touch-target touch-ripple touch-active flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('projektet.index')" :active="request()->routeIs('projektet.*')" class="touch-target touch-ripple touch-active">
                {{ __('Projektet') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('klientet.index')" :active="request()->routeIs('klientet.*')" class="touch-target touch-ripple touch-active">
                {{ __('Klientët') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('statuset.index')" :active="request()->routeIs('statuset.*')" class="touch-target touch-ripple touch-active">
                {{ __('Statuset') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('materialet.index')" :active="request()->routeIs('materialet.index')" class="touch-target touch-ripple touch-active">
                {{ __('Materialet') }}
            </x-responsive-nav-link>
            @if(auth()->check() && auth()->user()->hasRole('administrator'))
                <x-responsive-nav-link :href="route('stafi.index')" :active="request()->routeIs('stafi.index')" class="touch-target touch-ripple touch-active">
                    {{ __('Stafi') }}
                </x-responsive-nav-link>
            @endif
            <x-responsive-nav-link :href="route('fazat-projekti.index')" :active="request()->routeIs('fazat-projekti.index')" class="touch-target touch-ripple touch-active">
                {{ __('Fazat e Projektit') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('raportet.index')" :active="request()->routeIs('raportet.*')" class="touch-target touch-ripple touch-active">
                {{ __('Raportet') }}
            </x-responsive-nav-link>
            {{-- Moduli i planifikimit të rafteve u hoq --}}
            @can('view-ditar')
            <x-responsive-nav-link :href="route('ditar.index')" :active="request()->routeIs('ditar.index')" class="touch-target touch-ripple touch-active">
                {{ __('Ditari') }}
            </x-responsive-nav-link>
            @endcan
            <x-responsive-nav-link :href="route('njoftimet.index')" :active="request()->routeIs('njoftimet.*')" class="touch-target touch-ripple touch-active flex items-center justify-between">
                <span>{{ __('Njoftimet') }}</span>
                @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-blue-600 rounded-full ml-2">{{ $unreadNotificationsCount }}</span>
                @endif
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4 py-2">
                <div class="flex items-center">
                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                        <span class="text-base font-medium text-gray-600">{{ substr(Auth::user()->emri, 0, 1) }}</span>
                    </div>
                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->emri }} {{ Auth::user()->mbiemri }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="touch-target touch-ripple touch-active">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="touch-target touch-ripple touch-active">
                        <span class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            {{ __('Log Out') }}
                        </span>
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
