<x-layout title="Pengaturan Akun Sub Admin - SIMAT-RK">
    @section('page-title', 'Pengaturan Akun Ruangan')
    @section('breadcrumb', 'Profil / Pengaturan Akun Sub Admin')

    @php
        $user = Auth::user();
        $unit = $user->unit_id ? \App\Models\Unit::find($user->unit_id) : null;
    @endphp

    <style>
        /* Anti-White Background Browser Autofill in Dark Mode */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #0f172a inset !important;
            -webkit-text-fill-color: #f8fafc !important;
            caret-color: #f8fafc !important;
            transition: background-color 5000s ease-in-out 0s !important;
            border-color: #334155 !important;
        }
    </style>

    <div class="max-w-4xl mx-auto space-y-6 pb-12"
         x-data="{
            choice: 'email',
            email: '{{ old('email', $user->email ?? '') }}',
            password: '',
            password_confirmation: '',
            showPass: false,
            showConfirmPass: false,
            isSubmitting: false
         }">

        <!-- 1. HEADER BANNER MEWAH -->
        <div class="bg-gradient-to-r from-emerald-600/20 via-slate-900 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
            <!-- Glow background decor -->
            <div class="absolute -right-10 -bottom-10 w-56 h-56 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-10 -top-10 w-48 h-48 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 relative z-10">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-500/20 to-teal-500/30 border border-emerald-500/40 flex items-center justify-center text-emerald-300 font-black text-2xl shadow-xl shadow-emerald-950/50 shrink-0">
                        {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full font-mono text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 tracking-wider">
                                🛡️ SUB ADMIN
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full font-mono text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/40">
                                {{ $unit->kode_unit ?? 'UNIT' }}
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-white mt-1.5 tracking-tight">{{ $user->name ?? 'Sub Admin' }}</h1>
                        <p class="text-xs text-slate-300 mt-0.5">Penanggung Jawab: <strong class="text-emerald-400">{{ $unit->nama ?? 'Unit Ruangan' }}</strong></p>
                    </div>
                </div>

                <a href="{{ route('subadmin.dashboard') }}" 
                    class="px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700 shadow-md transition-all flex items-center justify-center space-x-2 shrink-0 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Dashboard</span>
                </a>
            </div>
        </div>

        <!-- 2. KARTU INFORMASI AKUN (INFO BOX) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 bg-slate-900/90 border border-slate-800 rounded-2xl flex items-center space-x-3.5 shadow-lg">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-lg shrink-0">
                    🏥
                </div>
                <div class="min-w-0">
                    <span class="text-slate-500 text-[10px] uppercase font-bold block">Ruangan</span>
                    <p class="text-white font-bold text-xs truncate">{{ $unit->nama ?? '-' }}</p>
                    <span class="text-[10px] text-slate-400 font-mono">{{ $unit->tipe ?? 'Unit RSUD' }}</span>
                </div>
            </div>

            <div class="p-4 bg-slate-900/90 border border-slate-800 rounded-2xl flex items-center space-x-3.5 shadow-lg">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-lg shrink-0">
                    ✉️
                </div>
                <div class="min-w-0">
                    <span class="text-slate-500 text-[10px] uppercase font-bold block">Email Login</span>
                    <p class="text-emerald-400 font-mono font-bold text-xs truncate">{{ $user->email ?? '-' }}</p>
                    <span class="text-[10px] text-slate-400">Akun Resmi Aktif</span>
                </div>
            </div>

            <div class="p-4 bg-slate-900/90 border border-slate-800 rounded-2xl flex items-center space-x-3.5 shadow-lg">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 text-lg shrink-0">
                    🔒
                </div>
                <div class="min-w-0">
                    <span class="text-slate-500 text-[10px] uppercase font-bold block">Keamanan</span>
                    <p class="text-cyan-300 font-bold text-xs truncate">Bcrypt Hash Standar</p>
                    <span class="text-[10px] text-slate-400">Terenkripsi Aman</span>
                </div>
            </div>
        </div>

        <!-- 3. PILIHAN INTERAKTIF SEBELUM MERUBAH (PILIH APA YANG INGIN DIUBAH) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <div>
                <h3 class="text-sm sm:text-base font-black text-white flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Pilih Bagian yang Ingin Anda Ubah:</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    Tentukan tindakan yang ingin dilakukan sebelum mengisi formulir di bawah ini.
                </p>
            </div>

            <!-- 3 Kartu Pilihan / Options -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                <!-- Pilihan 1: Ubah Email Saja -->
                <button type="button" @click="choice = 'email'"
                    :class="choice === 'email' 
                        ? 'bg-gradient-to-b from-emerald-500/20 to-emerald-950/40 border-emerald-500 ring-2 ring-emerald-500/40 shadow-xl shadow-emerald-950/50 text-white' 
                        : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:border-slate-700 hover:bg-slate-900/60'"
                    class="p-4 sm:p-5 rounded-2xl border text-left transition-all duration-200 relative flex flex-col justify-between cursor-pointer group">
                    
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg transition-transform group-hover:scale-110"
                             :class="choice === 'email' ? 'bg-emerald-500/30 text-emerald-300 border border-emerald-500/50' : 'bg-slate-800 text-slate-400 border border-slate-700/50'">
                            ✉️
                        </div>
                        <div x-show="choice === 'email'" x-cloak class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center text-slate-950 text-xs font-black shadow-md">
                            ✓
                        </div>
                    </div>
                    
                    <div>
                        <span class="block font-black text-sm text-white group-hover:text-emerald-300 transition-colors">
                            Ubah Email Saja
                        </span>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                            Perbarui alamat Gmail login akun ruangan.
                        </p>
                    </div>
                </button>

                <!-- Pilihan 2: Ubah Password Saja -->
                <button type="button" @click="choice = 'password'"
                    :class="choice === 'password' 
                        ? 'bg-gradient-to-b from-emerald-500/20 to-emerald-950/40 border-emerald-500 ring-2 ring-emerald-500/40 shadow-xl shadow-emerald-950/50 text-white' 
                        : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:border-slate-700 hover:bg-slate-900/60'"
                    class="p-4 sm:p-5 rounded-2xl border text-left transition-all duration-200 relative flex flex-col justify-between cursor-pointer group">
                    
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg transition-transform group-hover:scale-110"
                             :class="choice === 'password' ? 'bg-emerald-500/30 text-emerald-300 border border-emerald-500/50' : 'bg-slate-800 text-slate-400 border border-slate-700/50'">
                            🔒
                        </div>
                        <div x-show="choice === 'password'" x-cloak class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center text-slate-950 text-xs font-black shadow-md">
                            ✓
                        </div>
                    </div>
                    
                    <div>
                        <span class="block font-black text-sm text-white group-hover:text-emerald-300 transition-colors">
                            Ubah Password Saja
                        </span>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                            Ganti kata sandi login baru ruangan.
                        </p>
                    </div>
                </button>

                <!-- Pilihan 3: Ubah Keduanya -->
                <button type="button" @click="choice = 'both'"
                    :class="choice === 'both' 
                        ? 'bg-gradient-to-b from-emerald-500/20 to-emerald-950/40 border-emerald-500 ring-2 ring-emerald-500/40 shadow-xl shadow-emerald-950/50 text-white' 
                        : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:border-slate-700 hover:bg-slate-900/60'"
                    class="p-4 sm:p-5 rounded-2xl border text-left transition-all duration-200 relative flex flex-col justify-between cursor-pointer group">
                    
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg transition-transform group-hover:scale-110"
                             :class="choice === 'both' ? 'bg-emerald-500/30 text-emerald-300 border border-emerald-500/50' : 'bg-slate-800 text-slate-400 border border-slate-700/50'">
                            ⚡
                        </div>
                        <div x-show="choice === 'both'" x-cloak class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center text-slate-950 text-xs font-black shadow-md">
                            ✓
                        </div>
                    </div>
                    
                    <div>
                        <span class="block font-black text-sm text-white group-hover:text-emerald-300 transition-colors">
                            Ubah Email & Password
                        </span>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                            Perbarui alamat Gmail dan password bersamaan.
                        </p>
                    </div>
                </button>
            </div>
        </div>

        <!-- 4. FORMULIR PENGATURAN KREDENSIAL SESUAI PILIHAN -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
            
            <!-- Hiasan Cahaya Ambient Glow Background Halus -->
            <div class="absolute -right-12 -top-12 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-12 -bottom-12 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Form Header Dinamis dengan Jarak Garis Bawah yang Longgar ("Bawahin Dikit") -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-800 mb-6 gap-3 relative z-10">
                <div>
                    <h3 class="text-lg sm:text-xl font-black text-white flex items-center space-x-2.5">
                        <span class="p-2 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm">⚙️</span>
                        <span x-text="choice === 'email' ? 'Formulir Pembaruan Email Ruangan' : (choice === 'password' ? 'Formulir Ganti Password Baru' : 'Formulir Pembaruan Email & Password')"></span>
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-400 mt-2.5 leading-relaxed"
                       x-text="choice === 'email' ? 'Masukkan alamat Gmail baru untuk akun ruangan Anda.' : (choice === 'password' ? 'Buat kata sandi baru minimal 4 karakter untuk akses akun ruangan Anda.' : 'Perbarui alamat Gmail dan masukkan kata sandi baru untuk akun ruangan.')">
                    </p>
                </div>

                <!-- Hiasan Chip Status Mode Aktif -->
                <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-slate-950/80 border border-slate-800 text-emerald-400 text-xs font-semibold self-start sm:self-auto shadow-inner">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span x-text="choice === 'email' ? 'Mode Ubah Email' : (choice === 'password' ? 'Mode Ubah Password' : 'Mode Ubah Lengkap')"></span>
                </div>
            </div>

            <!-- Notifikasi Sukses / Feedback -->
            @if (session('success'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/15 border border-emerald-500/40 text-emerald-300 text-xs shadow-md flex items-center space-x-2.5 relative z-10">
                    <span class="text-base">🎉</span>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Notifikasi Error Validasi Server -->
            @if ($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-500/15 border border-rose-500/40 text-rose-300 text-xs shadow-md relative z-10">
                    <p class="font-bold flex items-center space-x-2 mb-1.5 text-xs">
                        <span>⚠️</span>
                        <span>Ada kesalahan pada isian form:</span>
                    </p>
                    <ul class="list-disc pl-5 space-y-0.5 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('subadmin.profile.update') }}" @submit="isSubmitting = true" class="space-y-4 relative z-10">
                @csrf
                <input type="hidden" name="change_type" :value="choice">

                <!-- HIASAN KARTU INFORMASI FITUR AGAR FORMULIR TIDAK SEPI -->
                <div x-show="choice === 'email' || choice === 'both'" 
                     class="p-3.5 rounded-xl bg-gradient-to-r from-emerald-500/10 via-slate-950/60 to-slate-900/30 border border-emerald-500/20 flex items-center justify-between text-xs shadow-sm">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-300 shrink-0 text-sm shadow-sm">
                            ✨
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-white text-xs">Email Resmi Unit Ruangan</p>
                            <p class="text-slate-400 text-[11px] truncate">Digunakan untuk akses login dan identitas kontak ruangan.</p>
                        </div>
                    </div>
                    <span class="hidden sm:inline-flex items-center space-x-1 text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 shrink-0 ml-2">
                        <span>🛡️</span> <span>Terproteksi</span>
                    </span>
                </div>

                <div x-show="choice === 'password'" 
                     x-cloak
                     class="p-3.5 rounded-xl bg-gradient-to-r from-cyan-500/10 via-slate-950/60 to-slate-900/30 border border-cyan-500/20 flex items-center justify-between text-xs shadow-sm">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-cyan-500/20 border border-cyan-500/30 flex items-center justify-center text-cyan-300 shrink-0 text-sm shadow-sm">
                            🔒
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-white text-xs">Keamanan Sandi Terenkripsi</p>
                            <p class="text-slate-400 text-[11px] truncate">Kata sandi baru dienkripsi standar tinggi untuk integritas data.</p>
                        </div>
                    </div>
                    <span class="hidden sm:inline-flex items-center space-x-1 text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 shrink-0 ml-2">
                        <span>⚡</span> <span>Bcrypt</span>
                    </span>
                </div>

                <!-- SEKSI 1: ALAMAT GMAIL / EMAIL LOGIN (Tampil jika pilihan 'email' atau 'both') -->
                <div x-show="choice === 'email' || choice === 'both'" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-slate-950/70 p-5 sm:p-6 rounded-2xl border border-slate-800/90 shadow-inner space-y-3">
                    <label for="email" class="block text-xs sm:text-sm font-bold text-slate-200">
                        Alamat Gmail / Email Login Akun <span class="text-rose-400">*</span>
                    </label>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                            <span class="text-base font-bold font-mono text-emerald-500/80">@</span>
                        </div>
                        <input type="email" id="email" name="email" x-model="email" :required="choice === 'email' || choice === 'both'"
                            value="{{ old('email', $user->email ?? '') }}"
                            placeholder="nama.ruangan@gmail.com"
                            autocomplete="off"
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-4 py-3 pl-11 pr-4 text-sm font-mono text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm">
                    </div>
                </div>

                <!-- SEKSI 2: KATA SANDI / PASSWORD (Tampil jika pilihan 'password' atau 'both') -->
                <div x-show="choice === 'password' || choice === 'both'"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-slate-950/70 p-5 sm:p-6 rounded-2xl border border-slate-800/90 shadow-inner space-y-4">
                    
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs sm:text-sm font-bold text-slate-200">
                            Password Baru <span x-show="choice === 'password'" class="text-rose-400">*</span>
                        </label>
                        <span class="text-xs text-slate-400 font-medium bg-slate-800/80 px-2.5 py-0.5 rounded-md border border-slate-700/50"
                              x-text="choice === 'password' ? 'Wajib diisi' : 'Opsional jika tidak diganti'">
                        </span>
                    </div>

                    <!-- Input Password Baru dengan Ikon Mata (Show/Hide) -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        
                        <input :type="showPass ? 'text' : 'password'" id="password" name="password" x-model="password" minlength="4" :required="choice === 'password'"
                            placeholder="Ketik kata sandi baru..."
                            autocomplete="new-password"
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-4 py-3 pl-11 pr-12 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-mono tracking-wide shadow-sm">

                        <!-- Tombol Ikon Mata Interaktif -->
                        <button type="button" @click="showPass = !showPass"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-emerald-400 transition-colors focus:outline-none cursor-pointer"
                            tabindex="-1"
                            title="Tampilkan / Sembunyikan Password">
                            <!-- Mata Terbuka -->
                            <svg x-show="showPass" x-cloak class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <!-- Mata Dicoret / Tertutup -->
                            <svg x-show="!showPass" class="w-5 h-5 text-slate-400 hover:text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-slate-400">Minimal 4 karakter kombinasi huruf, angka, atau simbol.</p>

                    <!-- Input Konfirmasi Password Baru dengan Ikon Mata (Show/Hide) -->
                    <div x-show="choice === 'password' || password.length > 0" x-cloak class="pt-3 border-t border-slate-800/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="password_confirmation" class="block text-xs sm:text-sm font-bold text-slate-200">
                                Ulangi Password Baru <span class="text-rose-400">*</span>
                            </label>
                            
                            <!-- Indikator Kecocokan Password Real-Time -->
                            <div class="text-xs font-semibold">
                                <span x-show="password_confirmation.length > 0 && password === password_confirmation" class="text-emerald-400 flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Password Cocok</span>
                                </span>
                                <span x-show="password_confirmation.length > 0 && password !== password_confirmation" class="text-rose-400 flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Belum Cocok</span>
                                </span>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            
                            <input :type="showConfirmPass ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" x-model="password_confirmation"
                                placeholder="Ulangi password baru untuk verifikasi..."
                                autocomplete="new-password"
                                class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl px-4 py-3 pl-11 pr-12 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-mono tracking-wide shadow-sm">

                            <!-- Tombol Ikon Mata Konfirmasi -->
                            <button type="button" @click="showConfirmPass = !showConfirmPass"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-emerald-400 transition-colors focus:outline-none cursor-pointer"
                                tabindex="-1"
                                title="Tampilkan / Sembunyikan Password">
                                <svg x-show="showConfirmPass" x-cloak class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="!showConfirmPass" class="w-5 h-5 text-slate-400 hover:text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Garis Pembatas Horisontal -->
                <div class="border-t border-slate-800/80 pt-6 mt-8"></div>

                <!-- SEKSI TOMBOL AKSI (DI BAWAH GARIS) -->
                <div class="flex items-center justify-end space-x-3 pb-2">
                    <a href="{{ route('subadmin.dashboard') }}"
                        class="px-5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700 shadow transition-all cursor-pointer">
                        Batal
                    </a>
                    <button type="submit" 
                        :disabled="isSubmitting || (choice === 'password' && (!password || password.length < 4 || password !== password_confirmation)) || (choice === 'email' && !email)"
                        class="px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 transition-all flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer active:scale-95">
                        <svg x-show="!isSubmitting" class="w-4 h-4 text-slate-950 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg x-show="isSubmitting" x-cloak class="w-4 h-4 animate-spin text-slate-950 shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span class="tracking-wide" x-text="isSubmitting ? 'Menyimpan...' : (choice === 'email' ? 'Simpan Perubahan Email' : (choice === 'password' ? 'Simpan Password Baru' : 'Simpan Email & Password'))"></span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-layout>
