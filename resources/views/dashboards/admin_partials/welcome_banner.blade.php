<!-- Welcome Banner Card -->
<div class="bg-gradient-to-r from-cyan-500/10 via-slate-900 to-slate-900 border border-cyan-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-8 relative overflow-hidden">
    <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-cyan-500/10 rounded-full blur-2xl pointer-events-none"></div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative z-10">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-bold mb-3">
                <span>🛡️ ADMIN OPERASIONAL - OTORISASI OPERASIONAL (CRUD)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Selamat Datang, {{ Auth::user()->name }}!</h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                Anda berada di Panel Admin Operasional. Wewenang Anda meliputi pembuatan Distribusi ASTAP, Mutasi Aset, Berita Acara (BAST), pembuatan akun User, serta Update/Delete Data ASTAP (status kondisi), Distribusi, User, dan BAST.
            </p>
        </div>
        
        <!-- Admin Fast Create Action -->
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('distribusi.create') }}" class="px-3.5 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Distribusi ASTAP Baru</span>
            </a>
        </div>
    </div>
</div>
