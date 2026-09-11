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

        get isStep1Valid() {
            return !!this.jenis_mutasi;
        },

        get isStep2Valid() {
            return !!this.ruangan_asal && !!this.ruangan_tujuan && (this.ruangan_asal !== this.ruangan_tujuan);
        },

        get isStep3Valid() {
            return this.selectedRegisterIds.length > 0 && !!(this.alasan_mutasi && this.alasan_mutasi.trim());
        },

        nextStep() {
            if (this.step === 1 && !this.isStep1Valid) {
                this.showToast('Silakan pilih salah satu Jenis Pengajuan Mutasi terlebih dahulu.', 'warning');
                return;
            }
            if (this.step === 2) {
                if (!this.ruangan_asal || !this.ruangan_tujuan) {
                    this.showToast('Ruangan Asal (Pengirim) dan Ruangan Tujuan (Penerima) wajib dipilih.', 'warning');
                    return;
                }
                if (this.ruangan_asal === this.ruangan_tujuan) {
                    this.showToast('Ruangan Asal dan Ruangan Tujuan tidak boleh sama! Silakan pilih unit tujuan yang berbeda.', 'warning');
                    return;
                }
            }
            if (this.step === 3) {
                if (this.selectedRegisterIds.length === 0) {
                    this.showToast('Silakan pilih minimal 1 barang aset yang akan dimutasi terlebih dahulu.', 'warning');
                    return;
                }
                if (!this.alasan_mutasi || !this.alasan_mutasi.trim()) {
                    this.showToast('⚠️ Alasan / Urgensi pengajuan mutasi wajib diisi sebelum lanjut ke Langkah 4.', 'warning');
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
            if (s < this.step) {
                this.step = s;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else if (s > this.step) {
                this.showToast('⚠️ Mohon selesaikan pengisian dan tekan tombol Lanjut ke Langkah ' + (this.step + 1) + ' terlebih dahulu.', 'warning');
            }
        },

        showToast(msg, type = 'warning') {
            this.toast = { show: true, message: msg, type: type };
            setTimeout(() => { this.toast.show = false; }, 4000);
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

        validateBeforeSubmit(e) {
            if (this.ruangan_asal && this.ruangan_tujuan && this.ruangan_asal === this.ruangan_tujuan) {
                this.showToast('⚠️ Ruangan Asal dan Ruangan Tujuan tidak boleh sama! Silakan pilih ruangan tujuan yang berbeda.', 'warning');
                e.preventDefault();
                return false;
            }
            if (this.selectedRegisterIds.length === 0) {
                this.showToast('⚠️ Silakan pilih minimal 1 barang aset yang akan dimutasi.', 'warning');
                e.preventDefault();
                return false;
            }
            e.preventDefault();
            const formElement = e.target;
            const targetItem = this.selectedRegisterIds.length + ' Barang Aset (' + (this.ruangan_asal || 'Asal') + ' ➔ ' + (this.ruangan_tujuan || 'Tujuan') + ')';
            this.askConfirmation({
                title: this.isEdit ? '✏️ Konfirmasi Simpan Perubahan Mutasi' : '🔄 Konfirmasi Pengajuan Mutasi Baru',
                message: this.isEdit ? 'Apakah Anda yakin ingin menyimpan perubahan data pengajuan mutasi ini?' : 'Apakah Anda yakin ingin mengajukan mutasi untuk ' + this.selectedRegisterIds.length + ' barang aset ini? Pindah tangan ruangan akan diproses setelah persetujuan.',
                itemName: targetItem,
                type: this.isEdit ? 'warning' : 'success',
                btnText: this.isEdit ? '✏️ Ya, Simpan Perubahan' : '🔄 Ya, Ajukan Mutasi (' + this.selectedRegisterIds.length + ' Barang)',
                onConfirm: () => {
                    formElement.submit();
                }
            });
            return false;
        }
        };
    }
</script>
