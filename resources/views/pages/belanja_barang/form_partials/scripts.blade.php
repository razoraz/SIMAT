<!-- ========================================================================= -->
<!-- SCRIPT ALPINE.JS & LOGIKA FORM BELANJA BARANG MULTI-ITEM                  -->
<!-- ========================================================================= -->
<script>
    function formBelanjaBarang() {
        return {
            step: 1,
            isSubmitting: false,

            // Master Data dari Backend
            master108: @json($dbMaster108 ?? []),
            masterTokoList: @json($dbTokoPenyedias ?? []),
            dbUnits: @json($dbUnits ?? []),

            // File Uploads
            fileFaktur: null,
            fileKuitansi: null,

            handleFileChange(event, type) {
                const file = event.target.files[0];
                if (!file) return;

                // Validasi Ukuran (Maks 5MB = 5 * 1024 * 1024 bytes)
                if (file.size > 5 * 1024 * 1024) {
                    alert('⚠️ Ukuran file melebihi batas maksimal 5MB. Silakan pilih file yang lebih kecil.');
                    event.target.value = '';
                    return;
                }

                if (type === 'faktur') {
                    this.fileFaktur = file;
                } else if (type === 'kuitansi') {
                    this.fileKuitansi = file;
                }
            },

            removeFile(type) {
                if (type === 'faktur') {
                    this.fileFaktur = null;
                    const el = document.getElementById('dokumen_faktur_input');
                    if (el) el.value = '';
                } else if (type === 'kuitansi') {
                    this.fileKuitansi = null;
                    const el = document.getElementById('dokumen_kuitansi_input');
                    if (el) el.value = '';
                }
            },

            formatFileSize(bytes) {
                if (!bytes || bytes === 0) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            },

            // Toko Combobox State
            isTokoDropdownOpen: false,

            get filteredTokoList() {
                const q = (this.formData.rekening_penyedia || '').toLowerCase().trim();
                if (!q) return this.masterTokoList || [];
                return (this.masterTokoList || []).filter(item => item.toLowerCase().includes(q));
            },

            selectToko(name) {
                this.formData.rekening_penyedia = name;
                this.isTokoDropdownOpen = false;
            },

            // Alokasi Penempatan Opsi A State
            globalAlokasiMode: 'gudang', // 'gudang' atau 'ruangan'
            globalUnitId: '',
            globalKondisi: 'Baik',
            globalAlamatBarang: 'RSUD Dr. H. Koesnandi',

            setGlobalAlokasiMode(mode) {
                this.globalAlokasiMode = mode;
                this.formData.items.forEach(item => {
                    item.mode_penempatan = mode;
                    if (mode === 'gudang') {
                        item.unit_id = null;
                        item.alamat_barang = 'Gudang Perbekalan RSUD';
                    } else if (mode === 'ruangan' && this.globalUnitId) {
                        item.unit_id = this.globalUnitId;
                        item.alamat_barang = this.globalAlamatBarang;
                    }
                });
            },

            applyGlobalUnit() {
                this.formData.items.forEach(item => {
                    if (item.mode_penempatan === 'ruangan') {
                        item.unit_id = this.globalUnitId;
                    }
                });
            },

            applyGlobalKondisi() {
                this.formData.items.forEach(item => {
                    item.kondisi = this.globalKondisi;
                });
            },

            applyGlobalAlamat() {
                this.formData.items.forEach(item => {
                    if (item.mode_penempatan === 'ruangan') {
                        item.alamat_barang = this.globalAlamatBarang;
                    }
                });
            },

            // Multi-Item Form Data Model
            formData: {
                tahun_perolehan: new Date().getFullYear(),
                triwulan: 'TW I',
                rekening_penyedia: '',
                rekening_nomor_faktur: '',
                rekening_tanggal_faktur: '',
                rekening_keterangan: '',
                items: [
                    {
                        _id: 1,
                        nama_barang: '',
                        jenis_astap_id: null,
                        kode_108: '',
                        nama_108: '',
                        jumlah_volume: 1,
                        satuan: 'Unit',
                        harga_satuan: 0,
                        mode_penempatan: 'gudang',
                        unit_id: null,
                        kondisi: 'Baik',
                        alamat_barang: 'Gudang Perbekalan RSUD',
                        spesifikasi_json: {
                            merk: '',
                            type: '',
                            bahan: '',
                            no_pabrik: ''
                        }
                    }
                ]
            },

            addItem() {
                const nextId = (this.formData.items.length ? Math.max(...this.formData.items.map(it => it._id || 0)) : 0) + 1;
                this.formData.items.push({
                    _id: nextId,
                    nama_barang: '',
                    jenis_astap_id: null,
                    kode_108: '',
                    nama_108: '',
                    jumlah_volume: 1,
                    satuan: 'Unit',
                    harga_satuan: 0,
                    mode_penempatan: this.globalAlokasiMode,
                    unit_id: this.globalAlokasiMode === 'ruangan' ? this.globalUnitId : null,
                    kondisi: this.globalKondisi || 'Baik',
                    alamat_barang: this.globalAlokasiMode === 'ruangan' ? this.globalAlamatBarang : 'Gudang Perbekalan RSUD',
                    spesifikasi_json: {
                        merk: '',
                        type: '',
                        bahan: '',
                        no_pabrik: ''
                    }
                });
            },

            duplicateItem(idx) {
                const src = this.formData.items[idx];
                const nextId = Math.max(...this.formData.items.map(it => it._id || 0)) + 1;
                const copy = JSON.parse(JSON.stringify(src));
                copy._id = nextId;
                copy.nama_barang = src.nama_barang ? (src.nama_barang + ' (Salinan)') : '';
                this.formData.items.splice(idx + 1, 0, copy);
            },

            removeItem(idx) {
                if (this.formData.items.length <= 1) {
                    alert('⚠️ Minimal harus ada 1 item barang dalam faktur.');
                    return;
                }
                this.formData.items.splice(idx, 1);
            },

            get totalNilaiFaktur() {
                return (this.formData.items || []).reduce((acc, item) => {
                    const vol = Math.max(1, parseInt(item.jumlah_volume) || 1);
                    const hrg = parseFloat(item.harga_satuan) || 0;
                    return acc + (vol * hrg);
                }, 0);
            },

            get totalVolumeFaktur() {
                return (this.formData.items || []).reduce((acc, item) => {
                    return acc + Math.max(1, parseInt(item.jumlah_volume) || 1);
                }, 0);
            },

            // Modal Picker 108 State
            isPicker108Open: false,
            activeItemIndex: null,
            search108: '',
            searchResults108: [],
            selectedAkunKode: '',
            selectedJenisKode: '',
            selectedObjekKode: '',
            selectedRincianKode: '',
            selectedSubSubId: '',
            jenisList: [],
            objekList: [],
            rincianList: [],
            subSubList: [],

            openPicker108(itemIndex) {
                this.activeItemIndex = itemIndex;
                this.search108 = '';
                this.searchResults108 = [];
                this.selectedAkunKode = '';
                this.selectedJenisKode = '';
                this.selectedObjekKode = '';
                this.selectedRincianKode = '';
                this.selectedSubSubId = '';
                this.jenisList = [];
                this.objekList = [];
                this.rincianList = [];
                this.subSubList = [];
                this.isPicker108Open = true;
            },

            closePicker108() {
                this.isPicker108Open = false;
                this.activeItemIndex = null;
            },

            performSearch108() {
                const q = this.search108.toLowerCase().trim();
                if (q.length < 2) {
                    this.searchResults108 = [];
                    return;
                }
                const results = [];
                const walk = (nodes) => {
                    for (const n of nodes) {
                        if (n.children && n.children.length > 0) {
                            walk(n.children);
                        } else {
                            if ((n.nama && n.nama.toLowerCase().includes(q)) || (n.kode && n.kode.toLowerCase().includes(q))) {
                                results.push(n);
                                if (results.length >= 25) return;
                            }
                        }
                    }
                };
                walk(this.master108);
                this.searchResults108 = results;
            },

            selectSearchResult108(item) {
                if (this.activeItemIndex !== null && this.formData.items[this.activeItemIndex]) {
                    const target = this.formData.items[this.activeItemIndex];
                    target.jenis_astap_id = item.id;
                    target.kode_108 = item.kode;
                    target.nama_108 = item.nama;
                    if (!target.nama_barang) {
                        target.nama_barang = item.nama;
                    }
                }
                this.closePicker108();
            },

            onAkunChange() {
                const akun = this.master108.find(a => a.kode === this.selectedAkunKode);
                this.jenisList = akun ? (akun.children || []) : [];
                this.selectedJenisKode = '';
                this.objekList = [];
                this.selectedObjekKode = '';
                this.rincianList = [];
                this.selectedRincianKode = '';
                this.subSubList = [];
                this.selectedSubSubId = '';
            },

            onJenisChange() {
                const jenis = this.jenisList.find(j => j.kode === this.selectedJenisKode);
                this.objekList = jenis ? (jenis.children || []) : [];
                this.selectedObjekKode = '';
                this.rincianList = [];
                this.selectedRincianKode = '';
                this.subSubList = [];
                this.selectedSubSubId = '';
            },

            onObjekChange() {
                const objek = this.objekList.find(o => o.kode === this.selectedObjekKode);
                this.rincianList = objek ? (objek.children || []) : [];
                this.selectedRincianKode = '';
                this.subSubList = [];
                this.selectedSubSubId = '';
            },

            onRincianChange() {
                const rincian = this.rincianList.find(r => r.kode === this.selectedRincianKode);
                this.subSubList = rincian ? (rincian.children || []) : [];
                this.selectedSubSubId = '';
            },

            onSubSubChange() {
                const subSub = this.subSubList.find(s => s.id == this.selectedSubSubId);
                if (subSub) {
                    this.selectSearchResult108(subSub);
                }
            },

            formatRupiah(val) {
                if (!val || isNaN(val)) return 'Rp 0';
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(val));
            },

            goToStep(target) {
                if (target > this.step) {
                    if (this.step === 1 && !this.validateStep(1)) return;
                    if (this.step === 2 && !this.validateStep(2)) return;
                }
                this.step = target;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            validateStep(s) {
                if (s === 1) {
                    if (!this.formData.rekening_penyedia.trim()) {
                        alert('⚠️ Mohon masukkan Nama Toko / Supplier Rekanan.');
                        return false;
                    }
                    if (!this.formData.rekening_nomor_faktur.trim()) {
                        alert('⚠️ Mohon masukkan Nomor Faktur / Nota Pembelian Toko.');
                        return false;
                    }
                    if (!this.formData.rekening_tanggal_faktur) {
                        alert('⚠️ Mohon masukkan Tanggal Faktur / Pembelian.');
                        return false;
                    }
                    if (!this.fileFaktur) {
                        alert('⚠️ Wajib mengunggah Nota / Faktur Pembelian Toko (PDF/Gambar max 5MB).');
                        return false;
                    }
                    return true;
                }

                if (s === 2) {
                    if (!this.formData.items || this.formData.items.length === 0) {
                        alert('⚠️ Minimal harus ada 1 item barang yang dibeli.');
                        return false;
                    }
                    for (let i = 0; i < this.formData.items.length; i++) {
                        const it = this.formData.items[i];
                        if (!it.jenis_astap_id) {
                            alert(`⚠️ Barang #${i + 1} belum memiliki Klasifikasi Kode 108. Silakan pilih Kode 108.`);
                            return false;
                        }
                        if (!it.nama_barang.trim()) {
                            alert(`⚠️ Barang #${i + 1} belum memiliki Nama Barang Spesifik.`);
                            return false;
                        }
                        if (!it.jumlah_volume || it.jumlah_volume < 1) {
                            alert(`⚠️ Barang #${i + 1} harus memiliki Volume minimal 1 unit.`);
                            return false;
                        }
                        if (it.harga_satuan === '' || isNaN(it.harga_satuan) || it.harga_satuan < 0) {
                            alert(`⚠️ Barang #${i + 1} harus memiliki Harga Satuan yang valid (>= 0).`);
                            return false;
                        }
                    }
                    return true;
                }

                if (s === 3) {
                    if (this.globalAlokasiMode === 'ruangan' && !this.globalUnitId) {
                        alert('⚠️ Mode Alokasi Langsung ke Ruangan dipilih. Mohon pilih Unit / Ruangan tujuan.');
                        return false;
                    }
                    return true;
                }

                return true;
            },

            submitForm() {
                if (!this.validateStep(1) || !this.validateStep(2) || !this.validateStep(3)) return;

                this.isSubmitting = true;
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                const fd = new FormData();
                fd.append('tahun_perolehan', this.formData.tahun_perolehan);
                fd.append('triwulan', this.formData.triwulan);
                fd.append('rekening_penyedia', this.formData.rekening_penyedia);
                fd.append('rekening_nomor_faktur', this.formData.rekening_nomor_faktur);
                fd.append('rekening_tanggal_faktur', this.formData.rekening_tanggal_faktur);
                fd.append('rekening_keterangan', this.formData.rekening_keterangan || '');
                fd.append('status_alokasi_global', this.globalAlokasiMode);

                if (this.fileFaktur) {
                    fd.append('dokumen_faktur', this.fileFaktur);
                }
                if (this.fileKuitansi) {
                    fd.append('dokumen_kuitansi', this.fileKuitansi);
                }

                // Kirim Items sebagai JSON String
                fd.append('items_json', JSON.stringify(this.formData.items));

                fetch("{{ route('astap.store_belanja_barang') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: fd
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(result => {
                    this.isSubmitting = false;
                    if (result.status === 200 && result.body.success) {
                        alert('🎉 ' + (result.body.message || 'Faktur Belanja Barang berhasil disimpan!'));
                        window.location.href = "{{ route('master.belanja_barang') }}";
                    } else {
                        const errMsg = result.body.message || (result.body.errors ? Object.values(result.body.errors).flat().join('\n') : 'Gagal menyimpan data.');
                        alert('❌ Terjadi Kesalahan:\n' + errMsg);
                    }
                })
                .catch(err => {
                    this.isSubmitting = false;
                    console.error(err);
                    alert('❌ Gagal menghubungi server. Silakan periksa koneksi Anda dan coba kembali.');
                });
            }
        };
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.6);
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(99, 102, 241, 0.4);
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(99, 102, 241, 0.7);
    }
</style>
