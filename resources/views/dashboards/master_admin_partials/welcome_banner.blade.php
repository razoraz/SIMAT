<!-- Welcome Banner Card -->
<div class="bg-gradient-to-r from-amber-500/10 via-slate-900 to-slate-900 border border-amber-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-8 relative overflow-hidden">
    <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>
    <div class="flex flex-col sm:flex-row items-center sm:items-center justify-center gap-4 relative z-10 text-center">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold mb-3">
                <span>👑 MASTER ADMIN - HAK WEWENANG PENUH (CRUD)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                Selamat Datang, {{ Auth::user()->name }}!
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                Anda berada di Panel Master Admin. Anda memiliki wewenang penuh (Create, Read, Update, Delete) untuk
                manajemen fitur master utama, master aset, dan master data sistem.
            </p>
        </div>
    </div>
</div>
