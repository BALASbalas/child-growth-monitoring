<!-- ===== HEADER: Clean White ===== -->
<header class="app-header flex items-center px-4 sm:px-6">
    <div class="flex items-center justify-between w-full">
        <!-- Left side: Hamburger + Logo + System Name -->
        <div class="flex items-center gap-3">
            <!-- Hamburger Menu Button -->
            <button @click="
                if (window.innerWidth < 1024) {
                    mobileMenuOpen = !mobileMenuOpen;
                } else {
                    sidebarCollapsed = !sidebarCollapsed;
                    localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
                }
            " class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 focus:outline-none transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- Logo + System Name (dynamically loaded from settings) -->
            <div class="flex items-center gap-2">
                @php
                    $sysLogo = $systemSettings->system_logo ?? null;
                    $sysName = $systemSettings->system_name ?? 'Child Growth Monitor';
                @endphp
                @if($sysLogo)
                    <img src="{{ asset('storage/' . $sysLogo) }}" alt="{{ $sysName }}" class="h-10 w-auto rounded-lg" style="max-height: 40px;">
                @else
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #2563EB, #1D4ED8);">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                @endif
                <span class="text-sm font-bold hidden sm:inline" style="color: #0F172A;">
                    {{ $sysName }}
                </span>
            </div>
        </div>

        <!-- Right side -->
        <div class="flex items-center gap-1 sm:gap-2">
            @auth
            @php
                $cu = Auth::user();
                $initial = strtoupper(substr($cu->name, 0, 1));
                $avatarColors = ['#2563EB', '#8b5cf6', '#d946ef', '#ec4899', '#f43f5e', '#f97316', '#eab308', '#10b981', '#14b8a6', '#06b6d4'];
                $avatarColor = $avatarColors[crc32($cu->email ?: $cu->name) % count($avatarColors)];
            @endphp

            <div class="hidden xl:flex items-center ml-1">
                <span class="text-xs font-medium px-3 py-1.5 rounded-lg bg-gray-100 text-gray-500">{{ now()->format('M d, Y') }}</span>
            </div>

            <div class="hidden sm:block w-px h-8 bg-gray-200 mx-1"></div>

            <!-- Profile -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false" 
                        class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-gray-50 transition-colors group">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-sm ring-2 ring-gray-100" style="background-color: {{ $avatarColor }};">
                        {{ $initial }}
                    </div>
                    <div class="hidden lg:block text-left">
                        <p class="text-sm font-semibold text-gray-900 leading-tight">{{ $cu->name }}</p>
                        <p class="text-[10px] text-gray-500 leading-tight">{{ $cu->role_label }}</p>
                    </div>
                    <svg class="hidden lg:block w-3 h-3 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div class="profile-dropdown" :class="{'show': open}">
                    <div class="px-4 py-4 border-b border-gray-100" style="background: linear-gradient(135deg, #2563EB, #1D4ED8);">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-sm ring-2 ring-white/40" style="background-color: {{ $avatarColor }};">
                                {{ $initial }}
                            </div>
                            <div class="text-white">
                                <p class="text-sm font-bold">{{ $cu->name }}</p>
                                <p class="text-[10px] text-blue-200">{{ $cu->email }}</p>
                                <p class="text-[10px] text-blue-200/80">{{ $cu->role_label }}</p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="profile-dropdown-item">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        My Profile
                    </a>
                    <a href="{{ route('profile.edit') }}" class="profile-dropdown-item">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Account Settings
                    </a>
                    <hr class="border-gray-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="profile-dropdown-item w-full text-red-600 hover:bg-red-50 rounded-b-xl">
                            <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </div>
</header>