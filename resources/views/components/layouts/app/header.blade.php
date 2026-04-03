<nav class="relative text-white border-gray-200 bg-primary-500 dark:bg-gray-900">
    <div class="items-center max-w-screen-xl mx-auto navbar px-4">
        <div class="navbar-start">
            <div class="dropdown">
                <div tabindex="0" role="button" class="btn btn-ghost lg:hidden me-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" /></svg>
                </div>
                <ul tabindex="0" class="menu menu-lg dropdown-content mt-3 z-[1] p-2 border border-primary-50 shadow-2xl shadow-primary-500/50 bg-primary-500 rounded-box w-52">
                    <x-layouts.app.navigation-links></x-layouts.app.navigation-links>
                </ul>
            </div>
            <a href="/" class="flex items-center justify-center">
                <img src="{{asset(config('app.logo.light') )}}?{{ time() }}" class="h-10" alt="Logo" />
            </a>
        </div>

        <div class="hidden navbar-center lg:flex">
            <x-nav>
                <x-layouts.app.navigation-links></x-layouts.app.navigation-links>
            </x-nav>
        </div>

        <div class="navbar-end gap-2 md:gap-4">
            {{-- Modern Language Switcher (DaisyUI Native - No JS needed) --}}
            {{-- Modern Language Switcher (Fixed Icon Selection) --}}
            <div class="dropdown dropdown-end">
                {{-- الزرار الأساسي --}}
                <div
                    tabindex="0"
                    role="button"
                    class="btn btn-ghost btn-circle border border-white/20 hover:bg-white/10 transition-all flex items-center justify-center"
                >
                    <div class="w-6 h-4 overflow-hidden rounded-sm shadow-sm pointer-events-none">
                        {{-- استخدام الدالة الخاصة بالحزمة لضمان قراءة اللغة الحالية بدقة --}}
                        @if(LaravelLocalization::getCurrentLocale() == 'de')
                            <img src="https://flagcdn.com/w40/de.png" class="w-full h-full object-cover" alt="German">
                        @else
                            <img src="https://flagcdn.com/w40/gb.png" class="w-full h-full object-cover" alt="English">
                        @endif
                    </div>
                </div>

                {{-- القائمة المنسدلة --}}
                <ul
                    tabindex="0"
                    class="dropdown-content mt-3 z-[100] p-2 shadow-2xl menu menu-sm bg-[#1a1a2e] border border-[#2e2e4f] rounded-xl w-44 text-white list-none"
                >
                    <li class="menu-title text-gray-500 text-[10px] uppercase tracking-widest pb-2 px-4 italic font-bold">
                        {{ __('Select Language') }}
                    </li>

                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        <li class="{{ $loop->first ? '' : 'mt-1' }}">
                            <a
                                rel="alternate"
                                hreflang="{{ $localeCode }}"
                                href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                                class="flex items-center justify-between py-3 px-4 hover:bg-primary-500/20 rounded-lg no-underline {{ LaravelLocalization::getCurrentLocale() == $localeCode ? 'bg-primary-600/30 text-white' : 'text-gray-300' }}"
                            >
                                <div class="flex items-center gap-3">
                                    <img src="https://flagcdn.com/w20/{{ $localeCode == 'en' ? 'gb' : $localeCode }}.png"
                                         class="w-5 h-3.5 rounded-sm shadow-sm"
                                         alt="{{ $properties['native'] }}">
                                    <span class="font-medium text-sm">{{ $properties['native'] }}</span>
                                </div>

                                @if(LaravelLocalization::getCurrentLocale() == $localeCode)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-[#a78bfa]">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>            @auth
                <x-layouts.app.user-menu></x-layouts.app.user-menu>
            @else
                <x-link class="hidden md:block text-primary-50" href="{{route('login')}}">{{ __('Login') }}</x-link>
                <x-button-link.secondary elementType="a" href="#plans">{{ __('Get started') }}</x-button-link.secondary>
            @endauth
        </div>
    </div>
</nav>
