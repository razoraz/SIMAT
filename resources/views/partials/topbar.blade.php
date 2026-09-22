<!-- TOPBAR SIMAT-RK -->
<header class="h-16 bg-slate-900/95 backdrop-blur-md border-b border-slate-800 px-4 sm:px-6 flex items-center justify-between z-30 sticky top-0 shadow-lg shadow-black/20">
    <!-- Left Section: Hamburger & Breadcrumb -->
    <div class="flex items-center space-x-3 sm:space-x-4">
        <button type="button" @click="sidebarOpen = !sidebarOpen"
            class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/80 focus:outline-none transition-all"
            aria-label="Toggle Navigation">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div>
            <h1 class="text-sm font-bold text-white tracking-tight">@yield('page-title', 'Dashboard')</h1>
            <p class="text-[11px] text-slate-400 hidden sm:block">@yield('breadcrumb', 'Beranda Utama SIMAT-RK')</p>
        </div>
    </div>

    <!-- Right Section: Info Role, Notifikasi, User Profile -->
    <div class="flex items-center space-x-2 sm:space-x-3">

        @php
            $user = Auth::user();
            $role = $user->role ?? 'sub_admin';
            
            $roleInfo = match($role) {
                'master_admin' => [
                    'title' => 'Master Admin System',
                    'color' => 'amber',
                    'badgeBg' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                    'desc' => 'Wewenang Penuh: Anda memiliki kontrol penuh ke seluruh modul sistem, mencakup manajemen pengguna, pembuatan akun baru, pengaturan data master, serta laporan audit aset.'
                ],
                'admin' => [
                    'title' => 'Admin Operasional',
                    'color' => 'cyan',
                    'badgeBg' => 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30',
                    'desc' => 'Wewenang Operasional: Anda dapat mengelola data ASTAP, menginput data pengadaan barang, mengatur distribusi aset RSUD, dan menerbitkan Berita Acara (BAST).'
                ],
                'sub_admin' => [
                    'title' => 'Sub Admin / User Master',
                    'color' => 'emerald',
                    'badgeBg' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                    'desc' => 'Wewenang Pengguna Khusus: Anda dapat mengajukan permohonan aset, melihat riwayat distribusi barang, serta memantau status aset pada unit Anda.'
                ],
                default => [
                    'title' => 'Pengguna',
                    'color' => 'slate',
                    'badgeBg' => 'bg-slate-500/20 text-slate-300 border-slate-500/30',
                    'desc' => 'Akses standar sistem.'
                ]
            };
        @endphp

        <!-- Tombol Info Role (Hover & Click Popover) -->
        <div x-data="{ infoOpen: false }" class="relative" @mouseleave="infoOpen = false">
            <button type="button" @mouseenter="infoOpen = true" @click="infoOpen = !infoOpen"
                class="p-2 rounded-xl text-slate-400 hover:text-cyan-400 hover:bg-slate-800/80 focus:outline-none transition-all relative"
                title="Informasi Hak Akses & Wewenang Role">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </button>

            <!-- Popover Card Informasi Role -->
            <div x-show="infoOpen" x-cloak
                 x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-72 sm:w-80 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-4 z-50 pointer-events-auto">
                <div class="flex items-center space-x-2.5 mb-2.5 pb-2 border-b border-slate-800">
                    <div class="p-1.5 rounded-lg bg-cyan-500/10 text-cyan-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">Informasi Hak Akses Role</h4>
                        <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-md border mt-0.5 {{ $roleInfo['badgeBg'] }}">
                            {{ $roleInfo['title'] }}
                        </span>
                    </div>
                </div>
                <p class="text-[11px] text-slate-300 leading-relaxed">
                    {{ $roleInfo['desc'] }}
                </p>
                <div class="mt-3 pt-2 border-t border-slate-800/80 flex items-center justify-between text-[10px] text-slate-500">
                    <span>A-SIMAT RSUD Dr. H. Koesnandi</span>
                    <span>Role: {{ strtoupper($role) }}</span>
                </div>
            </div>
        </div>

        <!-- Tombol Notifikasi Systems -->
        <div x-data="systemNotificationComponent()" class="relative">

            <!-- Bell Button (Klik untuk melihat daftar tanpa otomatis menandai terbaca semua) -->
            <button type="button" @click="notifOpen = !notifOpen"
                class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/80 focus:outline-none transition-all relative group"
                title="Notifikasi">
                <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>

                <!-- Badge Titik Hijau Berkedip -->
                <span x-show="unreadCount > 0" x-cloak class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 ring-2 ring-slate-900"></span>
                </span>
            </button>

            <!-- Dropdown Notifikasi -->
            <div x-show="notifOpen" x-cloak @click.away="notifOpen = false"
                 x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 style="width: 440px; max-width: 95vw;"
                 class="absolute right-0 mt-2 bg-slate-900/95 backdrop-blur-xl border border-slate-800 rounded-2xl shadow-2xl overflow-hidden z-50">
                
                <!-- Header Notifikasi -->
                <div class="px-4 py-3 border-b border-slate-800 flex items-center justify-between bg-slate-950/60">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-bold text-white uppercase tracking-wider">Notifikasi Sistem</span>
                        <span x-show="unreadCount > 0" x-cloak
                              class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/30" 
                              x-text="unreadCount + ' Baru'"></span>
                    </div>
                    <button type="button" @click="markAllAsRead()" class="text-[11px] text-slate-400 hover:text-emerald-400 transition-colors font-medium">
                        Tandai Dibaca
                    </button>
                </div>

                <!-- Windows Desktop Notification & Sound Control Bar -->
                <template x-if="desktopPermission !== 'granted'">
                    <div class="px-3.5 py-2 bg-gradient-to-r from-emerald-950/50 via-teal-950/40 to-slate-950/60 border-b border-emerald-500/30 flex items-center justify-between gap-2">
                        <div class="flex items-center space-x-2 min-w-0">
                            <span class="text-xs">🔔</span>
                            <span class="text-[11px] text-emerald-200 font-medium truncate">Aktifkan Notifikasi Windows & Suara</span>
                        </div>
                        <button type="button" @click="requestDesktopPermission()" class="px-2.5 py-1 rounded-lg text-[10.5px] font-bold bg-emerald-500 text-slate-950 hover:bg-emerald-400 shadow-md transition-all shrink-0 flex items-center space-x-1">
                            <span>Aktifkan</span>
                        </button>
                    </div>
                </template>
                <template x-if="desktopPermission === 'granted'">
                    <div class="px-3.5 py-1.5 bg-slate-950/40 border-b border-slate-800/60 flex items-center justify-between text-[10.5px] text-slate-400">
                        <div class="flex items-center space-x-1.5 text-emerald-400 font-semibold">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 shadow-sm shadow-emerald-400/50"></span>
                            <span>Windows Desktop Aktif</span>
                        </div>
                        <div class="flex items-center space-x-2.5">
                            <button type="button" @click="playChimeSound(); showDesktopToast('SIMAT-RK RSUD Dr. H. Koesnandi', 'Tes Suara & Notifikasi Windows Berhasil! 🔔', null)" 
                                    class="text-slate-300 hover:text-emerald-300 transition-colors font-semibold flex items-center space-x-1" 
                                    title="Klik untuk uji coba suara lonceng dan pop-up Windows">
                                <span>🔊 Tes Suara</span>
                            </button>
                            <span class="text-slate-700">|</span>
                            <button type="button" @click="toggleSound()" 
                                    class="transition-colors font-medium flex items-center space-x-1" 
                                    :class="soundEnabled ? 'text-cyan-400 hover:text-cyan-300' : 'text-rose-400 hover:text-rose-300 line-through'" 
                                    :title="soundEnabled ? 'Mute Suara' : 'Nyalakan Suara'">
                                <span x-text="soundEnabled ? 'Mute' : 'Muted'"></span>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Filter Cards / Chips 1 Baris Horisontal -->
                <div class="px-3 py-2 bg-slate-950/40 border-b border-slate-800/60 flex items-center gap-1.5">
                    <button type="button" @click="filterType = 'all'"
                        class="flex-1 py-1.5 rounded-lg text-[10.5px] font-semibold transition-all border text-center flex items-center justify-center space-x-1 whitespace-nowrap"
                        :class="filterType === 'all' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 shadow-sm' : 'bg-slate-800/40 text-slate-400 border-slate-800 hover:text-slate-200'">
                        <span>Semua</span>
                    </button>
                    <button type="button" @click="filterType = 'astap'"
                        class="flex-1 py-1.5 rounded-lg text-[10.5px] font-semibold transition-all border text-center flex items-center justify-center space-x-1 whitespace-nowrap"
                        :class="filterType === 'astap' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 shadow-sm' : 'bg-slate-800/40 text-slate-400 border-slate-800 hover:text-slate-200'">
                        <span>📦</span> <span>ASTAP</span>
                    </button>
                    <button type="button" @click="filterType = 'distribusi'"
                        class="flex-1 py-1.5 rounded-lg text-[10.5px] font-semibold transition-all border text-center flex items-center justify-center space-x-1 whitespace-nowrap"
                        :class="filterType === 'distribusi' ? 'bg-teal-500/20 text-teal-300 border-teal-500/40 shadow-sm' : 'bg-slate-800/40 text-slate-400 border-slate-800 hover:text-slate-200'">
                        <span>🚚</span> <span>Distribusi</span>
                    </button>
                    <button type="button" @click="filterType = 'mutasi'"
                        class="flex-1 py-1.5 rounded-lg text-[10.5px] font-semibold transition-all border text-center flex items-center justify-center space-x-1 whitespace-nowrap"
                        :class="filterType === 'mutasi' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40 shadow-sm' : 'bg-slate-800/40 text-slate-400 border-slate-800 hover:text-slate-200'">
                        <span>🔄</span> <span>Mutasi</span>
                    </button>
                </div>

                <!-- Daftar Notifikasi dengan Scrolling Rapi -->
                <div class="divide-y divide-slate-800/60 max-h-64 overflow-y-auto notif-scroll" style="max-height: 260px; overflow-y: auto;">
                    @forelse($systemNotifications ?? [] as $notif)
                        <a href="{{ $notif['link'] }}" 
                           x-show="filterType === 'all' || filterType === '{{ $notif['type'] }}'"
                           class="block px-3.5 py-2.5 hover:bg-slate-800/60 transition-colors relative {{ $notif['is_unread'] ? 'bg-emerald-500/5 notif-item-unread' : '' }}">
                            <div class="flex items-center space-x-3">
                                <!-- Ikon Notifikasi Berdasarkan Kategori -->
                                <div class="shrink-0">
                                    @if($notif['type'] === 'astap')
                                        <div class="rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xs shadow-sm"
                                             style="width: 28px; height: 28px; min-width: 28px; min-height: 28px;">
                                            📦
                                        </div>
                                    @elseif($notif['type'] === 'distribusi')
                                        <div class="rounded-lg bg-teal-500/10 text-teal-400 border border-teal-500/30 flex items-center justify-center text-xs shadow-sm"
                                             style="width: 28px; height: 28px; min-width: 28px; min-height: 28px;">
                                            🚚
                                        </div>
                                    @elseif($notif['type'] === 'mutasi')
                                        <div class="rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/30 flex items-center justify-center text-xs shadow-sm"
                                             style="width: 28px; height: 28px; min-width: 28px; min-height: 28px;">
                                            🔄
                                        </div>
                                    @else
                                        <div class="rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/30 flex items-center justify-center text-xs shadow-sm"
                                             style="width: 28px; height: 28px; min-width: 28px; min-height: 28px;">
                                            🔔
                                        </div>
                                    @endif
                                </div>

                                <!-- Konten Notifikasi Ringkas -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1">
                                        <p class="text-xs font-bold text-white truncate">{{ $notif['title'] }}</p>
                                        <span class="text-[10px] text-slate-400 font-mono shrink-0">{{ $notif['time_ago'] }}</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2 mt-0.5">
                                        <p class="text-[11px] text-slate-400 truncate">{{ $notif['message'] }}</p>
                                        @if($notif['is_unread'])
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 notif-unread-dot shrink-0"></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-6 text-center text-slate-500">
                            <div class="text-2xl mb-1">📭</div>
                            <p class="text-xs font-medium">Belum ada notifikasi baru untuk Anda</p>
                        </div>
                    @endforelse
                </div>

                <!-- Footer Dropdown -->
                <div class="px-4 py-2 border-t border-slate-800/80 bg-slate-950/40 text-center text-[10px] text-slate-500 font-medium">
                    A-SIMAT • Notifikasi otomatis dihapus setelah 24 jam
                </div>
            </div>
        </div>

        <!-- Script Logika Notifikasi Terpusat & Windows Desktop Audio API -->
        <script>
            function systemNotificationComponent() {
                return {
                    notifOpen: false,
                    filterType: 'all',
                    unreadCount: {{ (int)($unreadNotifCount ?? 0) }},
                    timer: null,
                    soundEnabled: localStorage.getItem('simat_sound_enabled') !== 'false',
                    desktopPermission: (typeof window !== 'undefined' && 'Notification' in window) ? Notification.permission : 'default',

                    init() {
                        if (typeof window !== 'undefined' && 'Notification' in window) {
                            this.desktopPermission = Notification.permission;
                        }

                        const initialNotifs = @json($systemNotifications ?? []);
                        if (Array.isArray(initialNotifs) && initialNotifs.length > 0) {
                            this.checkAndAlertNew(initialNotifs);
                        } else if (!localStorage.getItem('simat_last_alerted_id')) {
                            localStorage.setItem('simat_last_alerted_id', '0');
                        }

                        this.timer = setInterval(() => {
                            this.refreshNotif();
                        }, 15000);
                    },

                    playChimeSound() {
                        if (!this.soundEnabled) return;
                        try {
                            const AudioCtx = window.AudioContext || window.webkitAudioContext;
                            if (!AudioCtx) return;
                            const ctx = new AudioCtx();
                            
                            if (ctx.state === 'suspended') {
                                ctx.resume();
                            }

                            const now = ctx.currentTime;
                            
                            // Nada 1: C5 (523.25 Hz)
                            const osc1 = ctx.createOscillator();
                            const gain1 = ctx.createGain();
                            osc1.type = 'sine';
                            osc1.frequency.setValueAtTime(523.25, now);
                            gain1.gain.setValueAtTime(0.25, now);
                            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                            osc1.connect(gain1);
                            gain1.connect(ctx.destination);
                            osc1.start(now);
                            osc1.stop(now + 0.35);

                            // Nada 2: E5 (659.25 Hz) Lonceng Harmonis
                            const osc2 = ctx.createOscillator();
                            const gain2 = ctx.createGain();
                            osc2.type = 'sine';
                            osc2.frequency.setValueAtTime(659.25, now + 0.12);
                            gain2.gain.setValueAtTime(0.3, now + 0.12);
                            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
                            osc2.connect(gain2);
                            gain2.connect(ctx.destination);
                            osc2.start(now + 0.12);
                            osc2.stop(now + 0.55);
                        } catch (e) {
                            console.warn('Audio chime error:', e);
                        }
                    },

                    showDesktopToast(title, message, link) {
                        if (typeof window === 'undefined' || !('Notification' in window) || Notification.permission !== 'granted') return;
                        try {
                            const iconUrl = '{{ asset('img/Logo-rsud/logo-rsud.png') }}';
                            const notif = new Notification(title, {
                                body: message,
                                icon: iconUrl,
                                badge: iconUrl,
                                tag: 'simat-notif-' + Date.now(),
                                renotify: true
                            });
                            notif.onclick = () => {
                                window.focus();
                                if (link && link !== '#') {
                                    window.location.href = link;
                                }
                                notif.close();
                            };
                        } catch (e) {
                            console.warn('Windows Desktop notification error:', e);
                        }
                    },

                    async requestDesktopPermission() {
                        if (typeof window === 'undefined' || !('Notification' in window)) {
                            alert('Browser Anda tidak mendukung Web Notification API Windows.');
                            return;
                        }
                        try {
                            const perm = await Notification.requestPermission();
                            this.desktopPermission = perm;
                            if (perm === 'granted') {
                                this.playChimeSound();
                                this.showDesktopToast('SIMAT-RK RSUD Dr. H. Koesnandi', 'Notifikasi Windows & Suara Berhasil Diaktifkan! ✅', null);
                            } else if (perm === 'denied') {
                                alert('Izin notifikasi ditolak di browser. Silakan klik ikon gembok di samping alamat URL browser Anda untuk mengizinkan notifikasi.');
                            }
                        } catch (e) {
                            console.error('Gagal meminta izin notifikasi:', e);
                        }
                    },

                    toggleSound() {
                        this.soundEnabled = !this.soundEnabled;
                        localStorage.setItem('simat_sound_enabled', this.soundEnabled ? 'true' : 'false');
                        if (this.soundEnabled) {
                            this.playChimeSound();
                        }
                    },

                    checkAndAlertNew(notifications) {
                        if (!Array.isArray(notifications) || notifications.length === 0) return;

                        const unreadItems = notifications.filter(n => n.is_unread);
                        if (unreadItems.length === 0) return;

                        const storedAlertedId = localStorage.getItem('simat_last_alerted_id');
                        const lastAlertedId = storedAlertedId !== null ? parseInt(storedAlertedId, 10) : 0;
                        
                        // Ambil item belum dibaca yang ID-nya lebih baru dari lastAlertedId
                        const newItems = unreadItems.filter(n => (parseInt(n.id) || 0) > lastAlertedId);

                        if (newItems.length > 0) {
                            const maxNewId = Math.max(...unreadItems.map(n => parseInt(n.id) || 0));
                            localStorage.setItem('simat_last_alerted_id', String(maxNewId));

                            this.playChimeSound();

                            const newest = newItems[0];
                            const notifTitle = newItems.length === 1 
                                ? newest.title 
                                : `(${newItems.length}) Notifikasi Baru SIMAT-RK`;
                            const notifMsg = newItems.length === 1
                                ? newest.message
                                : `${newest.title} dan ${newItems.length - 1} transaksi lainnya.`;

                            this.showDesktopToast(notifTitle, notifMsg, newest.link);
                        }
                    },

                    async refreshNotif() {
                        try {
                            const res = await fetch('{{ route('notifications.list') }}', {
                                headers: { 'Accept': 'application/json' }
                            });
                            const data = await res.json();
                            if (data.success) {
                                this.unreadCount = data.unread_count;
                                if (Array.isArray(data.notifications)) {
                                    this.checkAndAlertNew(data.notifications);
                                }
                            }
                        } catch (e) {}
                    },

                    async markAllAsRead() {
                        if (this.unreadCount === 0) return;
                        try {
                            const res = await fetch('{{ route('notifications.mark_all_read') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            });
                            const json = await res.json();
                            if (json.success) {
                                this.unreadCount = 0;
                                document.querySelectorAll('.notif-unread-dot').forEach(el => el.remove());
                                document.querySelectorAll('.notif-item-unread').forEach(el => el.classList.remove('bg-emerald-500/5', 'border-emerald-500/20'));
                            }
                        } catch (e) {
                            console.error('Gagal menandai notifikasi dibaca', e);
                        }
                    }
                };
            }
        </script>

        <!-- Custom Scrollbar Styling -->
        <style>
            .notif-scroll {
                scrollbar-width: thin;
                scrollbar-color: #475569 transparent;
            }
            .notif-scroll::-webkit-scrollbar {
                width: 6px;
            }
            .notif-scroll::-webkit-scrollbar-track {
                background: rgba(15, 23, 42, 0.6);
            }
            .notif-scroll::-webkit-scrollbar-thumb {
                background: #475569;
                border-radius: 99px;
            }
            .notif-scroll::-webkit-scrollbar-thumb:hover {
                background: #10b981;
            }
        </style>

        <!-- Divider -->
        <div class="h-5 w-px bg-slate-800 my-auto"></div>

        <!-- Profil Pengguna & Logout -->
        <div x-data="{ profileOpen: false }" class="relative">
            <button type="button" @click="profileOpen = !profileOpen"
                class="flex items-center space-x-2 p-1.5 rounded-xl hover:bg-slate-800/80 focus:outline-none transition-all">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white font-bold text-xs shadow-md">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>
                <div class="text-left hidden md:block">
                    <p class="text-xs font-bold text-white leading-tight truncate max-w-[120px]">{{ $user->name ?? 'User' }}</p>
                    <p class="text-[10px] text-emerald-400 font-medium capitalize">{{ str_replace('_', ' ', $role) }}</p>
                </div>
                <svg class="w-3.5 h-3.5 text-slate-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- Dropdown Profile -->
            <div x-show="profileOpen" x-cloak @click.away="profileOpen = false"
                 x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-52 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl overflow-hidden z-50 p-1.5">
                
                <div class="px-3 py-2 border-b border-slate-800 mb-1">
                    <p class="text-xs font-bold text-white truncate">{{ $user->name ?? 'User' }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ $user->email ?? '' }}</p>
                </div>

                @if(($role ?? '') === 'sub_admin')
                <a href="{{ route('subadmin.profile') }}"
                    class="w-full flex items-center space-x-2 px-3 py-2 rounded-xl text-xs font-semibold text-emerald-400 hover:bg-emerald-500/10 transition-colors mb-1 cursor-pointer text-left">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Ubah Email & Password</span>
                </a>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center space-x-2 px-3 py-2 rounded-xl text-xs font-semibold text-rose-400 hover:bg-rose-500/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Keluar Sistem</span>
                    </button>
                </form>
            </div>
        </div>

    </div>
</header>
