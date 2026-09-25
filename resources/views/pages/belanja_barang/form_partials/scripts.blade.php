<!-- ========================================================================= -->
<!-- SCRIPT ALPINE.JS & LOGIKA FORM INPUT BELANJA BARANG PERBEKALAN            -->
<!-- ========================================================================= -->
<script>
    function formBelanjaBarang() {
        return {
            step: 1,
            isSubmitting: false,

            // Master Data dari Backend
            master108: @json($dbMaster108 ?? []),
            masterTokoList: @json($dbTokoPenyedias ?? []),

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

            // Cascading 108 State
            search108: '',
            searchResults108: [],
            selectedAkunKode: '',
            selectedJenisKode: '',
            selectedObjekKode: '',
            selectedRincianKode: '',
            selectedSubSubId: '',
            selectedSubSub: null,

            jenisList: [],
            objekList: [],
            rincianList: [],
            subSubList: [],

            // Form Data Model
            formData: {
                tahun_perolehan: new Date().getFullYear(),
                triwulan: 'TW I',
                rekening_penyedia: '',
                rekening_nomor_faktur: '',
                rekening_tanggal_faktur: '',
                total_realisasi: 0,
                rekening_keterangan: '',

                jenis_astap_id: null,
                nama_barang: '',
                jumlah_volume: 1,
                satuan: 'Unit',
                unit_id: null,
                kondisi: 'Baik',
                alamat_barang: 'RSUD Dr. H. Koesnandi',

                spesifikasi_json: {
                    merk: '',
                    type: '',
                    bahan: '',
                    ukuran: '',
                    no_pabrik: '',
                    judul: ''
                }
            },

            // KIB Detection
            get detectedKib() {
                if (!this.selectedSubSub || !this.selectedSubSub.kode) return 'B';
                const kode = this.selectedSubSub.kode;
                if (kode.startsWith('1.3.1')) return 'A';
                if (kode.startsWith('1.3.2')) return 'B';
                if (kode.startsWith('1.3.3')) return 'C';
                if (kode.startsWith('1.3.4')) return 'D';
                if (kode.startsWith('1.3.5')) return 'E';
                if (kode.startsWith('1.3.6')) return 'F';
                return 'B';
            },

            get isMesin() {
                return this.detectedKib === 'B';
            },

            get hasSelectedKib() {
                return !!this.selectedSubSub;
            },

            get kibLabel() {
                const map = {
                    'A': 'KIB A - Tanah',
                    'B': 'KIB B - Peralatan & Mesin',
                    'C': 'KIB C - Gedung & Bangunan',
                    'D': 'KIB D - Jalan, Jaringan & Irigasi',
                    'E': 'KIB E - Aset Tetap Lainnya',
                    'F': 'KIB F - KDP'
                };
                return map[this.detectedKib] || 'Peralatan & Perlengkapan';
            },

            formatRupiah(val) {
                if (!val || isNaN(val)) return 'Rp 0';
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(val));
            },

            // Logika Filter & Pencarian 108
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
                this.selectedSubSub = item;
                this.selectedSubSubId = item.id;
                this.formData.jenis_astap_id = item.id;
                if (!this.formData.nama_barang) {
                    this.formData.nama_barang = item.nama;
                }
                this.search108 = '';
                this.searchResults108 = [];
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
                this.selectedSubSub = null;
            },

            onJenisChange() {
                const jenis = this.jenisList.find(j => j.kode === this.selectedJenisKode);
                this.objekList = jenis ? (jenis.children || []) : [];
                this.selectedObjekKode = '';
                this.rincianList = [];
                this.selectedRincianKode = '';
                this.subSubList = [];
                this.selectedSubSubId = '';
                this.selectedSubSub = null;
            },

            onObjekChange() {
                const objek = this.objekList.find(o => o.kode === this.selectedObjekKode);
                this.rincianList = objek ? (objek.children || []) : [];
                this.selectedRincianKode = '';
                this.subSubList = [];
                this.selectedSubSubId = '';
                this.selectedSubSub = null;
            },

            onRincianChange() {
                const rincian = this.rincianList.find(r => r.kode === this.selectedRincianKode);
                this.subSubList = rincian ? (rincian.children || []) : [];
                this.selectedSubSubId = '';
                this.selectedSubSub = null;
            },

            onSubSubChange() {
                const subSub = this.subSubList.find(s => s.id == this.selectedSubSubId);
                this.selectedSubSub = subSub || null;
                this.formData.jenis_astap_id = subSub ? subSub.id : null;
                if (subSub && !this.formData.nama_barang) {
                    this.formData.nama_barang = subSub.nama;
                }
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
                        alert('⚠️ Mohon masukkan Nomor Faktur / Nota Pembelian.');
                        return false;
                    }
                    if (!this.formData.rekening_tanggal_faktur) {
                        alert('⚠️ Mohon masukkan Tanggal Faktur / Pembelian.');
                        return false;
                    }
                    if (!this.formData.total_realisasi || this.formData.total_realisasi <= 0) {
                        alert('⚠️ Mohon masukkan Total Nilai Pembelian (Rp) lebih dari 0.');
                        return false;
                    }
                    return true;
                }
                if (s === 2) {
                    if (!this.formData.jenis_astap_id) {
                        alert('⚠️ Mohon pilih Klasifikasi Kode Barang Permendagri 108.');
                        return false;
                    }
                    if (!this.formData.nama_barang.trim()) {
                        alert('⚠️ Mohon isi Nama Barang Spesifik.');
                        return false;
                    }
                    if (!this.formData.jumlah_volume || this.formData.jumlah_volume < 1) {
                        alert('⚠️ Mohon isi Jumlah Volume minimal 1 unit.');
                        return false;
                    }
                    return true;
                }
                if (s === 3) {
                    if (!this.formData.unit_id) {
                        alert('⚠️ Mohon pilih Unit / Ruangan Penempatan Inventaris (KIR).');
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

                fetch("{{ route('astap.store_belanja_barang') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(this.formData)
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(result => {
                    this.isSubmitting = false;
                    if (result.status === 200 && result.body.success) {
                        alert('🎉 Berhasil! ' + (result.body.message || 'Data Belanja Barang berhasil disimpan.'));
                        window.location.href = "{{ route('master.belanja_barang') }}";
                    } else {
                        const errMsg = result.body.message || (result.body.errors ? Object.values(result.body.errors).flat().join('\n') : 'Gagal menyimpan data.');
                        alert('❌ Terjadi Kesalahan:\n' + errMsg);
                    }
                })
                .catch(err => {
                    this.isSubmitting = false;
                    console.error(err);
                    alert('❌ Gagal menghubungi server. Silakan coba kembali.');
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
