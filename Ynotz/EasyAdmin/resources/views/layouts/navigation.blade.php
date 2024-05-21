<nav x-ref="appnav" x-data="{ open: false, height: $el.offsetHeight, actualHeight: $el.offsetHeight, opacity: 1 }"
    class="bg-base-100 transition-all duration-200"

    :style="'height: ' + height + 'px; opacity: '+opacity"
    x-init="
        setTimeout(() => {height = $el.offsetHeight; actualHeight = $el.offsetHeight;}, 100);
    "
    @navresize.window="
        height = $event.detail.navcollapsed ? 0 : actualHeight;
        opacity = $event.detail.navcollapsed ? 0 : 1;
        {{-- navcollapsed = $event.detail.navcollapsed; --}}
        setTimeout(() => {});
    "
    >
    <!-- Primary Navigation Menu -->
    {{-- <div x-data="{ navcollapsed: false}" --}}
    {{-- x-show="!navcollapsed" --}}
    <div
        class="px-2 md:px-4 py-2">
        <div class="flex justify-between">
            <div class="flex">
                <div class="shrink-0 flex items-center mr-2 md:hidden">
                    <x-easyadmin::display.icon x-data="{ sidebarhidden: true }" icon="easyadmin::icons.menu" height="h-7"
                        width="w-7"
                        @click="sidebarhidden=false; $dispatch('sidebarvisibility', {'hidden': false});" />
                </div>
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block mr-8">
                            <img src="{{ asset('images/logo.webp') }}" alt="logo" class="max-h-8 max-w-48">
                        </a>
                    @endauth
                    @guest
                        <a href="{{ route('home') }}" class="block mr-8">
                            <img src="{{ asset('images/logo.webp') }}" alt="logo" class="h-8">
                        </a>
                    @endguest
                    <span x-data="{ sidebarcollapse: false, collapsed: false }"
                        @sidebarresize.window="collapsed = $event.detail.collapsed;"
                        @navresize.window="collapsed = $event.detail.navcollapsed;"
                        class="flex flex-row items-center text-sm px-2 justify-end text-right transition-all"
                        >
                        <x-easyadmin::display.icon icon="easyadmin::icons.go_left" height="h-6" width="w-6"
                            @click="sidebarcollapse=!sidebarcollapse; collapsed=sidebarcollapse; $dispatch('sidebarresize', {'collapsed': sidebarcollapse});"
                            class="hidden md:inline-block transition-all" x-bind:class="!collapsed || 'rotate-180'" />
                    </span>
                </div>
                <!-- Navigation Links -->
                {{-- <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div> --}}
            </div>
            <div class="hidden md:flex flex-row space-x-6">
                <x-easyadmin::utils.theme-switch />
                <!-- Settings Dropdown -->
                <div class="hidden md:flex md:items-center md:ml-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="flex items-center font-medium text-accent-content hover:text-accent-content hover:border-base-200 focus:outline-none focus:text-accent-content focus:border-base-200 transition duration-150 ease-in-out p-1 pr-0 text-xs">
                                <span class="flex flex-row space-x-2 items-center">{{ Auth::user()->name }}</span>
                                @if (Auth::user()->getSingleMediaUrl('profile_picture') != null)
                                <img src="{{Auth::user()->getSingleMediaUrl('profile_picture')}}" alt="" class="inline-block w-5 h-5 rounded-full">
                                @else
                                <span class="ml-1 rounded-full w-6 h-6 flex flex-row justify-center items-center p-1 border border-base-content border-opacity-20">
                                    <x-easyadmin::display.icon icon="easyadmin::icons.user" width="w-5" height="h-5" />
                                </span>
                                @endif
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            {{-- <button class="block w-full px-4 py-2 text-sm leading-5 text-base-content hover:bg-base-200 focus:outline-none focus:bg-base-200 transition duration-150 ease-in-out text-left"
                                    @click.prevent.stop="$dispatch('passwordform');">
                                {{ __('Change Password') }}
                            </button> --}}
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" class="p-0 m-0">
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

            <!-- Hamburger -->
            <div class="-mr-2 flex space-x-2 items-center md:hidden">
                <x-easyadmin::utils.theme-switch />
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-base-content hover:text-base-content hover:bg-base-100 focus:outline-none focus:bg-base-100 focus:text-base-content transition duration-150 ease-in-out">
                    <x-easyadmin::display.icon icon="easyadmin::icons.user" height="h-6" width="w-6" />
                    {{-- <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg> --}}
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden md:hidden">
        {{-- <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div> --}}

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-base-content">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-base-content">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
