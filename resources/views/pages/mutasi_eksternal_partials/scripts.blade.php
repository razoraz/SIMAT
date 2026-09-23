<script>
    function mutasiEksternalCatalog() {
        return {
            userRole: {{ Js::from(Auth::user()?->role ?? 'admin') }},
            userName: {{ Js::from(Auth::user()?->name ?? 'Admin') }},

            searchQuery: '',
            statusFilter: 'all',
            jenisFilter: 'all',
            showDetailModal: false,
            selectedMutasi: null,

            mutasiEksternals: {{ Js::from($mutasiEksternals) }},

            get filteredMutasis() {
                const query = (this.searchQuery || '').toLowerCase().trim();

                return this.mutasiEksternals.filter(item => {
                    // Filter Jenis Transaksi
                    if (this.jenisFilter !== 'all') {
                        if (item.jenis !== this.jenisFilter) return false;
                    }

                    // Filter Status BAST
                    if (this.statusFilter === 'selesai') {
                        if (!item.status.toLowerCase().includes('selesai') && !item.status.toLowerCase().includes('disahkan')) return false;
                    } else if (this.statusFilter === 'pinjam_aktif') {
                        if (!item.status.toLowerCase().includes('peminjaman')) return false;
                    } else if (this.statusFilter === 'menunggu_verifikasi') {
                        if (!item.status.toLowerCase().includes('menunggu')) return false;
                    }

                    // Search Query (Nama Barang, Kode 108, NIBAR, Tahun, No BAST, OPD)
                    if (query) {
                        const match =
                            (item.nama_barang || '').toLowerCase().includes(query) ||
                            (item.nama || '').toLowerCase().includes(query) ||
                            (item.kode_barang || '').toLowerCase().includes(query) ||
                            (item.kode_108 || '').toLowerCase().includes(query) ||
                            (item.tahun_perolehan || '').toLowerCase().includes(query) ||
                            (item.kode || '').toLowerCase().includes(query) ||
                            (item.opd_asal || '').toLowerCase().includes(query) ||
                            (item.opd_tujuan || '').toLowerCase().includes(query) ||
                            (item.pejabat_opd_tujuan || '').toLowerCase().includes(query) ||
                            (item.nomor_sk_dasar || '').toLowerCase().includes(query) ||
                            (item.category || '').toLowerCase().includes(query) ||
                            (item.jenis || '').toLowerCase().includes(query) ||
                            (item.ruangan_asal || '').toLowerCase().includes(query);
                        if (!match) return false;
                    }

                    return true;
                });
            },

            get countAll() {
                return this.mutasiEksternals.length;
            },

            get countSelesai() {
                return this.mutasiEksternals.filter(m => m.status.toLowerCase().includes('selesai') || m.status.toLowerCase().includes('disahkan')).length;
            },

            get countPinjamAktif() {
                return this.mutasiEksternals.filter(m => m.status.toLowerCase().includes('peminjaman')).length;
            },

            get countMenunggu() {
                return this.mutasiEksternals.filter(m => m.status.toLowerCase().includes('menunggu')).length;
            },

            get countTransfer() {
                return this.mutasiEksternals.filter(m => m.jenis === 'Transfer Antar-OPD').length;
            },

            get countBpkad() {
                return this.mutasiEksternals.filter(m => m.jenis === 'Penyerahan ke BPKAD').length;
            },

            openDetail(item) {
                this.selectedMutasi = item;
                this.showDetailModal = true;
            },

            // Hitung statistik kondisi dari registers suatu aset (Baik, Kurang Baik, Rusak Ringan, Rusak Berat)
            getKondisiStats(item) {
                if (!item) return { total: 0, baik: 0, kurang_baik: 0, rusak_ringan: 0, rusak_berat: 0, pct_baik: 100, pct_kb: 0, pct_rr: 0, pct_rb: 0, kondisi_dominan: 'Baik', is_multi: false, text: 'Baik (100%)', badge_class: 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30', dot_class: 'bg-emerald-400' };
                const regs = item.registers || [];
                const total = regs.length;
                if (total === 0) {
                    const k = item.kondisi || item.kondisi_barang || 'Baik';
                    const isKb = k === 'Kurang Baik' || k === 'KB';
                    const isRr = k === 'Rusak Ringan' || k === 'RR';
                    const isRb = k === 'Rusak Berat' || k === 'RB' || k === 'Rusak';
                    const dominan = isKb ? 'Kurang Baik' : (isRr ? 'Rusak Ringan' : (isRb ? 'Rusak Berat' : 'Baik'));
                    const badgeClass = dominan === 'Baik' ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' : (dominan === 'Kurang Baik' ? 'bg-amber-500/15 text-amber-300 border-amber-500/30' : (dominan === 'Rusak Ringan' ? 'bg-orange-500/15 text-orange-300 border-orange-500/30' : 'bg-rose-500/15 text-rose-300 border-rose-500/30'));
                    const dotClass = dominan === 'Baik' ? 'bg-emerald-400' : (dominan === 'Kurang Baik' ? 'bg-amber-400' : (dominan === 'Rusak Ringan' ? 'bg-orange-400' : 'bg-rose-400'));
                    return {
                        total: 1,
                        baik: dominan === 'Baik' ? 1 : 0,
                        kurang_baik: isKb ? 1 : 0,
                        rusak_ringan: isRr ? 1 : 0,
                        rusak_berat: isRb ? 1 : 0,
                        pct_baik: dominan === 'Baik' ? 100 : 0,
                        pct_kb: isKb ? 100 : 0,
                        pct_rr: isRr ? 100 : 0,
                        pct_rb: isRb ? 100 : 0,
                        kondisi_dominan: dominan,
                        is_multi: false,
                        text: dominan + ' (100%)',
                        badge_class: badgeClass,
                        dot_class: dotClass
                    };
                }
                const baik = regs.filter(r => (r.kondisi || 'Baik') === 'Baik' || r.kondisi === 'B').length;
                const kb   = regs.filter(r => r.kondisi === 'Kurang Baik' || r.kondisi === 'KB').length;
                const rr   = regs.filter(r => r.kondisi === 'Rusak Ringan' || r.kondisi === 'RR').length;
                const rb   = regs.filter(r => r.kondisi === 'Rusak Berat' || r.kondisi === 'RB' || r.kondisi === 'Rusak').length;
                const dominan = (baik >= kb && baik >= rr && baik >= rb) ? 'Baik' : ((kb >= rr && kb >= rb) ? 'Kurang Baik' : ((rr >= rb) ? 'Rusak Ringan' : 'Rusak Berat'));
                const isSingle = (baik === total) || (kb === total) || (rr === total) || (rb === total);

                const pct_baik = Math.round((baik / total) * 100);
                const pct_kb   = Math.round((kb   / total) * 100);
                const pct_rr   = Math.round((rr   / total) * 100);
                const pct_rb   = Math.round((rb   / total) * 100);

                let parts = [];
                if (baik > 0) parts.push(`${pct_baik}% Baik (${baik}/${total})`);
                if (kb > 0)   parts.push(`${pct_kb}% Kurang Baik (${kb}/${total})`);
                if (rr > 0)   parts.push(`${pct_rr}% Rusak Ringan (${rr}/${total})`);
                if (rb > 0)   parts.push(`${pct_rb}% Rusak Berat (${rb}/${total})`);

                let text = parts.join(' • ');
                if (isSingle) {
                    if (baik === total) text = total > 1 ? `Baik (${total} Aset)` : 'Baik';
                    else if (kb === total) text = total > 1 ? `Kurang Baik (${total} Aset)` : 'Kurang Baik';
                    else if (rr === total) text = total > 1 ? `Rusak Ringan (${total} Aset)` : 'Rusak Ringan';
                    else if (rb === total) text = total > 1 ? `Rusak Berat (${total} Aset)` : 'Rusak Berat';
                }

                let badgeClass = 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30';
                if (rb > 0 && rb >= baik && rb >= kb) {
                    badgeClass = 'bg-rose-500/15 text-rose-300 border-rose-500/30';
                } else if (kb > 0 && kb >= baik) {
                    badgeClass = 'bg-amber-500/15 text-amber-300 border-amber-500/30';
                } else if (rr > 0 && rr >= baik) {
                    badgeClass = 'bg-orange-500/15 text-orange-300 border-orange-500/30';
                } else if (!isSingle) {
                    badgeClass = 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30';
                }

                let dotClass = pct_baik === 100 ? 'bg-emerald-400' : (pct_rb > 0 ? 'bg-rose-400' : (pct_rr > 0 ? 'bg-orange-400' : 'bg-amber-400'));

                return {
                    total,
                    baik,
                    kurang_baik: kb,
                    rusak_ringan: rr,
                    rusak_berat: rb,
                    pct_baik,
                    pct_kb,
                    pct_rr,
                    pct_rb,
                    kondisi_dominan: dominan,
                    is_multi: !isSingle,
                    text,
                    badge_class: badgeClass,
                    dot_class: dotClass
                };
            },

            resetFilters() {
                this.searchQuery = '';
                this.statusFilter = 'all';
                this.jenisFilter = 'all';
            },

            openReklas(item) {
                if (!item) return;
                const searchKey = item.kode_barang || item.nama_murni || item.nama || item.kode || '';
                window.location.href = `/astap?search=${encodeURIComponent(searchKey)}&open_reklas=${item.id}`;
            },

            cetakBast(item) {
                this.selectedMutasi = item;
                this.showDetailModal = true;
                this.$nextTick(() => {
                    setTimeout(() => {
                        window.print();
                    }, 250);
                });
            },

            deleteMutasi(item) {
                if (!item) return;
                const namaAset = item.nama_murni || item.nama || 'Aset';
                if (!confirm(`⚠️ Apakah Anda yakin ingin memindahkan data mutasi pelimpahan "${namaAset}" ke Recycle Bin (Tong Sampah)?\n\nSeluruh unit register NIBAR terkait juga akan dipindahkan ke Recycle Bin.`)) {
                    return;
                }

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                fetch(`/astap/${item.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(d => {
                    if (d.success !== false) {
                        this.mutasiEksternals = this.mutasiEksternals.filter(m => Number(m.id) !== Number(item.id));
                        if (this.selectedMutasi && Number(this.selectedMutasi.id) === Number(item.id)) {
                            this.showDetailModal = false;
                            this.selectedMutasi = null;
                        }
                        if (typeof window.showSimatToast === 'function') {
                            window.showSimatToast(d.message || 'Data mutasi eksternal berhasil dipindahkan ke Tong Sampah.', 'success');
                        } else {
                            alert('✓ Data mutasi eksternal berhasil dipindahkan ke Tong Sampah.');
                        }
                    } else {
                        if (typeof window.showSimatToast === 'function') {
                            window.showSimatToast('Gagal menghapus: ' + (d.message || 'Terjadi kesalahan.'), 'error');
                        } else {
                            alert('❌ Gagal menghapus: ' + (d.message || 'Terjadi kesalahan.'));
                        }
                    }
                })
                .catch(err => {
                    console.error('Delete error:', err);
                    alert('❌ Terjadi kesalahan jaringan saat mencoba menghapus data.');
                });
            }
        };
    }
</script>
