<!-- SIDEBAR SIMAT-RK -->
<aside x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed lg:sticky top-0 inset-y-0 left-0 z-50 w-72 lg:w-64 bg-slate-900 border-r border-slate-800 flex flex-col shrink-0 shadow-2xl transition-all duration-300 h-screen"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

    @php
        $user = Auth::user();
        $role = $user->role ?? 'sub_admin';

        $dashboardUrl = match ($role) {
            'master_admin' => route('masteradmin.dashboard'),
            'admin' => route('admin.dashboard'),
            'sub_admin' => route('subadmin.dashboard'),
            default => '/',
        };
    @endphp

    <!-- Sidebar Header with Mobile Close Button -->
    <div class="h-16 px-5 border-b border-slate-800 flex items-center justify-between bg-slate-950/40 shrink-0">
        <a href="{{ $dashboardUrl }}" @click="if (isMobile) sidebarOpen = false"
            class="flex items-center space-x-3 group focus:outline-none transition-all cursor-pointer"
            title="Kembali ke Dashboard">
            <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD"
                class="w-9 h-9 object-contain drop-shadow transition-transform duration-200 group-hover:scale-105">
            <div>
                <h2
                    class="font-extrabold text-white text-base tracking-tight transition-colors duration-200 group-hover:text-emerald-400">
                    SIMAT-RK</h2>
                <p class="text-[10px] text-emerald-400 font-semibold leading-tight">RSUD Dr. H. Koesnandi</p>
            </div>
        </a>

        <!-- Mobile Close Button (Hidden on Desktop) -->
        <button type="button" @click="sidebarOpen = false"
            class="lg:hidden p-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Sidebar Nav -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-2.5 scrollbar-thin scrollbar-thumb-slate-800">

        <!-- Section 1: Master Utama (Dropdown) -->
        @php
            $isMasterUtamaActive = request()->is('*/dashboard') 
                || request()->is('dashboard') 
                || request()->routeIs('astap.index') 
                || request()->routeIs('astap.create*') 
                || request()->routeIs('astap.pilih_jenis') 
                || (request()->routeIs('astap.edit*') && request('from') !== 'eksternal') 
                || request()->routeIs('kir.*') 
                || request()->routeIs('distribusi.*') 
                || request()->routeIs('bast.*');
        @endphp
        <div x-data="{ masterUtamaOpen: {{ $isMasterUtamaActive ? 'true' : 'false' }} }" class="space-y-1">
            <button type="button" @click="masterUtamaOpen = !masterUtamaOpen"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ $isMasterUtamaActive ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Master Utama</span>
                </div>
                <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200 {{ $isMasterUtamaActive ? 'rotate-90 text-emerald-400' : 'text-slate-500' }}"
                    :class="masterUtamaOpen ? 'rotate-90 text-emerald-400' : 'text-slate-500'"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Submenu Dropdown Master Utama -->
            <div x-show="masterUtamaOpen" @if(!$isMasterUtamaActive) x-cloak style="display: none;" @endif
                class="pl-4 pr-1 py-1 space-y-1 border-l-2 border-slate-800 ml-4 my-1">
                
                <!-- Dashboard -->
                <a href="{{ $dashboardUrl }}" @click="if (isMobile) sidebarOpen = false"
                    class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->is('*/dashboard') || request()->is('dashboard') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Data ASTAP / Katalog ASTAP -->
                @if ($role === 'sub_admin' || $user->canAccess('astap'))
                    <a href="{{ route('astap.index') }}" @click="if (isMobile) sidebarOpen = false"
                        class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('astap.index') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        <span>{{ $role === 'sub_admin' ? 'Katalog ASTAP' : 'Data ASTAP' }}</span>
                    </a>
                @endif

                <!-- Lembar KIR Ruangan (Khusus Sub Admin Ruangan) -->
                @if ($role === 'sub_admin')
                    <a href="{{ route('kir.index') }}" @click="if (isMobile) sidebarOpen = false"
                        class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('kir.index') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Lembar KIR Ruangan</span>
                    </a>
                @endif

                <!-- Distribusi ASTAP -->
                @if ($role === 'sub_admin' || $user->canAccess('distribusi'))
                    <a href="{{ route('distribusi.index') }}" @click="if (isMobile) sidebarOpen = false"
                        class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('distribusi.index') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>{{ $role === 'sub_admin' ? 'Pengajuan Baru' : 'Distribusi ASTAP' }}</span>
                    </a>
                @endif

                <!-- Berita Acara (BAST) -->
                @if ($user->canAccess('bast'))
                    <a href="{{ route('bast.index') }}" @click="if (isMobile) sidebarOpen = false"
                        class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('bast.index') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Berita Acara (BAST)</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Section 2: Master Aset (Dropdown) -->
        @if ($role === 'sub_admin' || $user->canAccess('mutasi') || $user->canAccess('unit') || $user->canAccess('astap'))
            @php
                $isMasterAsetActive = request()->routeIs('mutasi.*') 
                    || request()->routeIs('astap.edit_mutasi_masuk') 
                    || (request()->routeIs('astap.edit*') && request('from') === 'eksternal') 
                    || request()->routeIs('master.reklasifikasi*') 
                    || request()->routeIs('master.hibah*')
                    || request()->routeIs('astap.create-hibah');
                $isMutasiActive = request()->routeIs('mutasi.*') 
                    || request()->routeIs('astap.edit_mutasi_masuk') 
                    || (request()->routeIs('astap.edit*') && request('from') === 'eksternal');
            @endphp
            <div x-data="{ masterAsetOpen: {{ $isMasterAsetActive ? 'true' : 'false' }} }" class="space-y-1">
                <button type="button" @click="masterAsetOpen = !masterAsetOpen"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ $isMasterAsetActive ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>Master Aset</span>
                    </div>
                    <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200 {{ $isMasterAsetActive ? 'rotate-90 text-emerald-400' : 'text-slate-500' }}"
                        :class="masterAsetOpen ? 'rotate-90 text-emerald-400' : 'text-slate-500'"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Submenu Dropdown Master Aset -->
                <div x-show="masterAsetOpen" @if(!$isMasterAsetActive) x-cloak style="display: none;" @endif
                    class="pl-4 pr-1 py-1 space-y-1 border-l-2 border-slate-800 ml-4 my-1">
                    
                    <!-- Mutasi Aset (Dropdown Accordion: Internal & Eksternal) -->
                    @if ($role === 'sub_admin' || $user->canAccess('mutasi'))
                        @if ($role === 'sub_admin')
                            <a href="{{ route('mutasi.index') }}" @click="if (isMobile) sidebarOpen = false"
                                class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('mutasi.*') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                <span>Mutasi Aset</span>
                            </a>
                        @else
                            <div x-data="{ mutasiOpen: {{ $isMutasiActive ? 'true' : 'false' }} }" class="space-y-1">
                                <button type="button" @click="mutasiOpen = !mutasiOpen"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ $isMutasiActive ? 'bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                    <div class="flex items-center space-x-2.5">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                        <span>Mutasi Aset</span>
                                    </div>
                                    <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200 {{ $isMutasiActive ? 'rotate-90 text-emerald-400' : 'text-slate-500' }}"
                                        :class="mutasiOpen ? 'rotate-90 text-emerald-400' : 'text-slate-500'"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>

                                <!-- Submenu Dropdown Mutasi -->
                                <div x-show="mutasiOpen" @if(!$isMutasiActive) x-cloak style="display: none;" @endif
                                    class="pl-4 pr-1 py-1 space-y-1 border-l-2 border-slate-700/60 ml-3 my-1">
                                    <!-- 1. Mutasi Internal -->
                                    <a href="{{ route('mutasi.index') }}" @click="if (isMobile) sidebarOpen = false"
                                        class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all {{ request()->routeIs('mutasi.index') || request()->routeIs('mutasi.create') || request()->routeIs('mutasi.edit') ? 'bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                        <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ request()->routeIs('mutasi.index') || request()->routeIs('mutasi.create') || request()->routeIs('mutasi.edit') ? 'bg-emerald-400 ring-2 ring-emerald-400/40' : 'bg-slate-600' }}"></span>
                                        <span>Mutasi Internal</span>
                                    </a>

                                    <!-- 2. Mutasi Eksternal (Antar-OPD) -->
                                    <a href="{{ route('mutasi.eksternal') }}" @click="if (isMobile) sidebarOpen = false"
                                        class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all {{ request()->routeIs('mutasi.eksternal*') || request()->routeIs('astap.edit_mutasi_masuk') || (request()->routeIs('astap.edit*') && request('from') === 'eksternal') ? 'bg-indigo-500/20 text-indigo-300 font-bold border border-indigo-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                        <div class="flex items-center space-x-2.5">
                                            <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ request()->routeIs('mutasi.eksternal*') || request()->routeIs('astap.edit_mutasi_masuk') || (request()->routeIs('astap.edit*') && request('from') === 'eksternal') ? 'bg-indigo-400 ring-2 ring-indigo-400/40' : 'bg-slate-600' }}"></span>
                                            <span>Mutasi Eksternal</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- Reklasifikasi Aset -->
                    @if ($user->canAccess('astap') || $user->canAccess('master_data') || $role === 'admin' || $role === 'master_admin')
                        <a href="{{ route('master.reklasifikasi') }}" @click="if (isMobile) sidebarOpen = false"
                            class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('master.reklasifikasi*') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Reklasifikasi Aset</span>
                        </a>
                    @endif

                    <!-- Kelola Hibah Aset -->
                    @if ($user->canAccess('astap') || $user->canAccess('master_data') || $role === 'admin' || $role === 'master_admin')
                        <a href="{{ route('master.hibah') }}" @click="if (isMobile) sidebarOpen = false"
                            class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('master.hibah*') || request()->routeIs('astap.create-hibah') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                            </svg>
                            <span>Kelola Hibah Aset</span>
                        </a>
                    @endif

                </div>
            </div>
        @endif

        <!-- Section 3: Master Data Sistem (Dropdown) -->
        @if ($user->canAccess('users') || $user->canAccess('astap') || $user->canAccess('master_data') || $user->canAccess('unit'))
            @php
                $isMasterDataActive = request()->routeIs('master.users*') 
                    || request()->routeIs('master.jenis_astap*') 
                    || request()->routeIs('master.jenis_pengadaan*') 
                    || request()->routeIs('master.rekening_belanja*') 
                    || request()->routeIs('unit.*');
            @endphp
            <div x-data="{ masterDataOpen: {{ $isMasterDataActive ? 'true' : 'false' }} }" class="space-y-1">
                <button type="button" @click="masterDataOpen = !masterDataOpen"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ $isMasterDataActive ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Master Data Sistem</span>
                    </div>
                    <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200 {{ $isMasterDataActive ? 'rotate-90 text-emerald-400' : 'text-slate-500' }}"
                        :class="masterDataOpen ? 'rotate-90 text-emerald-400' : 'text-slate-500'"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Submenu Dropdown Master Data Sistem -->
                <div x-show="masterDataOpen" @if(!$isMasterDataActive) x-cloak style="display: none;" @endif
                    class="pl-4 pr-1 py-1 space-y-1 border-l-2 border-slate-800 ml-4 my-1">
                    
                    <!-- Manajemen Pengguna -->
                    @if ($user->canAccess('users'))
                        <a href="{{ route('master.users') }}" @click="if (isMobile) sidebarOpen = false"
                            class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('master.users*') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Manajemen Pengguna</span>
                        </a>
                    @endif

                    <!-- Jenis ASTAP (Klasifikasi Kode 108) -->
                    @if ($user->canAccess('astap'))
                        <a href="{{ route('master.jenis_astap') }}" @click="if (isMobile) sidebarOpen = false"
                            class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('master.jenis_astap*') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h10M7 12h10m-5 5h5" />
                            </svg>
                            <span>Jenis ASTAP</span>
                        </a>
                    @endif

                    <!-- Jenis Pengadaan -->
                    @if ($user->canAccess('master_data'))
                        <a href="{{ route('master.jenis_pengadaan') }}" @click="if (isMobile) sidebarOpen = false"
                            class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('master.jenis_pengadaan*') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Jenis Pengadaan</span>
                        </a>

                        <!-- Rekening Belanja SIPD -->
                        <a href="{{ route('master.rekening_belanja') }}" @click="if (isMobile) sidebarOpen = false"
                            class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('master.rekening_belanja*') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span>Rekening Belanja SIPD</span>
                        </a>
                    @endif

                    <!-- Unit & Paviliun -->
                    @if ($user->canAccess('unit'))
                        <a href="{{ route('unit.index') }}" @click="if (isMobile) sidebarOpen = false"
                            class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('unit.*') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01" />
                            </svg>
                            <span>Unit & Paviliun</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Section 4: Audit & Pemulihan (Dropdown) -->
        @if ($role === 'admin' || $role === 'master_admin')
            @php
                $isPemulihanActive = request()->routeIs('recycle_bin.*');
            @endphp
            <div x-data="{ pemulihanOpen: {{ $isPemulihanActive ? 'true' : 'false' }} }" class="space-y-1">
                <button type="button" @click="pemulihanOpen = !pemulihanOpen"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ $isPemulihanActive ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Audit & Pemulihan</span>
                    </div>
                    <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200 {{ $isPemulihanActive ? 'rotate-90 text-emerald-400' : 'text-slate-500' }}"
                        :class="pemulihanOpen ? 'rotate-90 text-emerald-400' : 'text-slate-500'"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Submenu Dropdown Audit & Pemulihan -->
                <div x-show="pemulihanOpen" @if(!$isPemulihanActive) x-cloak style="display: none;" @endif
                    class="pl-4 pr-1 py-1 space-y-1 border-l-2 border-slate-800 ml-4 my-1">
                    
                    <a href="{{ route('recycle_bin.index') }}" @click="if (isMobile) sidebarOpen = false"
                        class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('recycle_bin.*') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Pusat Data Terhapus</span>
                    </a>
                </div>
            </div>
        @endif

    </nav>

    <!-- Sidebar Footer / Account Info -->
    <div class="p-4 border-t border-slate-800 bg-slate-950/40 flex items-center justify-between shrink-0">
        @if (($role ?? '') === 'sub_admin')
            <a href="{{ route('subadmin.profile') }}"
                class="flex items-center space-x-2.5 overflow-hidden w-full text-left p-2 rounded-xl hover:bg-slate-900/80 transition-all cursor-pointer group focus:outline-none focus:ring-0 focus-visible:outline-none outline-none border-none shadow-none {{ request()->routeIs('subadmin.profile') ? 'bg-slate-900/80' : '' }}"
                title="Buka halaman ubah email dan password akun ruangan Anda">
                <div
                    class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-500/30 group-hover:border-emerald-400/60 group-hover:bg-emerald-500/30 flex items-center justify-center text-emerald-400 font-bold text-xs shrink-0 transition-all">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="truncate flex-1 min-w-0">
                    <p
                        class="text-xs font-bold text-white truncate group-hover:text-emerald-300 transition-colors flex items-center justify-between">
                        <span class="truncate">{{ Auth::user()->name ?? 'Pengguna' }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-emerald-400 ml-1 shrink-0 opacity-70 group-hover:opacity-100"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </p>
                    <p class="text-[10px] text-emerald-400 font-medium capitalize flex items-center space-x-1">
                        <span>{{ str_replace('_', ' ', $role) }}</span>
                        <span class="text-[9px] text-slate-400 font-normal">• Ubah Email & Password &rarr;</span>
                    </p>
                </div>
            </a>
        @else
            <div class="flex items-center space-x-2.5 overflow-hidden">
                <div
                    class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 font-bold text-xs shrink-0">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="truncate">
                    <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Pengguna' }}</p>
                    <p class="text-[10px] text-emerald-400 font-medium capitalize">{{ str_replace('_', ' ', $role) }}
                    </p>
                </div>
            </div>
        @endif
    </div>
</aside>
