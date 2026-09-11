<!-- Script Inisialisasi Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
function initAstapMasterChart() {
    const labels = {{ Js::from($chartLabels ?? []) }};
    const hargaPerTahun = {{ Js::from($chartHargaDataJuta ?? []) }};
    const volPerTahun = {{ Js::from($chartVolumeData ?? []) }};
    const hargaKumulatif = {{ Js::from($chartKumulatifHargaJuta ?? []) }};
    const volKumulatif = {{ Js::from($chartKumulatifVolume ?? []) }};

    const ctx = document.getElementById('astapGrowthChart');
    if (!ctx) return;

    if (typeof Chart === 'undefined') {
        console.warn('Chart.js belum siap, mencoba memuat kembali...');
        setTimeout(initAstapMasterChart, 150);
        return;
    }

    const chartCtx = ctx.getContext('2d');

    // Linear Gradient Fills
    const gradientHarga = chartCtx.createLinearGradient(0, 0, 0, 300);
    gradientHarga.addColorStop(0, 'rgba(16, 185, 129, 0.45)');
    gradientHarga.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

    const gradientVol = chartCtx.createLinearGradient(0, 0, 0, 300);
    gradientVol.addColorStop(0, 'rgba(6, 182, 212, 0.35)');
    gradientVol.addColorStop(1, 'rgba(6, 182, 212, 0.0)');

    window.astapChart = new Chart(chartCtx, {
        type: 'line',
        data: {
            labels: labels.length > 0 ? labels : ['Thn 2022', 'Thn 2023', 'Thn 2024', 'Thn 2025', 'Thn 2026'],
            datasets: [
                {
                    label: 'Valuasi Harga Aset (Rp Juta)',
                    data: hargaKumulatif.length > 0 ? hargaKumulatif : [350, 750, 1200, 2400, 5200],
                    borderColor: '#10b981',
                    backgroundColor: gradientHarga,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#020617',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    yAxisID: 'yHarga'
                },
                {
                    label: 'Kuantitas Volume (Unit)',
                    data: volKumulatif.length > 0 ? volKumulatif : [15, 38, 75, 120, 184],
                    borderColor: '#06b6d4',
                    backgroundColor: gradientVol,
                    borderWidth: 2.5,
                    borderDash: [4, 4],
                    fill: false,
                    tension: 0.35,
                    pointBackgroundColor: '#06b6d4',
                    pointBorderColor: '#020617',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    yAxisID: 'yVol'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#94a3b8',
                        font: { size: 11, weight: 'bold' },
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#f8fafc',
                    bodyColor: '#cbd5e1',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: true,
                    callbacks: {
                        label: function (context) {
                            let val = context.raw || 0;
                            if (context.datasetIndex === 0) {
                                if (val >= 1000) {
                                    return ` 💰 Valuasi Aset: Rp ${(val / 1000).toFixed(2).replace('.', ',')} Miliar (${val.toLocaleString('id-ID')} Juta)`;
                                }
                                return ` 💰 Valuasi Aset: Rp ${val.toLocaleString('id-ID')} Juta`;
                            } else {
                                return ` 📏 Total Volume: ${val} Unit Barang`;
                            }
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(51, 65, 85, 0.3)' },
                    ticks: { color: '#94a3b8', font: { size: 11, weight: '600' } }
                },
                yHarga: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { color: 'rgba(51, 65, 85, 0.3)' },
                    ticks: {
                        color: '#10b981',
                        font: { size: 10, weight: 'bold' },
                        callback: function (val) {
                            if (val >= 1000) {
                                return 'Rp ' + (val / 1000).toFixed(1) + ' M';
                            }
                            return 'Rp ' + val + ' Jt';
                        }
                    },
                    title: {
                        display: true,
                        text: 'Valuasi (Rupiah)',
                        color: '#10b981',
                        font: { size: 10, weight: 'bold' }
                    }
                },
                yVol: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: {
                        color: '#06b6d4',
                        font: { size: 10, weight: 'bold' },
                        callback: function (val) { return val + ' Unit'; }
                    },
                    title: {
                        display: true,
                        text: 'Kuantitas (Unit)',
                        color: '#06b6d4',
                        font: { size: 10, weight: 'bold' }
                    }
                }
            }
        }
    });

    // 2. Inisialisasi Grafik Kondisi Keseluruhan Barang (Doughnut Chart)
    const kondisiCtx = document.getElementById('kondisiChart');
    if (kondisiCtx) {
        const kondisiLabels = {{ Js::from($chartKondisiLabels ?? ['Baik', 'Kurang Baik', 'Rusak Ringan', 'Rusak Berat']) }};
        const kondisiData = {{ Js::from($chartKondisiData ?? [0, 0, 0, 0]) }};

        new Chart(kondisiCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: kondisiLabels,
                datasets: [{
                    data: kondisiData,
                    backgroundColor: [
                        '#10b981', // Emerald - Baik
                        '#f59e0b', // Amber - Kurang Baik
                        '#f97316', // Orange - Rusak Ringan
                        '#ef4444', // Rose - Rusak Berat
                    ],
                    borderColor: '#020617',
                    borderWidth: 3,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#94a3b8',
                            font: { size: 10, weight: 'bold' },
                            usePointStyle: true,
                            padding: 10
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: '#334155',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const val = context.raw || 0;
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${val} Unit (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. Inisialisasi Grafik Perbandingan Distribusi Barang (Doughnut Chart)
    const distCtx = document.getElementById('distribusiChart');
    if (distCtx) {
        const distLabels = {{ Js::from($chartDistribusiStatusLabels ?? ['Sudah Didistribusikan', 'Belum Didistribusikan (Gudang)']) }};
        const distData = {{ Js::from($chartDistribusiStatusData ?? [$totalTerdistribusiUnit ?? 0, $belumTerdistribusi ?? 0]) }};

        new Chart(distCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: distLabels,
                datasets: [{
                    data: distData,
                    backgroundColor: [
                        '#14b8a6', // Teal - Sudah Didistribusikan
                        '#a855f7', // Purple - Belum Didistribusikan (Gudang)
                    ],
                    borderColor: '#020617',
                    borderWidth: 3,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#94a3b8',
                            font: { size: 10, weight: 'bold' },
                            usePointStyle: true,
                            padding: 10
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: '#334155',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const val = context.raw || 0;
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${val} Unit (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    window.switchChartMode = function (mode) {
        const btnKumulatif = document.getElementById('btnKumulatif');
        const btnPerTahun = document.getElementById('btnPerTahun');
        if (!btnKumulatif || !btnPerTahun || !window.astapChart) return;

        if (mode === 'kumulatif') {
            btnKumulatif.className = "px-3.5 py-1.5 rounded-xl bg-emerald-500 text-slate-950 font-bold transition-all shadow-md";
            btnPerTahun.className = "px-3.5 py-1.5 rounded-xl text-slate-400 hover:text-white transition-all";

            window.astapChart.data.datasets[0].label = 'Akumulasi Valuasi (Rp Juta)';
            window.astapChart.data.datasets[0].data = hargaKumulatif;
            window.astapChart.data.datasets[1].label = 'Akumulasi Kuantitas (Unit)';
            window.astapChart.data.datasets[1].data = volKumulatif;
        } else {
            btnPerTahun.className = "px-3.5 py-1.5 rounded-xl bg-cyan-500 text-slate-950 font-bold transition-all shadow-md";
            btnKumulatif.className = "px-3.5 py-1.5 rounded-xl text-slate-400 hover:text-white transition-all";

            window.astapChart.data.datasets[0].label = 'Pengadaan Valuasi (Rp Juta/Thn)';
            window.astapChart.data.datasets[0].data = hargaPerTahun;
            window.astapChart.data.datasets[1].label = 'Pengadaan Kuantitas (Unit/Thn)';
            window.astapChart.data.datasets[1].data = volPerTahun;
        }
        window.astapChart.update();
    };
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAstapMasterChart);
} else {
    initAstapMasterChart();
}
</script>
