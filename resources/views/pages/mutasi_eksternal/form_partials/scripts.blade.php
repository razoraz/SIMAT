    <!-- Alpine.js Script Implementation -->
    <script>
        function formMutasiEksternal() {
            return {
                step: 1,
                isSubmitting: false,
                isEdit: {{ Js::from($isEdit) }},
                astapId: {{ Js::from($isEdit ? $astap->id : null) }},
                initialAstap: {{ Js::from($initialAstap) }},
                master108: @json($dbMaster108 ?? []),
                pejabatsList: @json($dbPejabats ?? []),

                // Form State
                formData: {
                    from: {{ Js::from(request('from')) }},
                    sumber_dana: 'pelimpahan_skpd',
                    tahun_perolehan: new Date().getFullYear(),
                    triwulan: 'TW I',
                    mutasi_asal: '',
                    mutasi_nomor_bamb: '',
                    mutasi_tanggal: new Date().toISOString().split('T')[0],
                    total_realisasi: 0,
                    mutasi_keterangan: '',
                    nama_barang: '',
                    jenis_astap_id: '',
                    jumlah_volume: 1,
                    satuan: 'Unit',
                    kondisi: 'Baik',
                    unit_id: '',
                    alamat_barang: 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1',
                    ppk_nama: '',
                    ppk_nip: '',
                    nomor_sk_dasar: '',
                    pj_asal_nama: '',
                    pj_asal_nip: '',
                    pj_asal_jabatan: '',
                    dokumen_lampiran_path: '',
                    
                    // Spesifikasi Tanah (KIB A)
                    tanah_items: [
                        {
                            tanah_hak: 'Hak Pakai',
                            tanah_sertifikat_tgl: '',
                            tanah_sertifikat_no: '',
                            tanah_kondisi: 'Baik',
                            tanah_penggunaan: 'Bangunan Fasilitas Kesehatan & Pelayanan Rumah Sakit',
                            tanah_jumlah_bidang: 1,
                            tanah_luas_m2: '',
                            tanah_alamat: '',
                            tanah_nilai_fisik: 0
                        }
                    ],
                    sertifikat_nomor: '',

                    // Spesifikasi Peralatan & Mesin (KIB B)
                    merk: '',
                    type: '',
                    no_pabrik: '',
                    ukuran: '',
                    bahan: '',
                    no_rangka: '',
                    no_mesin: '',
                    no_polisi: '',

                    // Spesifikasi Gedung & Bangunan (KIB C)
                    gedung_luas_m2: '',
                    gedung_bertingkat: 'Tidak',
                    gedung_beton: 'Beton',
                    gedung_status_tanah: 'Tanah Pemda'
                },

                selectedFile: null,

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        if (file.size > 10 * 1024 * 1024) {
                            alert('⚠️ Ukuran file maksimal adalah 10 MB.');
                            event.target.value = '';
                            return;
                        }
                        this.selectedFile = file;
                    }
                },

                // 108 Selection State
                selectedJenisIdx: '',
                selectedSubIdx: '',
                selectedSubSub: null,
                currentSubList: [],
                currentSubSubList: [],
                search108: '',
                searchResults108: [],
                allFlattened108: [],

                init() {
                    this.flatten108();

                    if (this.isEdit && this.initialAstap) {
                        Object.assign(this.formData, this.initialAstap);

                        // Aktifkan pilihan kode 108 & KIB yang sesuai
                        if (this.formData.jenis_astap_id) {
                            const found = this.allFlattened108.find(x => Number(x.id) === Number(this.formData.jenis_astap_id));
                            if (found) {
                                const originalNama = this.formData.nama_barang;
                                this.selectFromSearch(found);
                                if (originalNama) {
                                    this.formData.nama_barang = originalNama;
                                }
                            }
                        }
                    } else {
                        const m = new Date().getMonth() + 1;
                        if (m >= 1 && m <= 3) this.formData.triwulan = 'TW I';
                        else if (m >= 4 && m <= 6) this.formData.triwulan = 'TW II';
                        else if (m >= 7 && m <= 9) this.formData.triwulan = 'TW III';
                        else this.formData.triwulan = 'TW IV';

                        if (this.pejabatsList && this.pejabatsList.length > 0) {
                            this.formData.ppk_nama = this.pejabatsList[0].nama;
                            this.formData.ppk_nip = this.pejabatsList[0].nip || '';
                        }
                    }
                },

                get hargaSatuanHitung() {
                    const v = parseInt(this.formData.jumlah_volume) || 1;
                    const r = parseFloat(this.formData.total_realisasi) || 0;
                    return Math.round(r / Math.max(1, v));
                },

                get selectedKibKode() {
                    if (this.selectedSubSub && this.selectedSubSub.jenisKode) {
                        return this.selectedSubSub.jenisKode;
                    }
                    if (this.selectedJenisIdx !== '' && this.master108[this.selectedJenisIdx]) {
                        return this.master108[this.selectedJenisIdx].kode;
                    }
                    return '';
                },

                get selectedKibNama() {
                    if (this.selectedSubSub && this.selectedSubSub.jenisNama) {
                        return this.selectedSubSub.jenisNama;
                    }
                    if (this.selectedJenisIdx !== '' && this.master108[this.selectedJenisIdx]) {
                        return this.master108[this.selectedJenisIdx].nama;
                    }
                    return '';
                },

                get isTanah() {
                    return Boolean(this.selectedKibKode.startsWith('1.3.1') || this.selectedKibNama.includes('TANAH'));
                },

                get isMesin() {
                    return Boolean(this.selectedKibKode.startsWith('1.3.2') || this.selectedKibNama.includes('PERALATAN') || this.selectedKibNama.includes('MESIN'));
                },

                get isGedung() {
                    return Boolean(this.selectedKibKode.startsWith('1.3.3') || this.selectedKibNama.includes('GEDUNG') || this.selectedKibNama.includes('BANGUNAN'));
                },

                get hasSelectedKib() {
                    return Boolean(this.selectedKibKode || (this.selectedJenisIdx !== '' && this.selectedJenisIdx !== null && this.selectedJenisIdx !== undefined));
                },

                get kibLabel() {
                    if (!this.hasSelectedKib) return 'Belum Dipilih';
                    if (this.isTanah) return 'Rincian Tanah';
                    if (this.isGedung) return 'Rincian Gedung';
                    if (this.isMesin) return 'Rincian Mesin & Alat';
                    return 'Rincian Aset Tetap';
                },

                get kibBadgeIcon() {
                    if (!this.hasSelectedKib) return '🔍';
                    if (this.isTanah) return '🌾';
                    if (this.isGedung) return '🏢';
                    if (this.isMesin) return '⚙️';
                    return '📦';
                },

                get totalLuasTanah() {
                    if (!this.formData.tanah_items || !this.formData.tanah_items.length) return 0;
                    return this.formData.tanah_items.reduce((acc, curr) => acc + (parseFloat(curr.tanah_luas_m2) || 0), 0);
                },

                addTanahItem() {
                    this.formData.tanah_items.push({
                        tanah_hak: 'Hak Pakai',
                        tanah_sertifikat_tgl: '',
                        tanah_sertifikat_no: '',
                        tanah_kondisi: 'Baik',
                        tanah_penggunaan: 'Bangunan Fasilitas Kesehatan & Pelayanan Rumah Sakit',
                        tanah_jumlah_bidang: 1,
                        tanah_luas_m2: '',
                        tanah_alamat: '',
                        tanah_nilai_fisik: 0
                    });
                    this.syncTanahFields();
                },

                removeTanahItem(idx) {
                    if (this.formData.tanah_items.length > 1) {
                        this.formData.tanah_items.splice(idx, 1);
                        this.syncTanahFields();
                    }
                },

                syncTanahFields() {
                    if (!this.formData.tanah_items.length) return;
                    const first = this.formData.tanah_items[0];
                    this.formData.sertifikat_nomor = first.tanah_sertifikat_no || '';
                    this.formData.jumlah_volume = this.formData.tanah_items.length;
                    this.formData.satuan = 'Bidang';
                },

                flatten108() {
                    const list = [];
                    if (!this.master108 || !Array.isArray(this.master108)) return;
                    this.master108.forEach(j => {
                        if (j.sub_kategori) {
                            j.sub_kategori.forEach(s => {
                                if (s.sub_sub_kategori) {
                                    s.sub_sub_kategori.forEach(ss => {
                                        list.push({
                                            id: ss.id,
                                            kode: ss.kode,
                                            nama: ss.nama,
                                            jenisId: j.id,
                                            jenisKode: j.kode,
                                            jenisNama: j.nama,
                                            subId: s.id,
                                            subKode: s.kode
                                        });
                                    });
                                }
                            });
                        }
                    });
                    this.allFlattened108 = list;
                },

                performSearch108() {
                    if (!this.search108 || this.search108.length < 2) {
                        this.searchResults108 = [];
                        return;
                    }
                    const q = this.search108.toLowerCase();
                    this.searchResults108 = this.allFlattened108.filter(it => 
                        it.kode.toLowerCase().includes(q) || it.nama.toLowerCase().includes(q)
                    ).slice(0, 15);
                },

                selectFromSearch(item) {
                    this.selectedSubSub = item;
                    this.formData.jenis_astap_id = item.id;
                    if (!this.formData.nama_barang) {
                        this.formData.nama_barang = item.nama;
                    }
                    const jIdx = this.master108.findIndex(j => j.id === item.jenisId);
                    if (jIdx !== -1) {
                        this.selectedJenisIdx = jIdx;
                        this.currentSubList = this.master108[jIdx].sub_kategori || [];
                        const sIdx = this.currentSubList.findIndex(s => s.id === item.subId);
                        if (sIdx !== -1) {
                            this.selectedSubIdx = sIdx;
                            this.currentSubSubList = this.currentSubList[sIdx].sub_sub_kategori || [];
                        }
                    }
                    this.search108 = '';
                    this.searchResults108 = [];
                    if (this.isTanah) {
                        this.formData.satuan = 'Bidang';
                    } else if (this.formData.satuan === 'Bidang') {
                        this.formData.satuan = 'Unit';
                    }
                },

                onJenisChange() {
                    if (this.selectedJenisIdx === '') {
                        this.currentSubList = [];
                        this.currentSubSubList = [];
                        this.selectedSubIdx = '';
                        this.selectedSubSub = null;
                        this.formData.jenis_astap_id = '';
                        this.formData.satuan = 'Unit';
                        return;
                    }
                    this.currentSubList = this.master108[this.selectedJenisIdx].sub_kategori || [];
                    this.currentSubSubList = [];
                    this.selectedSubIdx = '';
                    this.selectedSubSub = null;
                    this.formData.jenis_astap_id = '';
                    if (this.isTanah) {
                        this.formData.satuan = 'Bidang';
                    } else if (this.formData.satuan === 'Bidang') {
                        this.formData.satuan = 'Unit';
                    }
                },

                onSubChange() {
                    if (this.selectedSubIdx === '') {
                        this.currentSubSubList = [];
                        this.selectedSubSub = null;
                        this.formData.jenis_astap_id = '';
                        return;
                    }
                    this.currentSubSubList = this.currentSubList[this.selectedSubIdx].sub_sub_kategori || [];
                    this.selectedSubSub = null;
                    this.formData.jenis_astap_id = '';
                },

                onSubSubChange(e) {
                    const id = parseInt(e.target.value);
                    if (!id) {
                        this.selectedSubSub = null;
                        this.formData.jenis_astap_id = '';
                        return;
                    }
                    const found = this.currentSubSubList.find(x => x.id === id);
                    if (found) {
                        const j = this.master108[this.selectedJenisIdx];
                        this.selectedSubSub = {
                            id: found.id,
                            kode: found.kode,
                            nama: found.nama,
                            jenisId: j ? j.id : null,
                            jenisKode: j ? j.kode : '',
                            jenisNama: j ? j.nama : ''
                        };
                        this.formData.jenis_astap_id = found.id;
                        if (!this.formData.nama_barang) {
                            this.formData.nama_barang = found.nama;
                        }
                    }
                },

                onPpkSelect() {
                    const p = this.pejabatsList.find(x => x.nama === this.formData.ppk_nama);
                    if (p) this.formData.ppk_nip = p.nip || '';
                },

                formatRupiah(val) {
                    const n = parseFloat(val) || 0;
                    return 'Rp ' + n.toLocaleString('id-ID');
                },

                formatTanggalIndo(dateStr) {
                    if (!dateStr) return '-';
                    const parts = dateStr.split('-');
                    if (parts.length === 3) return parts[2] + '/' + parts[1] + '/' + parts[0];
                    return dateStr;
                },

                goToStep(s) {
                    if (s > this.step) {
                        if (!this.validateStep(this.step)) return;
                    }
                    this.step = s;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                nextStep() {
                    if (this.validateStep(this.step)) {
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

                validateStep(s) {
                    if (s === 1) {
                        if (!this.formData.mutasi_asal.trim()) {
                            alert('⚠️ Mohon isi Instansi / SKPD Asal Pelimpahan.');
                            return false;
                        }
                        if (!this.formData.mutasi_nomor_bamb.trim()) {
                            alert('⚠️ Mohon isi Nomor Berita Acara BAMB / SK Pelimpahan.');
                            return false;
                        }
                        if (!this.formData.mutasi_tanggal) {
                            alert('⚠️ Mohon isi Tanggal BAMB / SK Pelimpahan.');
                            return false;
                        }
                        return true;
                    }
                    if (s === 2) {
                        if (!this.formData.jenis_astap_id) {
                            alert('⚠️ Mohon pilih Klasifikasi Kode Barang 108.');
                            return false;
                        }
                        if (!this.formData.nama_barang.trim()) {
                            alert('⚠️ Mohon isi Nama Lengkap Barang.');
                            return false;
                        }
                        return true;
                    }
                    if (s === 3) {
                        if (!this.formData.unit_id) {
                            alert('⚠️ Mohon pilih Unit / Ruangan Penempatan Baru di RSUD.');
                            return false;
                        }
                        if (this.isTanah) {
                            this.syncTanahFields();
                        }
                        return true;
                    }
                    return true;
                },

                submitForm() {
                    if (!this.validateStep(1) || !this.validateStep(2) || !this.validateStep(3)) return;
                    
                    if (this.isTanah) {
                        this.syncTanahFields();
                    }

                    this.isSubmitting = true;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                    const url = this.isEdit ? `/astap/update-mutasi-eksternal/${this.astapId}` : "{{ route('astap.store_mutasi_eksternal') }}";

                    const postData = new FormData();
                    for (const key in this.formData) {
                        if (key === 'tanah_items') {
                            postData.append('tanah_items', JSON.stringify(this.formData.tanah_items));
                        } else if (this.formData[key] !== null && this.formData[key] !== undefined) {
                            postData.append(key, this.formData[key]);
                        }
                    }
                    if (this.selectedFile) {
                        postData.append('dokumen_file', this.selectedFile);
                    }
                    if (this.isEdit) {
                        postData.append('_method', 'PUT');
                    }

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: postData
                    })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(result => {
                        this.isSubmitting = false;
                        if (result.status === 200 && result.body.success) {
                            alert('🎉 Berhasil! ' + (result.body.message || (this.isEdit ? 'Data Mutasi Eksternal berhasil diperbarui.' : 'Data Mutasi Eksternal berhasil disimpan.')));
                            window.location.href = result.body.redirect || ({{ Js::from($isFromEksternal) }} ? "{{ route('mutasi.eksternal') }}" : "{{ route('astap.index') }}");
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
        window.formMutasiMasuk = formMutasiEksternal;
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(168, 85, 247, 0.4);
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(168, 85, 247, 0.7);
        }
    </style>
