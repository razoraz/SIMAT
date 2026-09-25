<!-- ========================================================================= -->
<!-- TABEL REKENING BELANJA UNTUK PENGADAAN SIPD                               -->
<!-- ========================================================================= -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-5 mb-6">
    <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 350px; overflow-y: auto; overflow-x: auto;">
        <table class="w-full text-left text-xs text-slate-300 border-collapse">
            
            <!-- 2-Tier Header Sesuai Format Gambar -->
            <thead class="shadow-sm" style="position: sticky; top: 0; z-index: 10; background-color: #020617;">
                <!-- Baris 1: Header Utama -->
                <tr class="text-center font-extrabold uppercase tracking-wider text-xs border-b border-slate-800">
                    <th rowspan="2" class="px-4 py-3 bg-slate-950/90 text-slate-300 border-r border-slate-800 w-12 text-center align-middle">
                        NO
                    </th>
                    <th colspan="2" class="px-4 py-3 bg-blue-950/60 text-blue-300 border-r border-slate-800">
                        <div class="flex items-center justify-center space-x-2">
                            <span>💳</span>
                            <span>Rekening Belanja Untuk Pengadaan SIPD</span>
                        </div>
                    </th>
                    <th rowspan="2" class="px-4 py-3 bg-slate-950/90 text-slate-300 border-r border-slate-800 text-center align-middle w-48">
                        Kelompok Akun Belanja
                    </th>
                    <th rowspan="2" class="px-3 py-3 bg-blue-950/80 text-blue-300 text-center align-middle whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[190px] w-[190px]" style="position: sticky; right: 0; top: 0; z-index: 30; background-color: #0f1d38 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                        <div class="flex items-center justify-center space-x-1.5 font-extrabold uppercase tracking-wider">
                            <span>⚡</span>
                            <span>AKSI</span>
                        </div>
                    </th>
                </tr>

                <!-- Baris 2: Sub Kolom (Kode Rek & Nama Belanja Pengadaan) -->
                <tr class="text-center font-bold uppercase tracking-wider text-[11px] border-b border-slate-800">
                    <th class="px-4 py-2.5 bg-blue-950/40 text-blue-400 border-r border-slate-800/80 w-48 text-center">
                        Kode Rek
                    </th>
                    <th class="px-6 py-2.5 bg-blue-950/40 text-blue-200 border-r border-slate-800/80 min-w-[320px] text-left">
                        Nama Belanja Pengadaan
                    </th>
                </tr>
            </thead>

            <!-- Body Tabel -->
            <tbody class="divide-y divide-slate-800/80">
                @forelse ($rekeningList as $index => $item)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <!-- Kolom 1: No -->
                        <td class="px-4 py-4 text-center font-bold text-slate-400 border-r border-slate-800/80">{{ $index + 1 }}</td>

                        <!-- Kolom 2: Kode Rek -->
                        <td class="px-4 py-4 text-center font-mono font-bold text-blue-400 bg-blue-950/10 border-r border-slate-800/80">{{ $item->kode_rek }}</td>

                        <!-- Kolom 3: Nama Belanja Pengadaan -->
                        <td class="px-6 py-4 font-semibold text-white bg-blue-950/10 border-r border-slate-800/80 text-sm">{{ $item->nama_belanja }}</td>

                        <!-- Kolom 4: Kelompok Akun -->
                        <td class="px-4 py-4 text-center border-r border-slate-800/80 align-middle">
                            <span class="inline-block px-3 py-1.5 rounded-lg text-[11px] font-semibold border leading-tight"
                                :class="'{{ $item->kelompok }}' === '5.2.02' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30' : ('{{ $item->kelompok }}' === '5.2.03' ? 'bg-purple-500/20 text-purple-300 border-purple-500/30' : 'bg-blue-500/20 text-blue-300 border-blue-500/30')">
                                {{ $item->kelompok }} - {{ $item->nama_kelompok }}
                            </span>
                        </td>

                        <!-- Kolom 5: Aksi — FREEZE STICKY RIGHT -->
                        <td class="px-3 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[190px] w-[190px]" style="position: sticky; right: 0; z-index: 2; background-color: #0c172e !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" @click="openEdit({{ json_encode($item) }})"
                                    class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Ubah</span>
                                </button>

                                <form method="POST" action="{{ route('master.rekening_belanja.destroy', $item->id) }}" class="inline-block" @submit="confirmDeleteForm($event, '{{ $item->nama_belanja }} ({{ $item->kode_rek }})')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-slate-500">
                            <div class="text-2xl mb-2">📂</div>
                            <div class="font-semibold text-slate-400">Tidak ada data rekening belanja ditemukan.</div>
                            <div class="text-[11px] text-slate-600 mt-0.5">Silakan tambahkan akun rekening baru atau sesuaikan filter pencarian Anda.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
