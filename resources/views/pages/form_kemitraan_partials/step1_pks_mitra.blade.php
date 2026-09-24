<!-- ========================================================================= -->
<!-- LANGKAH 1: DOKUMEN PKS & MITRA REKANAN PIHAK KETIGA (AKUN 1.5.2)          -->
<!-- ========================================================================= -->
<div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-400/10 text-cyan-300 border border-cyan-400/20 text-xs font-bold mb-2">
            <span>🤝 LANGKAH 1 DARI 3: LEGALITAS PERJANJIAN KERJA SAMA (PKS)</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl bg-cyan-400/10 text-cyan-400 text-sm">📜</span>
            <span>Langkah 1: Dokumen PKS &amp; Identitas Mitra Rekanan</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">
            Masukkan legalitas perjanjian kerja sama (PKS/MoU/KSO), identitas pihak ketiga/mitra rekanan, masa berlaku kerjasama, serta periode pembukuan (Akun 1.5.2).
        </p>
    </div>

    <!-- Bagian PKS & Mitra Pihak Ketiga -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-cyan-500/30 space-y-5 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>🤝 Identitas Mitra &amp; Legalitas Dokumen PKS</span>
                <span class="text-rose-400">*</span>
            </span>
            <span class="text-[10px] font-bold text-cyan-300 bg-cyan-400/10 px-2 py-0.5 rounded-lg border border-cyan-400/20">
                Aset Kemitraan · Akun 1.5.2
            </span>
        </div>

        <!-- Nama Mitra / Rekanan Pihak Ketiga -->
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Nama Perusahaan Mitra / Rekanan Pihak Ketiga <span class="text-rose-400">*</span>
            </label>
            <input type="text" x-model="formData.mitra_nama" required
                placeholder="Contoh: PT. Roche Indonesia / PT. Fresenius Medical Care / CV. Medika Nusantara..."
                class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none font-bold transition-colors">
            
            <!-- Rekomendasi Cepat Mitra -->
            <div class="mt-2.5 flex flex-wrap items-center gap-1.5">
                <span class="text-[10px] text-slate-500 font-semibold mr-1">Rekomendasi Cepat:</span>
                <button type="button" @click="formData.mitra_nama = 'PT. Roche Indonesia'"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer transition-colors">
                    PT. Roche Indonesia
                </button>
                <button type="button" @click="formData.mitra_nama = 'PT. Fresenius Medical Care Indonesia'"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer transition-colors">
                    PT. Fresenius Medical Care
                </button>
                <button type="button" @click="formData.mitra_nama = 'PT. Kimia Farma Diagnostika'"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer transition-colors">
                    PT. Kimia Farma Diagnostika
                </button>
                <button type="button" @click="formData.mitra_nama = 'PT. Sysmex Indonesia'"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer transition-colors">
                    PT. Sysmex Indonesia
                </button>
                <button type="button" @click="formData.mitra_nama = 'Mitra Swasta Pengembang (BGS)'"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer transition-colors">
                    Mitra Pengembang (BGS)
                </button>
            </div>
        </div>

        <!-- Nomor & Tanggal PKS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nomor Dokumen Perjanjian Kerja Sama (PKS / MoU) <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.nomor_pks" required
                    placeholder="Contoh: 000.2.3.2/PKS-KSO/430.10.7/2026"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tanggal Penandatanganan PKS <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-datepicker x-model="formData.tanggal_pks" required
                    placeholder="dd/mm/yyyy"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>
        </div>

        <!-- Skema Bentuk Kemitraan (Radio Badges) -->
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Bentuk Skema Kemitraan Sesuai Permendagri 108 / SAP
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                <button type="button" @click="formData.skema_kemitraan = 'KSO'"
                    class="p-2.5 rounded-xl border text-center transition-all cursor-pointer"
                    :class="formData.skema_kemitraan === 'KSO' ? 'bg-cyan-500/20 border-cyan-400 text-cyan-300 font-black shadow-lg shadow-cyan-500/20' : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'">
                    <span class="block text-xs font-bold">KSO</span>
                    <span class="block text-[9px] text-slate-400 mt-0.5">Kerja Sama Operasi</span>
                </button>
                <button type="button" @click="formData.skema_kemitraan = 'BGS'"
                    class="p-2.5 rounded-xl border text-center transition-all cursor-pointer"
                    :class="formData.skema_kemitraan === 'BGS' ? 'bg-cyan-500/20 border-cyan-400 text-cyan-300 font-black shadow-lg shadow-cyan-500/20' : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'">
                    <span class="block text-xs font-bold">BGS</span>
                    <span class="block text-[9px] text-slate-400 mt-0.5">Bangun Guna Serah</span>
                </button>
                <button type="button" @click="formData.skema_kemitraan = 'KSP'"
                    class="p-2.5 rounded-xl border text-center transition-all cursor-pointer"
                    :class="formData.skema_kemitraan === 'KSP' ? 'bg-cyan-500/20 border-cyan-400 text-cyan-300 font-black shadow-lg shadow-cyan-500/20' : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'">
                    <span class="block text-xs font-bold">KSP</span>
                    <span class="block text-[9px] text-slate-400 mt-0.5">Kerja Sama Pemanfaatan</span>
                </button>
                <button type="button" @click="formData.skema_kemitraan = 'BSG'"
                    class="p-2.5 rounded-xl border text-center transition-all cursor-pointer"
                    :class="formData.skema_kemitraan === 'BSG' ? 'bg-cyan-500/20 border-cyan-400 text-cyan-300 font-black shadow-lg shadow-cyan-500/20' : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'">
                    <span class="block text-xs font-bold">BSG</span>
                    <span class="block text-[9px] text-slate-400 mt-0.5">Bangun Serah Guna</span>
                </button>
                <button type="button" @click="formData.skema_kemitraan = 'Sewa'"
                    class="p-2.5 rounded-xl border text-center transition-all cursor-pointer"
                    :class="formData.skema_kemitraan === 'Sewa' ? 'bg-cyan-500/20 border-cyan-400 text-cyan-300 font-black shadow-lg shadow-cyan-500/20' : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'">
                    <span class="block text-xs font-bold">Sewa</span>
                    <span class="block text-[9px] text-slate-400 mt-0.5">Sewa Barang/Alat</span>
                </button>
            </div>
        </div>

        <!-- Masa Berlaku Kerja Sama (Mulai s.d. Selesai) -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3">
            <span class="text-xs font-bold text-slate-200 block">
                🗓️ Jangka Waktu / Masa Berlaku Kerjasama (Konsesi)
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 mb-1">
                        Tanggal Mulai Berlaku Kerjasama
                    </label>
                    <input type="text" x-datepicker x-model="formData.tanggal_mulai"
                        placeholder="dd/mm/yyyy"
                        class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 mb-1">
                        Tanggal Berakhir Kerjasama (Konsesi Berakhir)
                    </label>
                    <input type="text" x-datepicker x-model="formData.tanggal_selesai"
                        placeholder="dd/mm/yyyy"
                        class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                </div>
            </div>
            <p class="text-[10.5px] text-cyan-400/80 italic">
                ℹ️ Catatan: Selama masa konsesi berlangsung, aset ini dibukukan pada Akun Neraca 1.5.2 (Aset Kemitraan Pihak Ketiga). Setelah konsesi berakhir, aset dapat diserahkan penuh menjadi Aset Tetap RSUD Koesnandi melalui Reklasifikasi.
            </p>
        </div>

        <!-- Tahun Perolehan & Triwulan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tahun Pembukuan / Mulai Operasional <span class="text-rose-400">*</span>
                </label>
                <input type="number" x-model.number="formData.tahun_perolehan" required min="1990" max="2100"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-bold">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Periode Triwulan Pembukuan <span class="text-rose-400">*</span>
                </label>
                <select x-model="formData.triwulan" required
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-bold">
                    <option value="TW I">TW I (Januari - Maret)</option>
                    <option value="TW II">TW II (April - Juni)</option>
                    <option value="TW III">TW III (Juli - September)</option>
                    <option value="TW IV">TW IV (Oktober - Desember)</option>
                </select>
            </div>
        </div>

        <!-- Ruang Lingkup & Keterangan Kerjasama -->
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Ruang Lingkup / Keterangan Kerjasama
            </label>
            <textarea x-model="formData.kemitraan_keterangan" rows="2"
                placeholder="Contoh: Kerja Sama Operasional (KSO) penempatan alat laboratorium analyzer dengan skema komitmen reagen, atau Bangun Guna Serah gedung parkir..."
                class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl p-3 text-xs text-white focus:outline-none"></textarea>
        </div>

    </div>

</div>
