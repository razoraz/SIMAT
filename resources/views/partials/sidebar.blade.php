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
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 scrollbar-thin scrollbar-thumb-slate-800">

        <!-- Section 1: Master Utama -->
        <div>
            <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Master Utama</div>
            <div class="space-y-1">
                <!-- Dashboard -->
                <a href="{{ $dashboardUrl }}" @click="if (isMobile) sidebarOpen = false"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->is('*/dashboard') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Data ASTAP / Katalog ASTAP -->
                @if ($role === 'sub_admin' || $user->canAccess('astap'))
                    <a href="{{ route('astap.index') }}" @click="if (isMobile) sidebarOpen = false"
                        class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('astap.index') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        <span>{{ $role === 'sub_admin' ? 'Katalog ASTAP' : 'Data ASTAP' }}</span>
                    </a>
                @endif

                <!-- Lembar KIR Ruangan (Khusus Sub Admin Ruangan, disembunyikan dari Master Admin & Admin) -->
                @if ($role === 'sub_admin')
                    <a href="{{ route('kir.index') }}" @click="if (isMobile) sidebarOpen = false"
                        class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('kir.index') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Lembar KIR Ruangan</span>
                    </a>
                @endif

                <!-- Distribusi ASTAP -->
                @if ($role === 'sub_admin' || $user->canAccess('distribusi'))
                    <a href="{{ route('distribusi.index') }}" @click="if (isMobile) sidebarOpen = false"
                        class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('distribusi.index') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>{{ $role === 'sub_admin' ? 'Pengajuan Baru' : 'Distribusi ASTAP' }}</span>
                    </a>
                @endif

                <!-- Berita Acara (BAST) - Khusus yang memiliki wewenang modul BAST -->
                @if ($user->canAccess('bast'))
                    <a href="{{ route('bast.index') }}" @click="if (isMobile) sidebarOpen = false"
                        class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('bast.index') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Berita Acara (BAST)</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Section 2: Master Aset (Grup Khusus Manajemen Lokasi & Servis Aset) -->
        @if ($role === 'sub_admin' || $user->canAccess('mutasi') || $user->canAccess('unit'))
            <div>
                <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Master Aset</div>
                <div class="space-y-1">
                    <!-- Mutasi Aset -->
                    @if ($role === 'sub_admin' || $user->canAccess('mutasi'))
                        <a href="{{ route('mutasi.index') }}" @click="if (isMobile) sidebarOpen = false"
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('mutasi.index') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <span>Mutasi Aset</span>
                        </a>
                    @endif

                    <!-- Unit & Paviliun (Katalog Unit & Ruangan RSUD) -->
                    @if ($user->canAccess('unit'))
                        <a href="{{ route('unit.index') }}" @click="if (isMobile) sidebarOpen = false"
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('unit.index') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01" />
                            </svg>
                            <span>Unit & Paviliun</span>
                        </a>
                    @endif

                </div>
            </div>
        @endif

        <!-- Section 3: Master Data Sistem (Role Master Admin & Admin dengan hak akses terkait) -->
        @if ($user->canAccess('users') || $user->canAccess('astap') || $user->canAccess('master_data'))
            <div>
                <div class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Master Data Sistem
                </div>
                <div class="space-y-1">
                    <!-- Manajemen Pengguna -->
                    @if ($user->canAccess('users'))
                        <a href="{{ route('master.users') }}" @click="if (isMobile) sidebarOpen = false"
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('master.users') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Manajemen Pengguna</span>
                        </a>
                    @endif

                    <!-- Jenis ASTAP (Klasifikasi Kode 108 Permendagri) -->
                    @if ($user->canAccess('astap'))
                        <a href="{{ route('master.jenis_astap') }}" @click="if (isMobile) sidebarOpen = false"
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('master.jenis_astap') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h10M7 12h10m-5 5h5" />
                            </svg>
                            <span>Jenis ASTAP</span>
                        </a>
                    @endif

                    <!-- Jenis Pengadaan -->
                    @if ($user->canAccess('master_data'))
                        <a href="{{ route('master.jenis_pengadaan') }}" @click="if (isMobile) sidebarOpen = false"
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('master.jenis_pengadaan') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Jenis Pengadaan</span>
                        </a>

                        <!-- Rekening Belanja SIPD -->
                        <a href="{{ route('master.rekening_belanja') }}" @click="if (isMobile) sidebarOpen = false"
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('master.rekening_belanja') ? 'bg-gradient-to-r from-emerald-600/20 to-teal-600/20 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span>Rekening Belanja SIPD</span>
                        </a>
                    @endif
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
