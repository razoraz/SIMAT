<!-- ========================================================================= -->
<!-- ALPINE.JS STATE & SCRIPT LOGIC (KEMITRAAN PIHAK KETIGA 1.5.2)             -->
<!-- ========================================================================= -->
<script>
    window.dbMasterJenisAstap108 = @json(!empty($dbMaster108) ? $dbMaster108 : []);
    window.dbUnits = @json(!empty($dbUnits) ? $dbUnits : []);
    window.dbPejabats = @json(!empty($dbPejabats) ? $dbPejabats : []);
    window.dbPenyedias = @json(!empty($dbPenyedias) ? $dbPenyedias : []);
    window.dbMitraKemitraans = @json(!empty($dbMitraKemitraans) ? $dbMitraKemitraans : []);

    function formKemitraan() {
        return {
            currentStep: 1,
            totalSteps: 3,
            isSubmitting: false,

            // Autocomplete Riwayat Mitra Kemitraan
            masterMitraList: (window.dbMitraKemitraans && window.dbMitraKemitraans.length > 0)
                ? window.dbMitraKemitraans
                : [
                    'PT. Roche Indonesia',
                    'PT. Fresenius Medical Care Indonesia',
                    'PT. Kimia Farma Diagnostika',
                    'PT. Sysmex Indonesia',
                    'Mitra Swasta Pengembang (BGS)'
                ],
            isMitraDropdownOpen: false,

            get filteredMitraList() {
                const q = (this.formData.mitra_nama || '').toLowerCase().trim();
                if (!q) {
                    return this.masterMitraList.slice(0, 15);
                }
                return this.masterMitraList.filter(m => m && m.toLowerCase().includes(q));
            },

            selectMitra(name) {
                this.formData.mitra_nama = name;
                this.isMitraDropdownOpen = false;
            },

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

            formatRupiah(val) {
                if (!val) return '0';
                return Number(val).toLocaleString('id-ID');
            },

            // Form Data Payload (Sesuai Controller backend `astap.store_kemitraan`)
            formData: {
                // Step 1: Legalitas PKS & Mitra
                mitra_nama: '',
                nomor_pks: '',
                tanggal_pks: '{{ date('d/m/Y') }}',
                skema_kemitraan: 'KSO',
                tanggal_mulai: '',
                tanggal_selesai: '',
                tahun_perolehan: {{ date('Y') }},
                triwulan: 'TW I',
                kemitraan_keterangan: '',

                // Step 2: Klasifikasi 108 & Nilai Aset
                nama_barang: '',
                jenis_astap_id: null,
                jumlah_volume: 1,
                satuan: 'Unit',
                total_realisasi: 0,

                // Step 3: Rincian Fisik, Ruangan & PPK
                unit_id: '',
                kondisi: 'Baik',
                alamat_barang: 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1',
                ppk_nama: '',
                ppk_nip: '',
                merk: '',
                type: '',
                no_pabrik: '',
                bahan: '',
                ukuran: ''
            },

            // Master Data & Filtering
            pejabatsList: window.dbPejabats || [],
            master108Raw: window.dbMasterJenisAstap108 || {},
            master108: [],
            flat108: [],
            search108: '',
            searchResults108: [],

            selectedJenisIdx: '',
            selectedSubIdx: '',
            currentSubList: [],
            currentSubSubList: [],
            selectedSubSub: null,

            init() {
                this.prepareMaster108();
            },

            prepareMaster108() {
                const parsed = [];
                const flat = [];

                Object.entries(this.master108Raw || {}).forEach(([jenisKode, jObj]) => {
                    if (!jObj || typeof jObj !== 'object') return;
                    const subList = [];
                    Object.entries(jObj).forEach(([subKode, sObj]) => {
                        if (!sObj || !Array.isArray(sObj)) return;
                        const ssList = [];
                        sObj.forEach(item => {
                            const entry = {
                                id: item.id,
                                kode: item.kode,
                                nama: item.nama
                            };
                            ssList.push(entry);
                            flat.push({
                                ...entry,
                                jenisKode: jenisKode,
                                subKode: subKode
                            });
                        });
                        subList.push({
                            kode: subKode,
                            nama: sObj[0]?.nama_sub || subKode,
                            subSubs: ssList
                        });
                    });
                    parsed.push({
                        kode: jenisKode,
                        nama: jObj[Object.keys(jObj)[0]]?.[0]?.nama_jenis || ('Kelompok ' + jenisKode),
                        subs: subList
                    });
                });

                this.master108 = parsed;
                this.flat108 = flat;
            },

            // Live Search 108
            performSearch108() {
                const q = (this.search108 || '').trim().toLowerCase();
                if (!q || q.length < 2) {
                    this.searchResults108 = [];
                    return;
                }
                this.searchResults108 = this.flat108.filter(it => 
                    it.kode.toLowerCase().includes(q) || it.nama.toLowerCase().includes(q)
                ).slice(0, 15);
            },

            selectFromSearch(item) {
                this.selectedSubSub = item;
                this.formData.jenis_astap_id = item.id;
                if (!this.formData.nama_barang) {
                    this.formData.nama_barang = item.nama;
                }
                this.search108 = '';
                this.searchResults108 = [];
                this.showToast('Klasifikasi Terpilih', `${item.kode} • ${item.nama}`, 'info');
            },

            // Quick select shortcut Akun 1.5.2
            quickSelectKemitraan(prefixKode) {
                const found = this.flat108.find(it => it.kode.startsWith(prefixKode));
                if (found) {
                    this.selectFromSearch(found);
                } else {
                    // Fallback: cari yang ada kode 1.5.2 atau kata kemitraan
                    const fallback = this.flat108.find(it => it.kode.startsWith('1.5.2') || it.nama.toLowerCase().includes('kemitraan'));
                    if (fallback) {
                        this.selectFromSearch(fallback);
                    } else {
                        this.showToast('Informasi', `Kode akun ${prefixKode} belum terdaftar di master jenis astap, silakan cari manual.`, 'warning');
                    }
                }
            },

            onJenisChange() {
                if (this.selectedJenisIdx === '') {
                    this.currentSubList = [];
                    this.currentSubSubList = [];
                    this.selectedSubIdx = '';
                    return;
                }
                this.currentSubList = this.master108[this.selectedJenisIdx]?.subs || [];
                this.currentSubSubList = [];
                this.selectedSubIdx = '';
            },

            onSubChange() {
                if (this.selectedSubIdx === '') {
                    this.currentSubSubList = [];
                    return;
                }
                this.currentSubSubList = this.currentSubList[this.selectedSubIdx]?.subSubs || [];
            },

            onSubSubChange(e) {
                const id = parseInt(e.target.value);
                const it = this.flat108.find(x => x.id === id);
                if (it) {
                    this.selectFromSearch(it);
                }
            },

            onPpkSelect() {
                const found = this.pejabatsList.find(p => p.nama === this.formData.ppk_nama);
                if (found) {
                    this.formData.ppk_nip = found.nip || '';
                } else {
                    this.formData.ppk_nip = '';
                }
            },

            // Stepper Navigation
            goToStep(s) {
                if (s > this.currentStep) {
                    for (let stepCheck = this.currentStep; stepCheck < s; stepCheck++) {
                        if (!this.validateStep(stepCheck)) return;
                    }
                }
                this.currentStep = s;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            nextStep() {
                if (this.validateStep(this.currentStep)) {
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
                    if (!this.formData.mitra_nama || !this.formData.mitra_nama.trim()) {
                        this.showToast('Validasi Gagal', 'Mohon isi nama perusahaan mitra / rekanan pihak ketiga.', 'error');
                        return false;
                    }
                    if (!this.formData.nomor_pks || !this.formData.nomor_pks.trim()) {
                        this.showToast('Validasi Gagal', 'Mohon isi nomor dokumen Perjanjian Kerja Sama (PKS).', 'error');
                        return false;
                    }
                    if (!this.formData.tanggal_pks) {
                        this.showToast('Validasi Gagal', 'Mohon isi tanggal penandatanganan PKS.', 'error');
                        return false;
                    }
                    if (!this.formData.tahun_perolehan) {
                        this.showToast('Validasi Gagal', 'Mohon tentukan tahun pembukuan.', 'error');
                        return false;
                    }
                } else if (s === 2) {
                    if (!this.formData.jenis_astap_id) {
                        this.showToast('Validasi Gagal', 'Mohon pilih klasifikasi kode barang 108 (rekomendasi Akun 1.5.2 Kemitraan).', 'error');
                        return false;
                    }
                    if (!this.formData.nama_barang || !this.formData.nama_barang.trim()) {
                        this.showToast('Validasi Gagal', 'Mohon isi nama spesifik barang kemitraan.', 'error');
                        return false;
                    }
                    if (!this.formData.jumlah_volume || this.formData.jumlah_volume < 1) {
                        this.showToast('Validasi Gagal', 'Jumlah volume barang minimal 1 unit.', 'error');
                        return false;
                    }
                    if (!this.formData.satuan || !this.formData.satuan.trim()) {
                        this.showToast('Validasi Gagal', 'Mohon isi satuan barang (contoh: Unit, Set, Buah).', 'error');
                        return false;
                    }
                    if (!this.formData.total_realisasi || this.formData.total_realisasi <= 0) {
                        this.showToast('Validasi Gagal', 'Mohon masukkan total taksiran nilai wajar aset kemitraan (Rp).', 'error');
                        return false;
                    }
                }
                return true;
            },

            // Submit Form via AJAX
            async submitForm() {
                if (!this.validateStep(1) || !this.validateStep(2)) return;

                if (!this.formData.unit_id) {
                    this.showToast('Validasi Gagal', 'Mohon pilih unit / ruangan penempatan barang di RSUD.', 'error');
                    return;
                }

                this.isSubmitting = true;

                try {
                    const res = await fetch('{{ route('astap.store_kemitraan') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    const json = await res.json();

                    if (res.ok && json.success) {
                        this.showToast('Berhasil Disimpan!', json.message || 'Data Aset Kemitraan berhasil dicatat ke SIMAT-RK.', 'success');
                        setTimeout(() => {
                            window.location.href = json.redirect || '{{ route('astap.index') }}';
                        }, 1200);
                    } else {
                        let errMsg = json.message || 'Terjadi kesalahan saat menyimpan data.';
                        if (json.errors) {
                            errMsg += '\n' + Object.values(json.errors).flat().join('\n');
                        }
                        this.showToast('Gagal Menyimpan', errMsg, 'error');
                    }
                } catch (err) {
                    console.error(err);
                    this.showToast('Kesalahan Jaringan', 'Terjadi kesalahan jaringan atau server saat menyimpan data.', 'error');
                } finally {
                    this.isSubmitting = false;
                }
            }
        };
    }
</script>
