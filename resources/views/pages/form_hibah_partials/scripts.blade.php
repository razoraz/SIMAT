<script>
    window.dbMasterJenisAstap108 = @json(!empty($dbMaster108) ? $dbMaster108 : []);
    window.dbRekeningBelanjas = @json(!empty($dbRekeningBelanjas) ? $dbRekeningBelanjas : []);
    window.dbUnits = @json(!empty($dbUnits) ? $dbUnits : []);
    window.dbPenyedias = @json(!empty($dbPenyedias) ? $dbPenyedias : []);
    window.dbPejabats = @json(!empty($dbPejabats) ? $dbPejabats : []);

    function formHibah() {
        return {
            currentStep: 1,
            totalSteps: 3,
            isSubmitting: false,

            // Master Data
            master108: window.dbMasterJenisAstap108 || [],
            masterRekeningBelanja: window.dbRekeningBelanjas || [],
            pejabatsList: window.dbPejabats || [],
            unitsList: window.dbUnits || [],

            // Filter States (Sama Persis seperti Belanja Modal)
            searchRekening: '',
            isRekeningOpen: false,
            searchJenis108: '',
            isJenis108Open: false,
            searchSubRincian108: '',
            isSubRincian108Open: false,
            searchNamaBarang108: '',
            isNamaBarang108Open: false,

            // Active Selected Item
            selectedSubSub: null,

            // Toast State
            toast: {
                show: false,
                title: '',
                message: '',
                type: 'success'
            },

            showToast(title, message, type = 'success') {
                this.toast.title = title;
                this.toast.message = message;
                this.toast.type = type;
                this.toast.show = true;
                setTimeout(() => {
                    this.toast.show = false;
                }, 5000);
            },

            // Form Data Payload
            formData: {
                // Langkah 1: BAST & Pemberi
                tahun_perolehan: new Date().getFullYear(),
                triwulan: 'TW I',
                hibah_pemberi: '',
                hibah_nomor_bast: '',
                hibah_tanggal_bast: new Date().toISOString().split('T')[0],
                total_realisasi: 0,
                jumlah_realisasi: 0,

                // Langkah 2: Rekening & 108
                rekening_belanja_id: null,
                kode_rek: '',
                nama_belanja: '',
                jenis_astap_id: null,
                jenis_aset_kode: '',
                jenis_aset_nama: '',
                sub_rincian_kode: '',
                sub_rincian_nama: '',
                nama_barang: '',
                satuan: 'Unit',
                jumlah_volume: 1,
                is_extracomtable: false,

                // Langkah 3: Rincian KIB
                // KIB A (Tanah) Repeater
                tanah_items: [
                    {
                        tanah_hak: 'Hak Pakai',
                        tanah_sertifikat_no: '',
                        tanah_sertifikat_tgl: '',
                        tanah_kondisi: 'Baik',
                        tanah_penggunaan: 'Bangunan Fasilitas Kesehatan & Pelayanan Rumah Sakit',
                        tanah_jumlah_bidang: 1,
                        tanah_luas_m2: '',
                        tanah_alamat: '',
                        tanah_nilai_fisik: 0
                    }
                ],

                // KIB B (Peralatan & Mesin)
                merk: '',
                type: '',
                no_pabrik: '',
                ukuran: '',
                bahan: '',
                kondisi: 'Baik',
                no_rangka: '',
                no_mesin: '',
                no_polisi: '',

                // KIB C (Gedung)
                gedung_luas_m2: '',
                gedung_bertingkat: 'Tidak',
                gedung_beton: 'Beton',

                // KIB D (Jaringan)
                jaringan_panjang_m: '',
                jaringan_lebar_m: '',
                jaringan_luas_m2: '',

                // ATB
                atb_jenis: 'Software / Aplikasi Sistem Informasi',
                atb_masa_manfaat: 4,

                // Penempatan Ruangan & PPK
                unit_id: '',
                alamat_barang: 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1',
                ppk_nama: '',
                ppk_nip: '',
                hibah_keterangan: ''
            },

            init() {
                if (this.pejabatsList.length > 0) {
                    this.formData.ppk_nama = this.pejabatsList[0].nama || '';
                    this.formData.ppk_nip = this.pejabatsList[0].nip || '';
                }
            },

            // ─── Filtered Getters (Sama Persis seperti Belanja Modal) ──────────
            get filteredRekeningBelanja() {
                const q = (this.searchRekening || '').toLowerCase().trim();
                if (!q) return this.masterRekeningBelanja;
                return this.masterRekeningBelanja.filter(r => 
                    (r.kode_rek && r.kode_rek.toLowerCase().includes(q)) ||
                    (r.nama_belanja && r.nama_belanja.toLowerCase().includes(q)) ||
                    (r.kelompok && r.kelompok.toLowerCase().includes(q))
                );
            },

            get filteredJenisAstap108() {
                const q = (this.searchJenis108 || '').toLowerCase().trim();
                if (!q) return this.master108;
                return this.master108.filter(j => 
                    (j.kode && j.kode.toLowerCase().includes(q)) ||
                    (j.nama && j.nama.toLowerCase().includes(q))
                );
            },

            get filteredSubRincian108() {
                let list = [];
                if (this.formData.jenis_aset_kode) {
                    const found = this.master108.find(j => j.kode === this.formData.jenis_aset_kode);
                    if (found && found.subRincian) list = found.subRincian;
                } else {
                    this.master108.forEach(j => {
                        if (j.subRincian) list = list.concat(j.subRincian);
                    });
                }
                const q = (this.searchSubRincian108 || '').toLowerCase().trim();
                if (!q) return list;
                return list.filter(s => 
                    (s.kode && s.kode.toLowerCase().includes(q)) ||
                    (s.nama && s.nama.toLowerCase().includes(q))
                );
            },

            get filteredSubSubRincian108() {
                let list = [];
                if (this.formData.sub_rincian_kode) {
                    const found = this.filteredSubRincian108.find(s => s.kode === this.formData.sub_rincian_kode);
                    if (found && found.subSubRincian) list = found.subSubRincian;
                } else if (this.formData.jenis_aset_kode) {
                    const foundJ = this.master108.find(j => j.kode === this.formData.jenis_aset_kode);
                    if (foundJ && foundJ.subRincian) {
                        foundJ.subRincian.forEach(s => {
                            if (s.subSubRincian) list = list.concat(s.subSubRincian);
                        });
                    }
                } else {
                    this.master108.forEach(j => {
                        if (j.subRincian) {
                            j.subRincian.forEach(s => {
                                if (s.subSubRincian) list = list.concat(s.subSubRincian);
                            });
                        }
                    });
                }
                const q = (this.searchNamaBarang108 || '').toLowerCase().trim();
                if (!q) return list.slice(0, 50);
                return list.filter(item => 
                    (item.kode && item.kode.toLowerCase().includes(q)) ||
                    (item.nama && item.nama.toLowerCase().includes(q))
                ).slice(0, 50);
            },

            get activeKodeBarang() {
                return this.selectedSubSub?.kode || '';
            },

            get activeNamaBarang() {
                return this.selectedSubSub?.nama || this.formData.nama_barang || '';
            },

            // ─── Selection Handlers ─────────────────────────────────────────
            selectRekening(r) {
                this.formData.rekening_belanja_id = r.id || null;
                this.formData.kode_rek = r.kode_rek;
                this.formData.nama_belanja = r.nama_belanja;
                this.isRekeningOpen = false;
                this.searchRekening = '';

                // Otomatis arahkan jenis aset jika cocok dengan kelompok rekening
                if (r.kelompok === 'Tanah') {
                    this.setJenisAsetByKode('1.3.1');
                } else if (r.kelompok === 'Bangunan' || r.kelompok === 'Gedung') {
                    this.setJenisAsetByKode('1.3.3');
                } else if (r.kelompok === 'Jalan' || r.kelompok === 'Jaringan') {
                    this.setJenisAsetByKode('1.3.4');
                }
            },

            setJenisAsetByKode(kode) {
                const j = this.master108.find(item => item.kode === kode);
                if (j) this.selectJenisAstap(j);
            },

            selectJenisAstap(j) {
                this.formData.jenis_aset_kode = j.kode;
                this.formData.jenis_aset_nama = j.nama;
                this.isJenis108Open = false;
                this.searchJenis108 = '';

                // Reset sub rincian & barang jika ganti kelompok jenis
                this.formData.sub_rincian_kode = '';
                this.formData.sub_rincian_nama = '';
                this.selectedSubSub = null;
                this.formData.jenis_astap_id = null;

                this.adjustSatuanForKib();
            },

            selectSubRincian(s) {
                this.formData.sub_rincian_kode = s.kode;
                this.formData.sub_rincian_nama = s.nama;
                this.isSubRincian108Open = false;
                this.searchSubRincian108 = '';

                // Otomatis isi parent jenis aset jika belum terpilih
                if (!this.formData.jenis_aset_kode) {
                    for (const j of this.master108) {
                        if (j.subRincian && j.subRincian.some(sub => sub.kode === s.kode)) {
                            this.formData.jenis_aset_kode = j.kode;
                            this.formData.jenis_aset_nama = j.nama;
                            break;
                        }
                    }
                }
                this.adjustSatuanForKib();
            },

            selectSubSubRincianItem(item) {
                this.selectedSubSub = item;
                this.formData.jenis_astap_id = item.id;
                this.formData.nama_barang = item.nama;
                this.isNamaBarang108Open = false;
                this.searchNamaBarang108 = '';

                // Otomatis cari dan isi parent jika belum terpilih
                let foundSub = null;
                let foundJenis = null;
                for (const j of this.master108) {
                    if (!j.subRincian) continue;
                    for (const s of j.subRincian) {
                        if (s.subSubRincian && s.subSubRincian.some(ss => ss.kode === item.kode || ss.id === item.id)) {
                            foundSub = s;
                            foundJenis = j;
                            break;
                        }
                    }
                    if (foundJenis) break;
                }

                if (foundJenis) {
                    this.formData.jenis_aset_kode = foundJenis.kode;
                    this.formData.jenis_aset_nama = foundJenis.nama;
                }
                if (foundSub) {
                    this.formData.sub_rincian_kode = foundSub.kode;
                    this.formData.sub_rincian_nama = foundSub.nama;
                }

                this.adjustSatuanForKib();
            },

            // ─── KIB Detection Getters ──────────────────────────────────────
            get isTanah() {
                const k = this.formData.jenis_aset_kode || '';
                return k.startsWith('1.3.1') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('tanah');
            },

            get isMesin() {
                const k = this.formData.jenis_aset_kode || '';
                return k.startsWith('1.3.2') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('peralatan');
            },

            get isGedung() {
                const k = this.formData.jenis_aset_kode || '';
                return k.startsWith('1.3.3') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('gedung');
            },

            get isJaringan() {
                const k = this.formData.jenis_aset_kode || '';
                return k.startsWith('1.3.4') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('jaringan') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('jalan');
            },

            get isAsetLainnya() {
                const k = this.formData.jenis_aset_kode || '';
                return k.startsWith('1.3.5') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('lainnya');
            },

            get isKdp() {
                const k = this.formData.jenis_aset_kode || '';
                return k.startsWith('1.3.6') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('konstruksi');
            },

            get isAtb() {
                const k = this.formData.jenis_aset_kode || '';
                return k.startsWith('1.5') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('berwujud') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('software');
            },

            get hasSelectedKib() {
                return !!(this.formData.jenis_aset_kode || this.formData.jenis_astap_id);
            },

            get kibLabel() {
                if (this.isTanah) return 'KIB A (Tanah)';
                if (this.isMesin) return 'KIB B (Peralatan & Mesin)';
                if (this.isGedung) return 'KIB C (Gedung & Bangunan)';
                if (this.isJaringan) return 'KIB D (Jalan, Irigasi & Jaringan)';
                if (this.isAsetLainnya) return 'KIB E (Aset Tetap Lainnya)';
                if (this.isAtb) return 'ATB (Aset Tidak Berwujud)';
                if (this.isKdp) return 'KIB F (Konstruksi Dalam Pengerjaan)';
                return 'Aset Tetap Hibah';
            },

            adjustSatuanForKib() {
                if (this.isTanah) {
                    this.formData.satuan = 'Bidang';
                    this.syncTanahFieldsToMain();
                } else if (this.isGedung) {
                    this.formData.satuan = 'Gedung';
                } else if (this.isJaringan) {
                    this.formData.satuan = 'M²';
                } else if (this.isAtb) {
                    this.formData.satuan = 'Paket';
                } else {
                    this.formData.satuan = 'Unit';
                }
            },

            // ─── Tanah Repeater Handlers ────────────────────────────────────
            get totalLuasTanah() {
                if (!this.formData.tanah_items) return 0;
                return this.formData.tanah_items.reduce((s, it) => s + (parseFloat(it.tanah_luas_m2) || 0), 0);
            },

            addTanahItem() {
                this.formData.tanah_items.push({
                    tanah_hak: 'Hak Pakai',
                    tanah_sertifikat_no: '',
                    tanah_sertifikat_tgl: '',
                    tanah_kondisi: 'Baik',
                    tanah_penggunaan: 'Bangunan Fasilitas Pelayanan Rumah Sakit',
                    tanah_jumlah_bidang: 1,
                    tanah_luas_m2: '',
                    tanah_alamat: '',
                    tanah_nilai_fisik: 0
                });
                this.syncTanahFieldsToMain();
            },

            removeTanahItem(index) {
                if (this.formData.tanah_items.length > 1) {
                    this.formData.tanah_items.splice(index, 1);
                    this.syncTanahFieldsToMain();
                }
            },

            syncTanahFieldsToMain() {
                if (this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                    const first = this.formData.tanah_items[0];
                    this.formData.kondisi = first.tanah_kondisi;
                    const totalBidang = this.formData.tanah_items.reduce((s, it) => s + (parseInt(it.tanah_jumlah_bidang) || 1), 0);
                    this.formData.jumlah_volume = totalBidang;
                    this.formData.satuan = 'Bidang';
                    if (first.tanah_alamat) {
                        this.formData.alamat_barang = first.tanah_alamat;
                    }
                }
            },

            syncRealisasiFromStep3() {
                this.formData.jumlah_realisasi = this.formData.total_realisasi;
            },

            onPpkSelect() {
                const found = this.pejabatsList.find(p => p.nama === this.formData.ppk_nama);
                if (found) {
                    this.formData.ppk_nip = found.nip || '';
                }
            },

            // ─── Utilities ──────────────────────────────────────────────────
            formatRupiah(val) {
                const num = Number(val || 0);
                return num.toLocaleString('id-ID');
            },

            formatTanggalIndo(dateStr) {
                if (!dateStr) return '-';
                try {
                    const d = new Date(dateStr);
                    if (isNaN(d.getTime())) return dateStr;
                    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                } catch {
                    return dateStr;
                }
            },

            // ─── Multi-Step Navigation & Validation ─────────────────────────
            goToStep(s) {
                if (s > this.currentStep) {
                    for (let i = this.currentStep; i < s; i++) {
                        if (!this.validateStep(i)) return;
                    }
                }
                this.currentStep = s;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            nextStep() {
                if (!this.validateStep(this.currentStep)) return;
                if (this.currentStep < this.totalSteps) {
                    this.currentStep++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            validateStep(s) {
                if (s === 1) {
                    if (!this.formData.tahun_perolehan) {
                        alert('⚠️ Mohon tentukan Tahun Anggaran Pembukuan.');
                        return false;
                    }
                    if (!this.formData.triwulan) {
                        alert('⚠️ Mohon tentukan Triwulan Pembukuan.');
                        return false;
                    }
                    if (!this.formData.hibah_pemberi.trim()) {
                        alert('⚠️ Mohon isi Nama Instansi Pemberi Hibah.');
                        return false;
                    }
                    if (!this.formData.hibah_nomor_bast.trim()) {
                        alert('⚠️ Mohon isi Nomor BAST Hibah.');
                        return false;
                    }
                    if (!this.formData.hibah_tanggal_bast) {
                        alert('⚠️ Mohon isi Tanggal BAST Hibah.');
                        return false;
                    }
                    return true;
                }

                if (s === 2) {
                    if (!this.formData.jenis_astap_id && !this.formData.jenis_aset_kode) {
                        alert('⚠️ Mohon pilih Klasifikasi Jenis Aset / Kode Barang 108.');
                        return false;
                    }
                    if (!this.formData.nama_barang || !this.formData.nama_barang.trim()) {
                        alert('⚠️ Mohon isi Nama Lengkap Barang.');
                        return false;
                    }
                    if (!this.formData.jumlah_volume || this.formData.jumlah_volume < 1) {
                        alert('⚠️ Mohon isi Volume / Kuantitas Barang.');
                        return false;
                    }
                    return true;
                }

                if (s === 3) {
                    if (this.isTanah) {
                        this.syncTanahFieldsToMain();
                    }
                    if (!this.formData.unit_id) {
                        alert('⚠️ Mohon pilih Unit / Ruangan Penempatan Aset (KIR).');
                        return false;
                    }
                    return true;
                }

                return true;
            },

            submitForm() {
                if (!this.validateStep(1) || !this.validateStep(2) || !this.validateStep(3)) return;

                if (this.isTanah) {
                    this.syncTanahFieldsToMain();
                }

                this.isSubmitting = true;
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                fetch("{{ route('astap.store_hibah') }}", {
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
                        alert('🎉 Berhasil! ' + (result.body.message || 'Data Hibah berhasil disimpan ke database.'));
                        window.location.href = "{{ route('master.hibah') }}";
                    } else {
                        const errMsg = result.body.message || (result.body.errors ? Object.values(result.body.errors).flat().join('\n') : 'Gagal menyimpan data hibah.');
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
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(245, 158, 11, 0.4);
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(245, 158, 11, 0.7);
    }
</style>
