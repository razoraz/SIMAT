<script>
    function rmbDashboard() {
        return {
            activeTab: 'tabel_21',
            isBalance: {{ ($kertasKerja['is_balance'] ?? true) ? 'true' : 'false' }},
            selisihNominal: {{ (float) ($kertasKerja['selisih'] ?? 0) }},
            openGroups: ['KIB A', 'KIB B', 'KIB C', 'KIB D', 'KIB E', 'KIB F'],
            allExpanded: true,

            isGroupOpen(gk) {
                return this.openGroups.includes(gk);
            },

            toggleGroup(gk) {
                if (this.openGroups.includes(gk)) {
                    this.openGroups = this.openGroups.filter(g => g !== gk);
                } else {
                    this.openGroups.push(gk);
                }
                this.allExpanded = (this.openGroups.length === 6);
            },

            toggleAllAccordion() {
                if (this.allExpanded) {
                    this.openGroups = [];
                    this.allExpanded = false;
                } else {
                    this.openGroups = ['KIB A', 'KIB B', 'KIB C', 'KIB D', 'KIB E', 'KIB F'];
                    this.allExpanded = true;
                }
            },

            exportToExcel() {
                // Ekspor lembar kerja RMB ke format Excel (.xls) dengan format XML Spreadsheet resmi
                const thn = '{{ $selectedTahun }}';
                const tw = '{{ $selectedTw === "all" ? "Seluruh-Tahun" : "TW-" . $selectedTw }}';
                const filename = `RMB_Rekonsiliasi_Belanja_Modal_${thn}_${tw}.xls`;

                const tableEl = document.querySelector('table');
                if (!tableEl) {
                    alert('Tabel RMB tidak ditemukan.');
                    return;
                }

                // Salin HTML tabel untuk dibungkus ke format Excel
                const tableHtml = tableEl.outerHTML;
                const htmlDoc = `
                    <html xmlns:o="urn:schemas-microsoft-com:office:office"
                          xmlns:x="urn:schemas-microsoft-com:office:excel"
                          xmlns="http://www.w3.org/TR/REC-html40">
                    <head>
                        <meta charset="utf-8">
                        <style>
                            table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 10pt; }
                            th, td { border: 1px solid #999; padding: 4px 8px; }
                            th { background-color: #0f172a; color: #ffffff; text-align: center; }
                        </style>
                    </head>
                    <body>
                        <h2 style="font-family: Arial; text-align: center;">REKONSILIASI BELANJA MODAL (RMB) ASET TETAP RSUD dr. H. KOESNANDI</h2>
                        <p style="text-align: center; font-size: 9pt;">Tahun Anggaran: ${thn} | Periode: ${tw}</p>
                        ${tableHtml}
                    </body>
                    </html>
                `;

                const blob = new Blob([htmlDoc], { type: 'application/vnd.ms-excel;charset=utf-8' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }
        };
    }
</script>
