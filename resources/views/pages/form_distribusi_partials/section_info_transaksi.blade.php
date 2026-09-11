            <!-- BAGIAN 1: INFORMASI TRANSAKSI & TUJUAN PENERIMA (AUTOFILL DATA UNIT) -->
            <div class="space-y-4">
                <h3 class="text-sm font-extrabold text-teal-300 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-800 pb-3">
                    <span x-text="isSubAdmin ? '1. Informasi Pengajuan & Pegawai Ruangan' : '1. Informasi Penyerahan & Pegawai Penerima Ruangan'"></span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Kode Transaksi Distribusi -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">No. Registrasi Distribusi</label>
                        <input type="text" x-model="formData.kode" readonly
                               class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-teal-400 font-mono font-bold focus:outline-none cursor-not-allowed">
                    </div>

                    <!-- Nomor BAST Rujukan (Otomatis & Terkunci) -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                            <span>No. BAST Distribusi</span>
                            <span class="text-[10px] text-teal-400 font-normal">Otomatis Sistem</span>
                        </label>
                        <input type="text" x-model="formData.bast_nomor" readonly
                               :disabled="formData.status === 'Ditolak'"
                               :placeholder="formData.status === 'Ditolak' ? '(tidak diterbitkan)' : (formData.status === 'Menunggu Konfirmasi' ? '(Menunggu Konfirmasi)' : '032 / ... / 430.10.7 / 2026')"
                               class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-teal-400 font-mono font-bold focus:outline-none cursor-not-allowed">
                    </div>

                    <!-- Tanggal Distribusi / Pengajuan -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                            <span x-text="isSubAdmin ? 'Tanggal Pengajuan' : 'Tanggal Penyerahan'"></span>
                            <span class="text-[10px] text-teal-400 font-normal">Auto Hari Ini</span>
                        </label>
                        <input type="text" x-datepicker x-model="formData.tgl"
                               :value="formData.tgl"
                               @change="updateYearInKode()"
                               placeholder="dd/mm/yyyy"
                               :readonly="isSubAdmin || formData.status === 'Ditolak'"
                               :disabled="formData.status === 'Ditolak'"
                               :class="(isSubAdmin || formData.status === 'Ditolak') ? 'bg-slate-950/80 text-slate-400 cursor-not-allowed pointer-events-none' : 'bg-slate-950 text-white'"
                               class="w-full border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:border-teal-500">
                    </div>

                    <!-- Status Distribusi -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                            <span>Status Distribusi</span>
                            <span class="text-teal-400 text-[10px] font-bold" x-text="isSubAdmin ? '🔒 Dikelola Admin' : '⚡ Status Transaksi'"></span>
                        </label>
                        
                        <!-- Dropdown Status (Hanya untuk Admin & Master Admin) -->
                        <template x-if="!isSubAdmin">
                            <select x-model="formData.status"
                                    @change="onStatusChange()"
                                    class="w-full bg-slate-950 border rounded-xl px-3.5 py-2.5 text-xs font-bold focus:outline-none focus:border-teal-500 transition-all cursor-pointer"
                                    :class="{
                                        'border-rose-500/50 ring-1 ring-rose-500/30 text-rose-400': formData.status === 'Ditolak',
                                        'border-slate-800 text-emerald-400': formData.status === 'Telah Diterima' || formData.status === 'Diterima',
                                        'border-slate-800 text-amber-400': formData.status === 'Dalam Pengiriman' || formData.status === 'Dikirim',
                                        'border-slate-800 text-cyan-400': formData.status === 'Menunggu Konfirmasi' || formData.status === 'Pending'
                                    }">
                                <option value="Menunggu Konfirmasi">⏳ Menunggu Konfirmasi</option>
                                <option value="Dalam Pengiriman">🚚 Dalam Pengiriman</option>
                                <option value="Telah Diterima">🟢 Telah Diterima</option>
                                <option value="Ditolak">❌ Ditolak</option>
                            </select>
                        </template>

                        <!-- Readonly Badge Status (Untuk Sub Admin Ruangan) -->
                        <template x-if="isSubAdmin">
                            <div class="w-full h-10 bg-slate-950/80 border border-slate-800 rounded-xl px-3.5 flex items-center text-xs font-bold space-x-2 cursor-not-allowed"
                                 :class="{
                                     'text-emerald-400': formData.status === 'Telah Diterima' || formData.status === 'Diterima',
                                     'text-amber-400': formData.status === 'Dalam Pengiriman' || formData.status === 'Dikirim',
                                     'text-cyan-400': formData.status === 'Menunggu Konfirmasi' || formData.status === 'Pending',
                                     'text-rose-400': formData.status === 'Ditolak',
                                     'text-slate-400': formData.status === 'Draft' || !formData.status
                                 }">
                                <span x-text="formData.status === 'Ditolak' ? '❌' : (formData.status === 'Telah Diterima' ? '🟢' : (formData.status === 'Dalam Pengiriman' ? '🚚' : (formData.status === 'Draft' || !formData.status ? '📝' : '⏳')))"></span>
                                <span x-text="formData.status || 'Draft'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Input Unit & Data PIC Penerima -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-950 border border-slate-800 space-y-4">
                    
                    <!-- KONDISI A: Akun Sub Admin (Unit Terkunci Otomatis Sesuai Akun Login) -->
                    <template x-if="isSubAdmin">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-300 flex items-center space-x-1.5">
                                    <span>🏥 Unit / Ruangan Anda</span>
                                    <span class="text-teal-400 font-mono text-[10px] bg-teal-500/20 px-2 py-0.5 rounded-full border border-teal-500/30">🔒 Terkunci Otomatis Sesuai Akun</span>
                                </label>
                            </div>
                            <div class="relative flex items-center">
                                <input type="text" :value="formData.tujuan || (userUnit ? userUnit.nama : 'Unit Ruangan')" readonly
                                       class="w-full bg-slate-900/70 border border-slate-800 rounded-xl px-4 py-2.5 pl-10 text-xs text-teal-300 font-extrabold cursor-not-allowed">
                                <svg class="w-4 h-4 text-teal-400 absolute left-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                        </div>
                    </template>

                    <!-- KONDISI B: Admin & Master Admin (Bisa Cari & Pilih Unit Bebas) -->
                    <template x-if="!isSubAdmin">
                        <div class="relative" @click.away="isSearchingUnit = false">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-300 flex items-center space-x-1.5">
                                    <span>🏥 Unit / Ruangan / Paviliun Tujuan</span>
                                    <span class="text-teal-400 font-mono text-[11px]" x-text="'(' + unitList.length + ' Unit Terdaftar)'"></span>
                                </label>
                                <template x-if="formData.tujuan && formData.status !== 'Ditolak'">
                                    <button type="button" @click="clearUnit()" class="text-xs text-rose-400 hover:text-rose-300 font-semibold cursor-pointer">
                                        ✕ Ganti Unit
                                    </button>
                                </template>
                            </div>

                            <div class="relative">
                                <input type="text" x-model="unitSearch" 
                                       :disabled="formData.status === 'Ditolak'"
                                       :readonly="formData.status === 'Ditolak'"
                                       @focus="if (formData.status !== 'Ditolak') isSearchingUnit = true" 
                                       @input="if (formData.status !== 'Ditolak') isSearchingUnit = true" 
                                       :placeholder="formData.status === 'Ditolak' ? 'Tujuan unit terkunci' : 'Ketik nama unit / ruangan (contoh: IGD, Melati, Radiologi, Bedah)...'" 
                                       :class="formData.status === 'Ditolak' ? 'bg-slate-950/80 text-slate-400 cursor-not-allowed border-slate-800' : 'bg-slate-900 text-white border-slate-800 focus:border-teal-500'"
                                       class="w-full border rounded-xl px-4 py-2.5 pl-10 text-xs placeholder-slate-500 focus:outline-none transition-all font-semibold">
                                <svg class="w-4 h-4 text-teal-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>

                            <!-- Dropdown Autocomplete Unit (Dark Themed) -->
                            <div x-show="isSearchingUnit && formData.status !== 'Ditolak'" 
                                 x-transition 
                                 class="absolute left-0 right-0 z-30 mt-1 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-56 overflow-y-auto divide-y divide-slate-800">
                                <template x-for="u in filteredUnitList.slice(0, 5)" :key="u.id">
                                    <div @click="selectUnit(u)" 
                                         class="p-3 hover:bg-teal-500/15 cursor-pointer transition-colors flex items-center justify-between group">
                                        <div>
                                            <p class="font-bold text-white text-xs group-hover:text-teal-300" x-text="u.nama"></p>
                                            <p class="text-[10px] text-slate-400" x-text="(u.kode || 'UNIT') + ' • ' + (u.tipe || 'Unit') + ' • PJ: ' + u.kepala"></p>
                                        </div>
                                        <span class="px-2 py-1 rounded bg-teal-500/20 text-teal-300 text-[10px] font-bold">Pilih &rarr;</span>
                                    </div>
                                </template>
                                <template x-if="filteredUnitList.length === 0">
                                    <div class="p-3 text-center text-xs text-slate-500">Unit tidak ditemukan</div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Auto-filled PIC Penerima Details (SELALU Readonly - otomatis dari unit) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-800/80">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold mb-1">Pegawai Penerima (Kepala/PJ)</span>
                            <input type="text" x-model="formData.penerima" 
                                   readonly
                                   placeholder="Terisi otomatis saat pilih unit..." 
                                   class="w-full bg-slate-900/60 text-slate-300 border border-slate-800 rounded-lg px-3 py-2 text-xs font-bold cursor-not-allowed focus:outline-none"
                                   title="Otomatis terisi dari data kepala unit">
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold mb-1">NIP Pegawai</span>
                            <input type="text" x-model="formData.penerima_nip" 
                                   readonly
                                   placeholder="Terisi otomatis saat pilih unit..." 
                                   class="w-full bg-slate-900/60 text-slate-400 border border-slate-800 rounded-lg px-3 py-2 text-xs font-mono cursor-not-allowed focus:outline-none"
                                   title="Otomatis terisi dari data NIP kepala unit">
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold mb-1">Jabatan Penerima</span>
                            <input type="text" x-model="formData.penerima_jabatan" 
                                   readonly
                                   placeholder="Terisi otomatis saat pilih unit..." 
                                   class="w-full bg-slate-900/60 text-slate-400 border border-slate-800 rounded-lg px-3 py-2 text-xs cursor-not-allowed focus:outline-none"
                                   title="Otomatis terisi dari jabatan kepala unit">
                        </div>
                    </div>

                </div>
            </div>

