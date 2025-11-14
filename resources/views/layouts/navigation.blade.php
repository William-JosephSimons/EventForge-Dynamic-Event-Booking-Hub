<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @auth
                        @if(Auth::user()->isAttendee())
                            <!-- Attendee bookings link -->
                            <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.index')">
                                {{ __('My Bookings') }}
                            </x-nav-link>

                        @elseif(Auth::user()->isOrganiser())
                            <!-- Organiser dashboard link -->
                            <x-nav-link :href="route('events.dashboard')" :active="request()->routeIs('events.dashboard')">
                                {{ __('My Events') }}
                            </x-nav-link>
                            <!-- Organiser create link -->
                            <x-nav-link :href="route('events.create')" :active="request()->routeIs('events.create')">
                                {{ __('Create Event') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>


            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- This content is only shown to logged in users -->
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <!-- Display Name and Role in Nav Bar -->
                                <div>{{ Auth::user()->name }}: {{ ucfirst(Auth::user()->role) }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
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
                @else
                    <!-- This content is shown only to guests -->
                    <div class="px-4">
                        <x-nav-link :href="route('login')" :active="request()->routeIs('login')">
                            {{ __("Log in") }}
                        </x-nav-link>
                    </div>
                    <div class="px-4">
                        <x-nav-link :href="route('register')" :active="request()->routeIs('register')">
                            {{ __("Register") }}
                        </x-nav-link>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>