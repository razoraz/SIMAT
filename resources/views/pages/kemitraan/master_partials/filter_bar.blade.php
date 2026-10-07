<!-- ========================================================================= -->
<!-- FILTER BAR & PENCARIAN MASTER KEMITRAAN ASET (AKUN 1.5.2)                 -->
<!-- ========================================================================= -->
<div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-4">
    <form method="GET" action="{{ route('master.kemitraan') }}" class="space-y-4">
        
        <!-- Baris Atas: Live Search Bar + Tombol Cari & Tombol Refresh / Reset Filter -->
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full">
            <div class="relative flex-1 w-full">
                <input type="text" name="search" value="{{ $search ?? '' }}"
                    placeholder="Cari nama barang / rekanan mitra / nomor PKS kemitraan..."
                    class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-2.5 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition-all">
                <svg class="w-4 h-4 text-cyan-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div class="flex items-center space-x-2 shrink-0 self-end sm:self-auto">
                <button type="submit"
                    class="px-4 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold transition-all shadow-md shadow-cyan-500/20 cursor-pointer flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cari</span>
                </button>

                <a href="{{ route('master.kemitraan') }}"
                    title="Muat Ulang / Reset Seluruh Filter"
                    class="group/rf inline-flex items-center space-x-1.5 px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold border border-slate-700 transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-cyan-400 group-hover/rf:rotate-180 transition-all duration-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Reset Filter</span>
                </a>
            </div>
        </div>

        <!-- Baris Bawah: 4 Dropdown Filter (Skema, Tahun, Triwulan, Status Konsesi) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-3 border-t border-slate-800/80">
            <!-- Filter Skema Kemitraan -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Skema Kemitraan</label>
                <select name="skema" onchange="this.form.submit()"
                    class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    <option value="all" {{ ($filterSkema ?? 'all') === 'all' ? 'selected' : '' }}>-- Semua Skema --</option>
                    <option value="Sewa" {{ ($filterSkema ?? '') === 'Sewa' ? 'selected' : '' }}>Sewa (1.5.2.01.01.01)</option>
                    <option value="KSP" {{ ($filterSkema ?? '') === 'KSP' ? 'selected' : '' }}>KSP - Kerja Sama Pemanfaatan (1.5.2.01.01.02)</option>
                    <option value="BGS/BSG" {{ ($filterSkema ?? '') === 'BGS/BSG' || ($filterSkema ?? '') === 'BSG' ? 'selected' : '' }}>BGS / BSG (1.5.2.01.01.03)</option>
                    <option value="KSPI" {{ ($filterSkema ?? '') === 'KSPI' ? 'selected' : '' }}>KSPI - Infrastruktur (1.5.2.01.01.04)</option>
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
                    <option value="Konsesi Berakhir" {{ ($filterStatus ?? '') === 'Konsesi Berakhir' ? 'selected' : '' }}>🟠 Konsesi Berakhir (Siap Reklas)</option>
                    <option value="Selesai / Reklasifikasi" {{ ($filterStatus ?? '') === 'Selesai / Reklasifikasi' ? 'selected' : '' }}>🔵 Selesai Direklasifikasi</option>
                    <option value="Dihentikan" {{ ($filterStatus ?? '') === 'Dihentikan' ? 'selected' : '' }}>🔴 Dihentikan / Dibatalkan</option>
                </select>
            </div>
        </div>

    </form>
</div>
