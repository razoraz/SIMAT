<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMAT-RK RSUD Dr. H. Koesnandi Bondowoso</title>

    <!-- Favicon Logo RSUD -->
    <link rel="icon" type="image/png" href="{{ asset('img/Logo-rsud/logo-rsud.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/Logo-rsud/logo-rsud.png') }}">

    <!-- Tailwind CSS (Vite) & Alpine.js -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Ambient Glowing Backgrounds (Medical Emerald & Cyan Blobs) -->
    <div class="absolute -top-36 -left-36 w-96 h-96 bg-emerald-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-36 -right-36 w-96 h-96 bg-teal-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div x-data="{ showPassword: false }" class="w-full max-w-md relative z-10">

        <!-- Card Login SIMAT-RK -->
        <div class="bg-slate-900/90 backdrop-blur-2xl border border-slate-800 rounded-3xl shadow-2xl p-6 sm:p-8">
            
            <!-- Logo Emblem RSUD & Header SIMAT-RK -->
            <div class="text-center mb-7">
                <div class="inline-flex items-center justify-center p-2 rounded-3xl bg-slate-950/60 shadow-2xl shadow-emerald-500/20 mb-4 border border-emerald-500/30 relative group">
                    <!-- Ring Glow Outer -->
                    <div class="absolute inset-0 rounded-3xl bg-emerald-400/20 blur-md group-hover:blur-lg transition-all pointer-events-none"></div>
                    
                    <!-- Official RSUD Logo Image -->
                    <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD Dr. H. Koesnandi" class="w-16 h-16 object-contain relative z-10 drop-shadow-md">
                </div>
                <h1 class="text-2xl font-black text-white tracking-tight">SIMAT-RK</h1>
                <p class="text-xs font-semibold text-emerald-400 tracking-wide mt-1">Sistem Informasi Manajemen Terpadu</p>
                <p class="text-[11px] text-slate-400 mt-0.5">RSUD Dr. H. Koesnandi Bondowoso</p>
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="mb-5 p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-start space-x-2.5 shadow-sm">
                    <svg class="w-4 h-4 mt-0.5 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Session Success Alert -->
            @if (session('success'))
                <div class="mb-5 p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs flex items-center space-x-2.5 shadow-sm">
                    <svg class="w-4 h-4 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Form Login Simpel & Cepat -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Input Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-300 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" x-ref="emailInput" required
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 pl-10 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all duration-200"
                            placeholder="nama@asimat.com" value="{{ old('email') }}">
                        <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                    </div>
                </div>

                <!-- Input Password -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-300 mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" x-ref="passwordInput" required
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 pl-10 pr-10 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all duration-200"
                            placeholder="••••••••">
                        <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        
                        <!-- Toggle Password Button -->
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-3.5 top-3.5 text-slate-500 hover:text-slate-300 focus:outline-none transition-colors">
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 013.682-.773c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692-4.692a3 3 0 00-4.243-4.243"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center space-x-2 text-slate-400 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="rounded bg-slate-950 border-slate-800 text-teal-600 focus:ring-0 focus:ring-offset-0">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 hover:from-emerald-500 hover:to-cyan-500 text-white font-bold text-sm shadow-xl shadow-teal-600/25 hover:shadow-teal-500/40 transition-all duration-200 transform active:scale-[0.99]">
                    Masuk ke SIMAT-RK
            </form>

        </div>

        <!-- Footer Info -->
        <p class="text-center text-xs text-slate-500 mt-4">&copy; {{ date('Y') }} RSUD Dr. H. Koesnandi Bondowoso. All rights reserved.</p>
    </div>

</body>
</html>
