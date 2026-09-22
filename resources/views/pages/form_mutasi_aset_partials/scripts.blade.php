<script>
    function formMutasiApp() {
        return {
        step: 1,
        isEdit: {{ isset($mutasi) ? 'true' : 'false' }},
        isSubAdmin: {{ $isSubAdmin ? 'true' : 'false' }},
        userUnitNama: {{ Js::from($userUnitNama) }},
        userUnitKepala: {{ Js::from($userUnitKepala) }},

        /* ---- Jenis Mutasi ---- */
        jenis_mutasi: {{ Js::from(old('jenis_mutasi', $mutasi->jenis_mutasi ?? 'Ajukan Mutasi')) }},
        subJenisPengembalian: 'gudang',
        jenisMutasiOptions: [
            { value: 'Ajukan Mutasi',  emoji: '🔄', label: 'Ajukan Mutasi',     desc: 'Unit asal mengajukan pemindahan/penyerahan barang miliknya ke unit tujuan', color: 'blue' },
            { value: 'Perbaikan',      emoji: '🔧', label: 'Perbaikan / Servis',   desc: 'Barang rusak dikirim ke unit/IPSRS yang bisa memperbaiki', color: 'amber' },
            { value: 'Minta Mutasi',   emoji: '📥', label: 'Minta Mutasi',      desc: 'Unit B meminta aset milik Unit A untuk dipindahkan ke Unit B', color: 'teal' },
            { value: 'Pengembalian',   emoji: '↩️', label: 'Pengembalian Barang', desc: 'Barang yang selesai diperbaiki / selesai dipakai / tidak dibutuhkan dikembalikan ke unit terkait atau Pengurus Barang', color: 'rose' }
        ],

        /* ---- State Ruangan & PJ ---- */
        ruangan_asal: {{ Js::from(old('ruangan_asal', $mutasi->ruangan_asal ?? '')) }},
        ruangan_tujuan: {{ Js::from(old('ruangan_tujuan', $mutasi->ruangan_tujuan ?? '')) }},
        penanggung_jawab_asal: {{ Js::from(old('penanggung_jawab_asal', $mutasi->penanggung_jawab_asal ?? '')) }},
        penanggung_jawab_tujuan: {{ Js::from(old('penanggung_jawab_tujuan', $mutasi->penanggung_jawab_tujuan ?? '')) }},
        alasan_mutasi: {{ Js::from(old('alasan_mutasi', $mutasi->alasan_mutasi ?? '')) }},

        /* ---- State Search Filterable Unit Selector ---- */
        searchUnitAsal: '',
        isUnitAsalOpen: false,
        searchUnitTujuan: '',
        isUnitTujuanOpen: false,

        /* ---- Multi-Select Barang Aset ---- */
        selectedRegisterIds: {{ Js::from($initialRegisterIds) }},
        editedKondisi: {},
        filterKondisi: '',
        searchBarang: '',
        showDropdown: false,

        registers: {{ Js::from($registers) }},
        units: {{ Js::from($units) }},
        lockedCountByUnit: {{ Js::from($lockedCountByUnit ?? []) }},

        get lockedCountForAsal() {
            if (!this.ruangan_asal) return 0;
            const norm = this.ruangan_asal.toLowerCase().trim();
            return this.lockedCountByUnit[norm] || 0;
        },

        init() {
            // Jika role sub_admin dan bukan edit, kunci unit sesuai jenis mutasi
            if (this.isSubAdmin && this.userUnitNama && !this.isEdit) {
                if (this.jenis_mutasi === 'Minta Mutasi') {
                    this.ruangan_tujuan = this.userUnitNama;
                    this.penanggung_jawab_tujuan = this.userUnitKepala;
                } else {
                    this.ruangan_asal = this.userUnitNama;
                    this.penanggung_jawab_asal = this.userUnitKepala;
                }
            }

            if (this.ruangan_asal) {
                this.searchUnitAsal = this.ruangan_asal;
                const u = this.units.find(item => item.nama === this.ruangan_asal);
                if (u && !this.penanggung_jawab_asal) this.penanggung_jawab_asal = u.kepala || ('Kepala Ruangan ' + u.nama);
            }

            if (this.ruangan_tujuan) {
                this.searchUnitTujuan = this.ruangan_tujuan;
                const u = this.units.find(item => item.nama === this.ruangan_tujuan);
                if (u && !this.penanggung_jawab_tujuan) this.penanggung_jawab_tujuan = u.kepala || ('Kepala Ruangan ' + u.nama);
            }

            // Inisialisasi editedKondisi default dari data registers
            (this.registers || []).forEach(r => {
                this.editedKondisi[r.id] = r.kondisi || 'Baik';
            });
        },

        selectJenisMutasi(val) {
            this.jenis_mutasi = val;
            this.selectedRegisterIds = [];

            if (this.isSubAdmin && this.userUnitNama) {
                if (val === 'Minta Mutasi') {
                    // Minta Mutasi: Unit Saya = Unit Penerima (Tujuan), Unit Pengirim (Asal) = Unit Lain yang Diminta
                    this.ruangan_tujuan = this.userUnitNama;
                    this.penanggung_jawab_tujuan = this.userUnitKepala;
                    this.searchUnitTujuan = this.userUnitNama;

                    this.ruangan_asal = '';
                    this.penanggung_jawab_asal = '';
                    this.searchUnitAsal = '';
                } else {
                    // Ajukan Mutasi / Perbaikan / Pengembalian: Unit Saya = Unit Pengirim (Asal)
                    this.ruangan_asal = this.userUnitNama;
                    this.penanggung_jawab_asal = this.userUnitKepala;
                    this.searchUnitAsal = this.userUnitNama;

                    this.ruangan_tujuan = '';
                    this.penanggung_jawab_tujuan = '';
                    this.searchUnitTujuan = '';

                    if (val === 'Perbaikan') {
                        const ipsrs = this.units.find(u => (u.nama || '').toLowerCase().includes('ips') || (u.nama || '').toLowerCase().includes('sarana'));
                        if (ipsrs) this.selectUnitTujuan(ipsrs);
                    } else if (val === 'Pengembalian') {
                        this.setPengembalianTarget('gudang');
                    }
                }
            } else {
                if (val === 'Perbaikan') {
                    const ipsrs = this.units.find(u => (u.nama || '').toLowerCase().includes('ips') || (u.nama || '').toLowerCase().includes('sarana'));
                    if (ipsrs) this.selectUnitTujuan(ipsrs);
                } else if (val === 'Pengembalian') {
                    this.setPengembalianTarget('gudang');
                }
            }
        },

        setPengembalianTarget(target) {
            this.subJenisPengembalian = target;
            if (target === 'gudang') {
                const perbekalan = this.units.find(u => (u.nama || '').toLowerCase().includes('perbekalan') || (u.nama || '').toLowerCase().includes('rumah tangga'));
                if (perbekalan) this.selectUnitTujuan(perbekalan);
            } else {
                this.ruangan_tujuan = '';
                this.penanggung_jawab_tujuan = '';
                this.searchUnitTujuan = '';
                this.isUnitTujuanOpen = false;
            }
        },

        /* Filter unit pengirim (Kecuali Unit Tujuan jika sudah dipilih) */
        get filteredUnitsAsal() {
            let list = this.units || [];
            if (this.ruangan_tujuan) {
                const tujuanNorm = this.ruangan_tujuan.toLowerCase().trim();
                list = list.filter(u => (u.nama || '').toLowerCase().trim() !== tujuanNorm);
            }
            if (!this.searchUnitAsal || this.searchUnitAsal === this.ruangan_asal) {
                return list;
            }
            const q = this.searchUnitAsal.toLowerCase();
            return list.filter(u =>
                (u.nama && u.nama.toLowerCase().includes(q)) ||
                (u.kepala && u.kepala.toLowerCase().includes(q))
            );
        },

        selectUnitAsal(u) {
            this.ruangan_asal = u.nama;
            this.penanggung_jawab_asal = u.kepala || ('Kepala Ruangan ' + u.nama);
            this.searchUnitAsal = u.nama;
            this.isUnitAsalOpen = false;
            this.selectedRegisterIds = [];
        },

        /* Filter unit penerima (Kecuali Unit Pengirim yang sedang dipilih) */
        get filteredUnitsTujuan() {
            let list = this.units || [];
            if (this.ruangan_asal) {
                const asalNorm = this.ruangan_asal.toLowerCase().trim();
                list = list.filter(u => (u.nama || '').toLowerCase().trim() !== asalNorm);
            }
            if (!this.searchUnitTujuan || this.searchUnitTujuan === this.ruangan_tujuan) {
                return list;
            }
            const q = this.searchUnitTujuan.toLowerCase();
            return list.filter(u =>
                (u.nama && u.nama.toLowerCase().includes(q)) ||
                (u.kepala && u.kepala.toLowerCase().includes(q))
            );
        },

        selectUnitTujuan(u) {
            this.ruangan_tujuan = u.nama;
            this.penanggung_jawab_tujuan = u.kepala || ('Kepala Ruangan ' + u.nama);
            this.searchUnitTujuan = u.nama;
            this.isUnitTujuanOpen = false;
        },

        /* Semua register barang yang tersedia di unit pengirim */
        get availableRegistersForAsal() {
            if (!this.ruangan_asal) return [];
            const selectedAsalNorm = this.ruangan_asal.toLowerCase().trim();
            return (this.registers || []).filter(r => {
                const unitNorm = (r.unit_nama || '').toLowerCase().trim();
                return unitNorm === selectedAsalNorm || unitNorm.includes(selectedAsalNorm) || selectedAsalNorm.includes(unitNorm);
            });
        },

        /* Filter register barang di unit pengirim sesuai kata kunci pencarian & filter kondisi */
        get filteredRegisters() {
            let list = this.availableRegistersForAsal;
            if (this.filterKondisi) {
                list = list.filter(r => (this.editedKondisi[r.id] || r.kondisi) === this.filterKondisi);
            }
            if (!this.searchBarang) return list;
            const q = this.searchBarang.toLowerCase();
            return list.filter(r =>
                (r.nibar && r.nibar.toLowerCase().includes(q)) ||
                (r.nama_barang && r.nama_barang.toLowerCase().includes(q))
            );
        },

        /* Toggle pilihan barang (Multi Select) */
        toggleSelectRegister(reg) {
            const idStr = Number(reg.id);
            const idx = this.selectedRegisterIds.indexOf(idStr);
            if (idx > -1) {
                this.selectedRegisterIds.splice(idx, 1);
            } else {
                this.selectedRegisterIds.push(idStr);
                if (!this.editedKondisi[idStr]) {
                    this.editedKondisi[idStr] = reg.kondisi || 'Baik';
                }
            }
        },

        isRegisterSelected(id) {
            return this.selectedRegisterIds.includes(Number(id));
        },

        selectAllRegisters() {
            this.selectedRegisterIds = [];
            (this.availableRegistersForAsal || []).forEach(r => {
                const idStr = Number(r.id);
                this.selectedRegisterIds.push(idStr);
                if (!this.editedKondisi[idStr]) {
                    this.editedKondisi[idStr] = r.kondisi || 'Baik';
                }
            });
        },

        clearAllSelectedRegisters() {
            this.selectedRegisterIds = [];
        },

        /* Daftar barang terpilih lengkap untuk preview */
        get selectedRegistersList() {
            return this.registers.filter(r => this.selectedRegisterIds.includes(Number(r.id)));
        },

        /* ---- Computed ---- */
        get canChangeKondisi() {
            return ['Ajukan Mutasi', 'Perbaikan', 'Pengembalian'].includes(this.jenis_mutasi);
        },

        /* ---- Computed ---- */
        get selectedJenis() {
            return this.jenisMutasiOptions.find(j => j.value === this.jenis_mutasi) || this.jenisMutasiOptions[0];
        },

        get labelAsal() {
            return this.jenis_mutasi === 'Minta Mutasi' ? 'Unit Asal (Pemilik Aset)' : 'Unit Asal (Pengirim)';
        },

        get labelTujuan() {
            if (this.jenis_mutasi === 'Minta Mutasi') {
                return 'Ruangan Tujuan (Pemohon)';
            }
            const map = {
                'Ajukan Mutasi':   'Ruangan Tujuan (Penerima)',
                'Pemindahan':      'Ruangan Tujuan (Penerima)',
                'Perbaikan':       'Ruangan Tujuan (IPSRS / Teknisi)',
                'Pengembalian':    'Ruangan Tujuan (Unit Terkait)',
                'Penghapusan':     'Ruangan Tujuan (Pengurus Barang)'
            };
            return map[this.jenis_mutasi] || 'Ruangan Tujuan';
        },

        get assetBannerLabel() {
            return this.jenis_mutasi === 'Minta Mutasi'
                ? 'Menampilkan Aset dari Unit Pemilik yang Diminta:'
                : 'Menampilkan Aset dari Unit Pengirim:';
        },

        get alasanPlaceholder() {
            const map = {
                'Ajukan Mutasi':   'Contoh: Unit asal menyerahkan/memindahkan aset ini ke unit tujuan untuk mendukung operasional...',
                'Pemindahan':      'Contoh: Unit asal menyerahkan/memindahkan aset ini ke unit tujuan untuk mendukung operasional...',
                'Perbaikan':       'Contoh: Alat mengalami gangguan fungsi / error, perlu perbaikan oleh teknisi IPSRS...',
                'Minta Mutasi':    'Contoh: Unit B membutuhkan aset milik Unit A dan mengajukan permohonan pemindahan barang...',
                'Minta_Mutasi':    'Contoh: Unit B membutuhkan aset milik Unit A dan mengajukan permohonan pemindahan barang...',
                'Pengembalian':    'Contoh: Barang telah selesai diperbaiki oleh IPSRS / selesai dipakai / tidak dibutuhkan dan dikembalikan ke unit terkait atau Pengurus Barang...',
                'Penghapusan':     'Contoh: Barang tidak dibutuhkan lagi / selesai masa pakai dan dikembalikan ke Pengurus Barang / Admin Aset...'
            };
            return map[this.jenis_mutasi] || 'Alasan pemindahan / mutasi aset...';
        },

        nextStep() {
            if (this.step === 1) {
                if (!this.jenis_mutasi) {
                    alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nJenis Pengajuan Mutasi belum dipilih.');
                    return;
                }
            }
            if (this.step === 2) {
                if (!this.ruangan_asal) {
                    alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nUnit Pengirim (Asal) belum dipilih.');
                    return;
                }
                if (!this.ruangan_tujuan) {
                    alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nUnit Penerima (Tujuan) belum dipilih.');
                    return;
                }
                if (this.ruangan_asal === this.ruangan_tujuan) {
                    alert('⚠️ Unit Pengirim dan Unit Penerima tidak boleh sama!\n\nSilakan pilih Unit Penerima (Tujuan) yang berbeda.');
                    return;
                }
                if (!this.penanggung_jawab_asal || !this.penanggung_jawab_asal.trim()) {
                    alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nPenanggung Jawab Unit Pengirim belum diisi.');
                    return;
                }
                if (!this.penanggung_jawab_tujuan || !this.penanggung_jawab_tujuan.trim()) {
                    alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nPenanggung Jawab Unit Penerima belum diisi.');
                    return;
                }
            }
            if (this.step === 3) {
                if (!this.selectedRegisterIds || this.selectedRegisterIds.length === 0) {
                    alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nMinimal harus ada 1 barang aset yang dipilih untuk dimutasi.');
                    return;
                }
                if (!this.alasan_mutasi || !this.alasan_mutasi.trim()) {
                    alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nAlasan / Keperluan mutasi aset belum diisi.');
                    return;
                }
            }
            if (this.step < 4) {
                this.step++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        prevStep() {
            if (this.step > 1) {
                this.step--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        goToStep(s) {
            if (s <= this.step) {
                this.step = s;
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            // Validasi saat lompat ke langkah di depan
            if (s > 1 && !this.jenis_mutasi) {
                this.step = 1;
                alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nJenis Pengajuan Mutasi belum dipilih.');
                return;
            }
            if (s > 2) {
                if (!this.ruangan_asal) {
                    this.step = 2;
                    alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nUnit Pengirim (Asal) belum dipilih.');
                    return;
                }
                if (!this.ruangan_tujuan) {
                    this.step = 2;
                    alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nUnit Penerima (Tujuan) belum dipilih.');
                    return;
                }
                if (this.ruangan_asal === this.ruangan_tujuan) {
                    this.step = 2;
                    alert('⚠️ Unit Pengirim dan Unit Penerima tidak boleh sama!\n\nSilakan pilih Unit Penerima (Tujuan) yang berbeda.');
                    return;
                }
            }
            if (s > 3) {
                if (!this.selectedRegisterIds || this.selectedRegisterIds.length === 0) {
                    this.step = 3;
                    alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nMinimal harus ada 1 barang aset yang dipilih untuk dimutasi.');
                    return;
                }
                if (!this.alasan_mutasi || !this.alasan_mutasi.trim()) {
                    this.step = 3;
                    alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nAlasan / Keperluan mutasi aset belum diisi.');
                    return;
                }
            }

            this.step = s;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        showSimatToast(message, type = 'success') {
            const cleanMsg = String(message || '').replace(/^[\s✅✔️☑️✓✔⚠️❌🚫⛔ℹ️🗑️✏️🔑💾]+/, '').trim();
            this.toast = { show: true, message: cleanMsg, type };
            setTimeout(() => { this.toast.show = false; }, 4000);
        },

        showToast(msg, type = 'warning') {
            this.showSimatToast(msg, type);
        },

        showConfirmModal: false,
        confirmData: {
            title: 'Konfirmasi Tindakan',
            message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
            itemName: '',
            type: 'success',
            btnText: 'Ya, Lanjutkan',
            onConfirm: null
        },

        toast: { show: false, message: '', type: 'success' },

        askConfirmation({ title, message, itemName, type = 'success', btnText, onConfirm }) {
            this.confirmData = {
                title: title || 'Konfirmasi Tindakan',
                message: message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                itemName: itemName || '',
                type: type,
                btnText: btnText || (type === 'danger' ? 'Ya, Hapus Data' : (type === 'warning' ? 'Ya, Simpan Perubahan' : 'Ya, Ajukan Mutasi')),
                onConfirm: onConfirm
            };
            this.showConfirmModal = true;
        },

        executeConfirmedAction() {
            if (typeof this.confirmData.onConfirm === 'function') {
                this.confirmData.onConfirm();
            }
            this.showConfirmModal = false;
        },

        submitWithConfirmation(e) {
            return this.validateBeforeSubmit(e);
        },

        validateBeforeSubmit(e) {
            if (e && e.preventDefault) e.preventDefault();

            // ── 1. Validasi Jenis Mutasi (Langkah 1) ─────────────────────────
            if (!this.jenis_mutasi) {
                this.step = 1;
                alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nJenis Pengajuan Mutasi belum dipilih.');
                return false;
            }

            // ── 2. Validasi Unit & Ruangan (Langkah 2) ──────────────────────
            if (!this.ruangan_asal || !this.ruangan_asal.trim()) {
                this.step = 2;
                alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nUnit Pengirim (Asal) belum dipilih.');
                return false;
            }
            if (!this.ruangan_tujuan || !this.ruangan_tujuan.trim()) {
                this.step = 2;
                alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nUnit Penerima (Tujuan) belum dipilih.');
                return false;
            }
            if (this.ruangan_asal === this.ruangan_tujuan) {
                this.step = 2;
                alert('⚠️ Unit Pengirim dan Unit Penerima tidak boleh sama!\n\nSilakan pilih Unit Penerima (Tujuan) yang berbeda.');
                return false;
            }
            if (!this.penanggung_jawab_asal || !this.penanggung_jawab_asal.trim()) {
                this.step = 2;
                alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nPenanggung Jawab Unit Pengirim belum diisi.');
                return false;
            }
            if (!this.penanggung_jawab_tujuan || !this.penanggung_jawab_tujuan.trim()) {
                this.step = 2;
                alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nPenanggung Jawab Unit Penerima belum diisi.');
                return false;
            }

            // ── 3. Validasi Barang & Alasan Mutasi (Langkah 3) ──────────────
            if (!this.selectedRegisterIds || this.selectedRegisterIds.length === 0) {
                this.step = 3;
                alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nMinimal harus ada 1 barang aset yang dipilih untuk dimutasi.');
                return false;
            }
            if (!this.alasan_mutasi || !this.alasan_mutasi.trim()) {
                this.step = 3;
                alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nAlasan / Keperluan mutasi aset belum diisi.');
                return false;
            }

            // ── 4. Konfirmasi Dialog Modal Sesuai Distribusi ────────────────
            const formElement = (e && e.target && e.target.tagName === 'FORM') ? e.target : document.querySelector('form');
            const targetItem = this.selectedRegisterIds.length + ' Barang Aset (' + (this.ruangan_asal || 'Asal') + ' ➔ ' + (this.ruangan_tujuan || 'Tujuan') + ')';
            
            this.askConfirmation({
                title: this.isEdit ? '✏️ Konfirmasi Ubah Mutasi' : '🔄 Konfirmasi Pengajuan Mutasi',
                message: this.isEdit 
                    ? 'Apakah Anda yakin ingin menyimpan perubahan data pengajuan mutasi ini?' 
                    : 'Apakah Anda yakin ingin mengajukan mutasi untuk ' + this.selectedRegisterIds.length + ' barang aset ini?',
                itemName: targetItem,
                type: this.isEdit ? 'info' : 'success',
                btnText: this.isEdit ? '✏️ Ya, Simpan Perubahan' : '✅ Ya, Kirim Pengajuan',
                onConfirm: () => {
                    if (formElement) {
                        formElement.submit();
                    }
                }
            });
            return false;
        }
        };
    }
</script>
