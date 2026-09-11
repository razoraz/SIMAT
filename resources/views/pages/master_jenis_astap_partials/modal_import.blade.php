<!-- MODAL IMPORT CSV -->
<div x-show="showImportModal" x-transition.opacity class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4" x-cloak>
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl relative" @click.away="showImportModal = false">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div class="flex items-center space-x-3">
                <div class="p-2.5 rounded-2xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Import Data Kode 108 BMD</h3>
                    <p class="text-xs text-slate-400">Upload file Excel (.xlsx, .xls) atau CSV</p>
                </div>
            </div>
            <button type="button" @click="showImportModal = false" class="text-slate-400 hover:text-white p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('master.jenis_astap.import') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
            @csrf
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                <label class="block text-xs font-semibold text-slate-300">Pilih File Excel / CSV (*.xlsx, *.xls, *.csv):</label>
                <input type="file" name="file" accept=".xlsx, .xls, .csv, .txt" required
                    class="block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-500/20 file:text-emerald-400 hover:file:bg-emerald-500/30 cursor-pointer">
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    💡 Format file yang didukung: <b>.xlsx</b>, <b>.xls</b>, atau <b>.csv</b>.
                </p>
                <div class="flex items-center space-x-2 pt-2 border-t border-slate-800/80">
                    <input type="checkbox" id="reset_existing" name="reset_existing" value="1" checked
                        class="w-4 h-4 rounded border-slate-700 bg-slate-950 text-emerald-500 focus:ring-0 cursor-pointer">
                    <label for="reset_existing" class="text-xs text-slate-300 cursor-pointer select-none font-medium">
                        Kosongkan data lama sebelum impor (Mencegah duplikasi data)
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('master.jenis_astap.template') }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 underline flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Unduh Template CSV Contoh</span>
                </a>

                <div class="flex items-center space-x-2">
                    <button type="button" @click="showImportModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all">
                        Upload &amp; Import
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
