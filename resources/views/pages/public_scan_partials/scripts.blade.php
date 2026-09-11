<!-- SCRIPT COPY & SHARE -->
<script>
    function copyNibar() {
        const nibar = document.getElementById('nibarText')?.innerText?.trim();
        if (!nibar) return;
        navigator.clipboard.writeText(nibar).then(() => {
            const btnLabel = document.getElementById('copyBtnLabel');
            const copyBtn = document.getElementById('copyBtn');
            if (btnLabel && copyBtn) {
                btnLabel.innerText = 'Tersalin!';
                copyBtn.classList.add('bg-emerald-600', 'text-slate-950');
                copyBtn.classList.remove('bg-slate-800', 'text-slate-200');
                setTimeout(() => {
                    btnLabel.innerText = 'Salin NIBAR';
                    copyBtn.classList.remove('bg-emerald-600', 'text-slate-950');
                    copyBtn.classList.add('bg-slate-800', 'text-slate-200');
                }, 2000);
            }
        }).catch(err => {
            console.error('Gagal menyalin:', err);
        });
    }

    function shareAssetInfo() {
        const nama = @json($astap->nama_barang ?? 'Aset RSUD');
        const nibar = @json($register->nibar ?? ($nibar ?? ''));
        const ruang = @json($ruang ?? 'RSUD Dr. H. Koesnandi');
        const kondisi = @json($kondisi ?? 'Baik');

        const text = `*VERIFIKASI ASET SIMAT-RK RSUD DR. H. KOESNANDI*\n\n` +
                     `• *Barang:* ${nama}\n` +
                     `• *NIBAR:* ${nibar}\n` +
                     `• *Lokasi/Ruang:* ${ruang}\n` +
                     `• *Kondisi:* ${kondisi}\n` +
                     `• *Link Verifikasi:* ${window.location.href}`;

        if (navigator.share) {
            navigator.share({
                title: 'Identitas Aset ' + nama,
                text: text,
                url: window.location.href
            }).catch(console.warn);
        } else {
            navigator.clipboard.writeText(text).then(() => {
                alert('📋 Informasi aset telah disalin ke clipboard! Anda dapat menempelkannya ke WhatsApp atau catatan.');
            });
        }
    }
</script>
