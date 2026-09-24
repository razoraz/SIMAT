<!-- ========================================================================= -->
<!-- FILTER BAR & PENCARIAN MASTER KEMITRAAN ASET (AKUN 1.5.2)                 -->
<!-- ========================================================================= -->
<div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-3">
    <form method="GET" action="{{ route('master.kemitraan') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        
        <!-- Filter Skema Kemitraan -->
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Skema Kemitraan</label>
            <select name="skema" onchange="this.form.submit()"
                class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                <option value="all" {{ ($filterSkema ?? 'all') === 'all' ? 'selected' : '' }}>-- Semua Skema --</option>
                <option value="KSO" {{ ($filterSkema ?? '') === 'KSO' ? 'selected' : '' }}>KSO (Kerja Sama Operasi)</option>
                <option value="BGS" {{ ($filterSkema ?? '') === 'BGS' ? 'selected' : '' }}>BGS (Bangun Guna Serah)</option>
                <option value="BSG" {{ ($filterSkema ?? '') === 'BSG' ? 'selected' : '' }}>BSG (Bangun Serah Guna)</option>
                <option value="KSP" {{ ($filterSkema ?? '') === 'KSP' ? 'selected' : '' }}>KSP (Kerja Sama Pemanfaatan)</option>
                <option value="Sewa" {{ ($filterSkema ?? '') === 'Sewa' ? 'selected' : '' }}>Sewa Fasilitas / Alkes</option>
            </select>
        </div>

        <!-- Filter Tahun -->
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Tahun Perolehan</label>
            <select name="tahun" onchange="this.form.submit()"
                class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                <option value="all" {{ ($filterTahun ?? 'all') === 'all' ? 'selected' : '' }}>-- Semua Tahun --</option>
                @for ($y = date('Y') + 1; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ ($filterTahun ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <!-- Filter Triwulan -->
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Periode Triwulan</label>
            <select name="triwulan" onchange="this.form.submit()"
                class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                <option value="all" {{ ($filterTw ?? 'all') === 'all' ? 'selected' : '' }}>-- Semua Triwulan --</option>
                <option value="TW I" {{ ($filterTw ?? '') === 'TW I' ? 'selected' : '' }}>TW I (Januari - Maret)</option>
                <option value="TW II" {{ ($filterTw ?? '') === 'TW II' ? 'selected' : '' }}>TW II (April - Juni)</option>
                <option value="TW III" {{ ($filterTw ?? '') === 'TW III' ? 'selected' : '' }}>TW III (Juli - September)</option>
                <option value="TW IV" {{ ($filterTw ?? '') === 'TW IV' ? 'selected' : '' }}>TW IV (Oktober - Desember)</option>
            </select>
        </div>

        <!-- Filter Status Konsesi -->
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status Konsesi</label>
            <select name="status" onchange="this.form.submit()"
                class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                <option value="all" {{ ($filterStatus ?? 'all') === 'all' ? 'selected' : '' }}>-- Semua Status --</option>
                <option value="Aktif" {{ ($filterStatus ?? '') === 'Aktif' ? 'selected' : '' }}>🟢 Aktif Berjalan</option>
                <option value="Selesai / Reklasifikasi" {{ ($filterStatus ?? '') === 'Selesai / Reklasifikasi' ? 'selected' : '' }}>🔵 Siap Reklasifikasi</option>
                <option value="Dihentikan" {{ ($filterStatus ?? '') === 'Dihentikan' ? 'selected' : '' }}>🔴 Dihentikan</option>
            </select>
        </div>

        <!-- Search Box -->
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Pencarian Barang / PKS</label>
            <div class="relative">
                <input type="text" name="search" value="{{ $search ?? '' }}"
                    placeholder="Nama alkes, mitra, no. PKS..."
                    class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl pl-8 pr-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

    </form>
</div>
