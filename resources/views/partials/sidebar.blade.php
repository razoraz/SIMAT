<!-- SIDEBAR SIMAT-RK (Bespoke High-End UI) -->
<aside x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed lg:sticky top-0 inset-y-0 left-0 z-50 w-72 lg:w-64 bg-gradient-to-b from-slate-900 via-slate-900/95 to-slate-950 border-r border-slate-800/80 flex flex-col shrink-0 shadow-2xl transition-all duration-300 h-screen"
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

        // Deteksi State Rute Aktif dengan Bersih & Presisi
        $isFromAstap = request('from') === 'astap';
        $isFormMasterAset = request()->routeIs('astap.create_kemitraan')
            || (request()->routeIs('astap.edit_kemitraan') && !$isFromAstap)
            || request()->routeIs('astap.create_hibah')
            || (request()->routeIs('astap.edit_hibah') && !$isFromAstap)
            || request()->routeIs('astap.create_belanja_barang')
            || (request()->routeIs('astap.edit_belanja_barang') && !$isFromAstap)
            || request()->routeIs('astap.create_mutasi*')
            || request()->routeIs('astap.edit_mutasi*');

        $isDashboardActive = request()->is('*/dashboard') || request()->is('dashboard');
        $isAstapActive = (
            request()->routeIs('astap.index') 
            || (request()->routeIs('astap.create*') && !$isFormMasterAset) 
            || request()->routeIs('astap.pilih_jenis') 
            || (request()->routeIs('astap.edit*') && !$isFormMasterAset && request('from') !== 'eksternal' && !request()->routeIs('astap.edit_mutasi*'))
            || $isFromAstap
        );
        $isKirActive = request()->routeIs('kir.*');
        $isDistribusiActive = request()->routeIs('distribusi.*');
        $isBastActive = request()->routeIs('bast.*');

        $isMasterUtamaActive = ($isDashboardActive || $isAstapActive || $isKirActive || $isDistribusiActive || $isBastActive) && !$isFormMasterAset;

        $isMutasiInternalActive = request()->routeIs('mutasi.*') && !request()->routeIs('mutasi.eksternal*');
        $isMutasiEksternalActive = request()->routeIs('mutasi.eksternal*') 
            || request()->routeIs('astap.create_mutasi_eksternal') 
            || request()->routeIs('astap.edit_mutasi_eksternal') 
            || request()->routeIs('astap.create_mutasi_masuk') 
            || request()->routeIs('astap.edit_mutasi_masuk') 
            || (request()->routeIs('astap.edit*') && request('from') === 'eksternal');
        $isMutasiActive = $isMutasiInternalActive || $isMutasiEksternalActive;

        $isReklasActive = request()->routeIs('master.reklasifikasi*');
        $isHibahActive = request()->routeIs('master.hibah*') || request()->routeIs('astap.create_hibah') || request()->routeIs('astap.edit_hibah') || request()->routeIs('astap.update_hibah');
        $isKemitraanActive = request()->routeIs('master.kemitraan*') || request()->routeIs('astap.create_kemitraan') || (request()->routeIs('astap.edit_kemitraan') && !$isFromAstap);
        $isBelanjaBarangActive = request()->routeIs('master.belanja_barang*') || request()->routeIs('astap.create_belanja_barang') || (request()->routeIs('astap.edit_belanja_barang') && !$isFromAstap);

        $isMasterAsetActive = $isMutasiActive || $isReklasActive || $isHibahActive || $isKemitraanActive || $isBelanjaBarangActive;

        $isUsersActive = request()->routeIs('master.users*');
        $isJenisAstapActive = request()->routeIs('master.jenis_astap*');
        $isJenisPengadaanActive = request()->routeIs('master.jenis_pengadaan*');
        $isRekeningActive = request()->routeIs('master.rekening_belanja*');
        $isUnitActive = request()->routeIs('unit.*');

        $isMasterDataActive = $isUsersActive || $isJenisAstapActive || $isJenisPengadaanActive || $isRekeningActive || $isUnitActive;

        $isRmbActive = request()->routeIs('rmb.*');
        $isAuditKoreksiActive = request()->routeIs('audit_koreksi.*');
        $isRecycleBinActive = request()->routeIs('recycle_bin.*');

        $isPemulihanActive = $isRmbActive || $isAuditKoreksiActive || $isRecycleBinActive;

        $defaultSection = match (true) {
            $isMasterAsetActive   => 'master_aset',
            $isMasterDataActive   => 'master_data',
            $isPemulihanActive    => 'pemulihan',
            default               => 'master_utama',
        };
    @endphp

    <!-- Sidebar Header with Hospital Branding & Subtle Glow -->
    <div class="h-16 px-4 sm:px-5 border-b border-slate-800/80 flex items-center justify-between bg-slate-950/60 backdrop-blur-md shrink-0">
        <a href="{{ $dashboardUrl }}" @click="if (isMobile) sidebarOpen = false"
            class="flex items-center space-x-3 group focus:outline-none transition-all cursor-pointer min-w-0"
            title="Kembali ke Dashboard">
            <div class="relative shrink-0">
                <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD"
                    class="w-9 h-9 object-contain drop-shadow-[0_0_10px_rgba(16,185,129,0.3)] transition-transform duration-200 group-hover:scale-105">
            </div>
            <div class="truncate">
                <div class="flex items-center space-x-1.5">
                    <h2 class="font-extrabold text-white text-base tracking-tight transition-colors duration-200 group-hover:text-emerald-400">
                        SIMAT-RK
                    </h2>
                    <span class="text-[9px] font-bold text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/30 uppercase tracking-widest">
                        v2.0
                    </span>
                </div>
                <p class="text-[10px] text-slate-400 font-medium leading-tight truncate">RSUD Dr. H. Koesnandi</p>
            </div>
        </a>

        <!-- Mobile Close Button (Hidden on Desktop) -->
        <button type="button" @click="sidebarOpen = false"
            class="lg:hidden p-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/80 transition-all shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Sidebar Nav with High Scannability Hierarchy (Single Open Accordion Mode) -->
    <nav x-data="{ activeSection: '{{ $defaultSection }}' }" class="flex-1 overflow-y-auto px-3 py-3.5 space-y-2 scrollbar-thin scrollbar-thumb-slate-800 notif-scroll">

        <!-- SECTION 1: MASTER UTAMA (Emerald / Teal Theme) -->
        <div class="space-y-1">
            <button type="button" @click="activeSection = (activeSection === 'master_utama' ? null : 'master_utama')"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ $isMasterUtamaActive ? 'bg-gradient-to-r from-emerald-500/20 via-teal-500/10 to-transparent text-emerald-300 border border-emerald-500/40 shadow-lg shadow-emerald-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
                <div class="flex items-center space-x-3 min-w-0">
                    @if($isMasterUtamaActive)
                        <span class="w-1 h-4 rounded-full bg-emerald-400 shadow-sm shadow-emerald-400/80 shrink-0"></span>
                    @endif
                    <svg class="w-4 h-4 shrink-0 transition-colors {{ $isMasterUtamaActive ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="truncate">Master Utama</span>
                </div>
                <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200"
                    :class="activeSection === 'master_utama' ? 'rotate-90 text-emerald-400' : 'text-slate-500'"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Submenu Dropdown Master Utama -->
            <div x-show="activeSection === 'master_utama'" @if($defaultSection !== 'master_utama') x-cloak style="display: none;" @endif
                class="pl-3.5 pr-1 py-1 space-y-1 border-l-2 border-slate-800/90 ml-4 my-1 transition-all">
                
                <!-- 1. Dashboard -->
                <a href="{{ $dashboardUrl }}" @click="if (isMobile) sidebarOpen = false"
                    class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isDashboardActive ? 'bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isDashboardActive ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Data ASTAP / Katalog ASTAP -->
                @if ($role === 'sub_admin' || $user?->canAccess('astap'))
                    <a href="{{ route('astap.index') }}" @click="if (isMobile) sidebarOpen = false"
                        class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isAstapActive ? 'bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isAstapActive ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        <span>{{ $role === 'sub_admin' ? 'Katalog ASTAP' : 'Data ASTAP' }}</span>
                    </a>
                @endif

                <!-- 3. Lembar KIR Ruangan (Khusus Sub Admin Ruangan) -->
                @if ($role === 'sub_admin')
                    <a href="{{ route('kir.index') }}" @click="if (isMobile) sidebarOpen = false"
                        class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isKirActive ? 'bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isKirActive ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Lembar KIR Ruangan</span>
                    </a>
                @endif

                <!-- 4. Distribusi ASTAP -->
                @if ($role === 'sub_admin' || $user?->canAccess('distribusi'))
                    <a href="{{ route('distribusi.index') }}" @click="if (isMobile) sidebarOpen = false"
                        class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isDistribusiActive ? 'bg-teal-500/20 text-teal-300 font-bold border border-teal-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isDistribusiActive ? 'text-teal-400' : 'text-slate-500 group-hover:text-teal-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>{{ $role === 'sub_admin' ? 'Pengajuan Baru' : 'Distribusi ASTAP' }}</span>
                    </a>
                @endif

                <!-- 5. Berita Acara (BAST) -->
                @if ($user?->canAccess('bast'))
                    <a href="{{ route('bast.index') }}" @click="if (isMobile) sidebarOpen = false"
                        class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isBastActive ? 'bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isBastActive ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Berita Acara (BAST)</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- SECTION 2: MASTER ASET (Indigo / Cyan Theme) -->
        @if ($role === 'sub_admin' || $role === 'master_admin' || $user?->canAccess('mutasi') || $user?->canAccess('astap'))
            <div class="space-y-1">
                <button type="button" @click="activeSection = (activeSection === 'master_aset' ? null : 'master_aset')"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ $isMasterAsetActive ? 'bg-gradient-to-r from-indigo-500/20 via-cyan-500/10 to-transparent text-indigo-300 border border-indigo-500/40 shadow-lg shadow-indigo-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
                    <div class="flex items-center space-x-3 min-w-0">
                        @if($isMasterAsetActive)
                            <span class="w-1 h-4 rounded-full bg-indigo-400 shadow-sm shadow-indigo-400/80 shrink-0"></span>
                        @endif
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isMasterAsetActive ? 'text-indigo-400' : 'text-slate-500 group-hover:text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span class="truncate">Master Aset</span>
                    </div>
                    <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200"
                        :class="activeSection === 'master_aset' ? 'rotate-90 text-indigo-400' : 'text-slate-500'"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Submenu Dropdown Master Aset -->
                <div x-show="activeSection === 'master_aset'" @if($defaultSection !== 'master_aset') x-cloak style="display: none;" @endif
                    class="pl-3.5 pr-1 py-1 space-y-1 border-l-2 border-slate-800/90 ml-4 my-1 transition-all">
                    
                    <!-- 1. Mutasi Aset (Dropdown Accordion: Internal & Eksternal) -->
                    @if ($role === 'sub_admin' || $user?->canAccess('mutasi'))
                        @if ($role === 'sub_admin')
                            <a href="{{ route('mutasi.index') }}" @click="if (isMobile) sidebarOpen = false"
                                class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isMutasiInternalActive ? 'bg-indigo-500/20 text-indigo-300 font-bold border border-indigo-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                                <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isMutasiInternalActive ? 'text-indigo-400' : 'text-slate-500 group-hover:text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                <span>Mutasi Aset</span>
                            </a>
                        @else
                            <div x-data="{ mutasiOpen: {{ $isMutasiActive ? 'true' : 'false' }} }" class="space-y-1">
                                <button type="button" @click="mutasiOpen = !mutasiOpen"
                                    class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all duration-200 group {{ $isMutasiActive ? 'bg-indigo-500/20 text-indigo-300 font-bold border border-indigo-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border border-transparent' }}">
                                    <div class="flex items-center space-x-2 min-w-0">
                                        <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isMutasiActive ? 'text-indigo-400' : 'text-slate-500 group-hover:text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                        <span class="truncate">Mutasi Aset</span>
                                    </div>
                                    <svg class="w-3 h-3 shrink-0 transition-transform duration-200"
                                        :class="mutasiOpen ? 'rotate-90 text-indigo-400' : 'text-slate-500'"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>

                                <!-- Submenu Dropdown Mutasi -->
                                <div x-show="mutasiOpen" @if(!$isMutasiActive) x-cloak style="display: none;" @endif
                                    class="pl-3 pr-1 py-1 space-y-1 border-l-2 border-indigo-500/30 ml-2.5 my-1">
                                    <!-- Mutasi Internal -->
                                    <a href="{{ route('mutasi.index') }}" @click="if (isMobile) sidebarOpen = false"
                                        class="group flex items-center space-x-2 px-2 py-1 rounded text-[10.5px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isMutasiInternalActive ? 'bg-indigo-500/25 text-indigo-300 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isMutasiInternalActive ? 'bg-indigo-400 shadow-sm shadow-indigo-400' : 'bg-slate-600' }}"></span>
                                        <span>Mutasi Internal</span>
                                    </a>

                                    <!-- Mutasi Eksternal (Antar-OPD) -->
                                    @if ($role === 'master_admin' || $user?->canDo('mutasi', 'eksternal'))
                                        <a href="{{ route('mutasi.eksternal') }}" @click="if (isMobile) sidebarOpen = false"
                                            class="group flex items-center space-x-2 px-2 py-1 rounded text-[10.5px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isMutasiEksternalActive ? 'bg-indigo-500/25 text-indigo-300 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $isMutasiEksternalActive ? 'bg-indigo-400 shadow-sm shadow-indigo-400' : 'bg-slate-600' }}"></span>
                                            <span>Mutasi Eksternal</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- 2. Reklasifikasi Aset -->
                    @if ($role === 'master_admin' || $user?->canDo('astap', 'reklas'))
                        <a href="{{ route('master.reklasifikasi') }}" @click="if (isMobile) sidebarOpen = false"
                            class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isReklasActive ? 'bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isReklasActive ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Reklasifikasi Aset</span>
                        </a>
                    @endif

                    <!-- 3. Kelola Hibah Aset -->
                    @if ($role === 'master_admin' || $user?->canDo('astap', 'hibah'))
                        <a href="{{ route('master.hibah') }}" @click="if (isMobile) sidebarOpen = false"
                            class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isHibahActive ? 'bg-indigo-500/20 text-indigo-300 font-bold border border-indigo-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isHibahActive ? 'text-indigo-400' : 'text-slate-500 group-hover:text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                            </svg>
                            <span>Kelola Hibah Aset</span>
                        </a>
                    @endif

                    <!-- 4. Kelola Kemitraan Aset (KSO / KSP / Sewa Akun 1.5.2) -->
                    @if ($role === 'master_admin' || $user?->canDo('astap', 'kemitraan'))
                        <a href="{{ route('master.kemitraan') }}" @click="if (isMobile) sidebarOpen = false"
                            class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isKemitraanActive ? 'bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isKemitraanActive ? 'text-cyan-400' : 'text-slate-500 group-hover:text-cyan-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Kelola Kemitraan Aset</span>
                        </a>
                    @endif

                    <!-- 5. Kelola Belanja Barang (Instalasi Perbekalan / Gudang) -->
                    @if ($role === 'master_admin' || $user?->canAccess('astap') || $user?->canAccess('master_data'))
                        <a href="{{ route('master.belanja_barang') }}" @click="if (isMobile) sidebarOpen = false"
                            class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isBelanjaBarangActive ? 'bg-teal-500/20 text-teal-300 font-bold border border-teal-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isBelanjaBarangActive ? 'text-teal-400' : 'text-slate-500 group-hover:text-teal-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span>Kelola Belanja Barang</span>
                        </a>
                    @endif

                </div>
            </div>
        @endif

        <!-- SECTION 3: MASTER DATA SISTEM (Cyan / Sky Blue Theme) -->
        @if ($role === 'master_admin' || $user?->canAccess('users') || $user?->canAccess('master_data') || $user?->canAccess('unit'))
            <div class="space-y-1">
                <button type="button" @click="activeSection = (activeSection === 'master_data' ? null : 'master_data')"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ $isMasterDataActive ? 'bg-gradient-to-r from-cyan-500/20 via-sky-500/10 to-transparent text-cyan-300 border border-cyan-500/40 shadow-lg shadow-cyan-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
                    <div class="flex items-center space-x-3 min-w-0">
                        @if($isMasterDataActive)
                            <span class="w-1 h-4 rounded-full bg-cyan-400 shadow-sm shadow-cyan-400/80 shrink-0"></span>
                        @endif
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isMasterDataActive ? 'text-cyan-400' : 'text-slate-500 group-hover:text-cyan-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="truncate">Master Data Sistem</span>
                    </div>
                    <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200"
                        :class="activeSection === 'master_data' ? 'rotate-90 text-cyan-400' : 'text-slate-500'"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Submenu Dropdown Master Data Sistem -->
                <div x-show="activeSection === 'master_data'" @if($defaultSection !== 'master_data') x-cloak style="display: none;" @endif
                    class="pl-3.5 pr-1 py-1 space-y-1 border-l-2 border-slate-800/90 ml-4 my-1 transition-all">
                    
                    <!-- 1. Manajemen Pengguna -->
                    @if ($role === 'master_admin' || $user?->canAccess('users'))
                        <a href="{{ route('master.users') }}" @click="if (isMobile) sidebarOpen = false"
                            class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isUsersActive ? 'bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isUsersActive ? 'text-cyan-400' : 'text-slate-500 group-hover:text-cyan-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Manajemen Pengguna</span>
                        </a>
                    @endif

                    <!-- 2. Jenis ASTAP (Klasifikasi Kode 108) -->
                    @if ($role === 'master_admin' || $user?->canDo('master_data', 'jenis_astap'))
                        <a href="{{ route('master.jenis_astap') }}" @click="if (isMobile) sidebarOpen = false"
                            class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isJenisAstapActive ? 'bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isJenisAstapActive ? 'text-cyan-400' : 'text-slate-500 group-hover:text-cyan-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h10M7 12h10m-5 5h5" />
                            </svg>
                            <span>Jenis ASTAP</span>
                        </a>
                    @endif

                    <!-- 3. Jenis Pengadaan -->
                    @if ($role === 'master_admin' || $user?->canDo('master_data', 'jenis_pengadaan'))
                        <a href="{{ route('master.jenis_pengadaan') }}" @click="if (isMobile) sidebarOpen = false"
                            class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isJenisPengadaanActive ? 'bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isJenisPengadaanActive ? 'text-cyan-400' : 'text-slate-500 group-hover:text-cyan-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Jenis Pengadaan</span>
                        </a>
                    @endif

                    <!-- 4. Rekening Belanja SIPD -->
                    @if ($role === 'master_admin' || $user?->canDo('master_data', 'rekening'))
                        <a href="{{ route('master.rekening_belanja') }}" @click="if (isMobile) sidebarOpen = false"
                            class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isRekeningActive ? 'bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isRekeningActive ? 'text-cyan-400' : 'text-slate-500 group-hover:text-cyan-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span>Rekening Belanja SIPD</span>
                        </a>
                    @endif

                    <!-- 5. Unit & Paviliun -->
                    @if ($role === 'master_admin' || $user?->canAccess('unit'))
                        <a href="{{ route('unit.index') }}" @click="if (isMobile) sidebarOpen = false"
                            class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isUnitActive ? 'bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isUnitActive ? 'text-cyan-400' : 'text-slate-500 group-hover:text-cyan-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01" />
                            </svg>
                            <span>Unit & Paviliun</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- SECTION 4: AUDIT & PEMULIHAN (Amber / Rose Theme) -->
        @if ($role === 'master_admin' || ($role === 'admin' && ($user?->canAccess('astap') || $user?->canAccess('recycle_bin'))))
            <div class="space-y-1">
                <button type="button" @click="activeSection = (activeSection === 'pemulihan' ? null : 'pemulihan')"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ $isPemulihanActive ? 'bg-gradient-to-r from-amber-500/20 via-rose-500/10 to-transparent text-amber-300 border border-amber-500/40 shadow-lg shadow-amber-500/10 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
                    <div class="flex items-center space-x-3 min-w-0">
                        @if($isPemulihanActive)
                            <span class="w-1 h-4 rounded-full bg-amber-400 shadow-sm shadow-amber-400/80 shrink-0"></span>
                        @endif
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isPemulihanActive ? 'text-amber-400' : 'text-slate-500 group-hover:text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span class="truncate">Audit & Pemulihan</span>
                    </div>
                    <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200"
                        :class="activeSection === 'pemulihan' ? 'rotate-90 text-amber-400' : 'text-slate-500'"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Submenu Dropdown Audit & Pemulihan -->
                <div x-show="activeSection === 'pemulihan'" @if($defaultSection !== 'pemulihan') x-cloak style="display: none;" @endif
                    class="pl-3.5 pr-1 py-1 space-y-1 border-l-2 border-slate-800/90 ml-4 my-1 transition-all">
                    
                    <!-- 1. Rekon Belanja Modal (RMB) -->
                    @if ($role === 'master_admin' || $user?->canAccess('astap'))
                        <a href="{{ route('rmb.index') }}" @click="if (isMobile) sidebarOpen = false"
                            class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isRmbActive ? 'bg-amber-500/20 text-amber-300 font-bold border border-amber-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isRmbActive ? 'text-amber-400' : 'text-slate-500 group-hover:text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Rekon Belanja Modal (RMB)</span>
                        </a>
                    @endif

                    <!-- 2. Audit Koreksi Nilai BMD -->
                    @if ($role === 'master_admin' || $user?->canAccess('astap'))
                        <a href="{{ route('audit_koreksi.index') }}" @click="if (isMobile) sidebarOpen = false"
                            class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isAuditKoreksiActive ? 'bg-amber-500/20 text-amber-300 font-bold border border-amber-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isAuditKoreksiActive ? 'text-amber-400' : 'text-slate-500 group-hover:text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Audit Koreksi Nilai BMD</span>
                        </a>
                    @endif

                    <!-- 3. Pusat Data Terhapus (Recycle Bin) -->
                    @if ($role === 'master_admin' || $user?->canAccess('recycle_bin'))
                        <a href="{{ route('recycle_bin.index') }}" @click="if (isMobile) sidebarOpen = false"
                            class="group flex items-center space-x-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition-all hover:translate-x-1 duration-150 {{ $isRecycleBinActive ? 'bg-rose-500/20 text-rose-300 font-bold border border-rose-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 transition-colors {{ $isRecycleBinActive ? 'text-rose-400' : 'text-slate-500 group-hover:text-rose-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Pusat Data Terhapus</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

    </nav>

    <!-- Sidebar Footer / Account Info Card (Bespoke Hospital Profile) -->
    <div class="p-3 border-t border-slate-800/80 bg-slate-950/60 backdrop-blur-md shrink-0">
        @if (($role ?? '') === 'sub_admin')
            <a href="{{ route('subadmin.profile') }}"
                class="flex items-center space-x-2.5 overflow-hidden w-full text-left p-2 rounded-xl bg-slate-900/60 hover:bg-slate-800/70 border border-slate-800/80 hover:border-emerald-500/30 transition-all cursor-pointer group focus:outline-none shadow-sm {{ request()->routeIs('subadmin.profile') ? 'border-emerald-500/40 bg-emerald-500/10' : '' }}"
                title="Buka halaman ubah email dan password akun ruangan Anda">
                <div class="relative shrink-0">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-500/30 to-teal-600/30 border border-emerald-500/40 flex items-center justify-center text-emerald-300 font-bold text-xs shadow-sm">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-400 ring-2 ring-slate-950"></span>
                </div>
                <div class="truncate flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-white truncate group-hover:text-emerald-300 transition-colors">
                            {{ $user->name ?? 'Pengguna' }}
                        </p>
                        <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-emerald-400 shrink-0 opacity-70 group-hover:opacity-100 transition-opacity"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <p class="text-[10px] text-emerald-400 font-medium capitalize flex items-center space-x-1 mt-0.5">
                        <span class="truncate">{{ str_replace('_', ' ', $role) }}</span>
                        <span class="text-[9px] text-slate-400 font-normal shrink-0">• Ubah Akun &rarr;</span>
                    </p>
                </div>
            </a>
        @else
            <div class="flex items-center space-x-2.5 overflow-hidden w-full p-2 rounded-xl bg-slate-900/60 border border-slate-800/80 shadow-sm">
                <div class="relative shrink-0">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-500/30 to-cyan-600/30 border border-emerald-500/40 flex items-center justify-center text-emerald-300 font-bold text-xs shadow-sm">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-400 ring-2 ring-slate-950 animate-pulse"></span>
                </div>
                <div class="truncate flex-1 min-w-0">
                    <p class="text-xs font-bold text-white truncate">{{ $user->name ?? 'Pengguna' }}</p>
                    <div class="flex items-center justify-between gap-1 mt-0.5">
                        <span class="text-[10px] text-emerald-400 font-medium capitalize truncate">{{ str_replace('_', ' ', $role) }}</span>
                        <span class="inline-flex items-center text-[9px] text-slate-400 font-mono shrink-0">
                            SIMAT-RK
                        </span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</aside>
