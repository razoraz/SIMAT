<!-- Script Inisialisasi Chart.js untuk Dashboard Sub Admin -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
function initSubAdminCharts() {
    if (typeof Chart === 'undefined') {
        setTimeout(initSubAdminCharts, 100);
        return;
    }

    // 1. Data Grafik Nilai Aset Ruangan
    const chartYears = {{ Js::from($chartYears ?? []) }};
    const hargaKumulatif = {{ Js::from($chartRoomKumulatifHargaJuta ?? []) }};
    const volKumulatif = {{ Js::from($chartRoomKumulatifVolume ?? []) }};
    const hargaPerTahun = {{ Js::from($chartRoomHargaJuta ?? []) }};
    const volPerTahun = {{ Js::from($chartRoomVolume ?? []) }};
    const rawHargaKumulatif = {{ Js::from(array_map(fn($v) => (float)$v, $chartRoomHarga ?? [])) }};

    const growthCtx = document.getElementById('roomAstapGrowthChart');
    if (growthCtx) {
        window.roomAstapChart = new Chart(growthCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: chartYears,
                datasets: [
                    {
                        label: 'Akumulasi Valuasi (Rp Juta)',
                        data: hargaKumulatif,
                        borderColor: '#10b981', // Emerald
                        backgroundColor: 'rgba(16, 185, 129, 0.15)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#10b981',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        yAxisID: 'y'
                    },
                    {
                        type: 'bar',
                        label: 'Akumulasi Kuantitas (Unit)',
                        data: volKumulatif,
                        backgroundColor: 'rgba(20, 184, 166, 0.35)', // Teal
                        borderColor: '#14b8a6',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        maxBarThickness: 32,
                        yAxisID: 'y1'
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
                        labels: {
                            color: '#94a3b8',
                            font: { size: 11, weight: '600' },
                            usePointStyle: true,
                            padding: 12
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
                                const val = context.raw || 0;
                                if (context.dataset.yAxisID === 'y') {
                                    return ` Valuasi: Rp ${val.toLocaleString('id-ID')} Juta`;
                                }
                                return ` Kuantitas: ${val} Unit Barang`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(51, 65, 85, 0.3)' },
                        ticks: { color: '#94a3b8', font: { size: 10 } }
                    },
                    y: {
                        type: 'linear',
                        position: 'left',
                        grid: { color: 'rgba(51, 65, 85, 0.3)' },
                        ticks: {
                            color: '#10b981',
                            font: { size: 10 },
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID') + ' Jt';
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: {
                            color: '#14b8a6',
                            font: { size: 10 },
                            callback: function(value) {
                                return value + ' Unit';
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Inisialisasi Grafik Kondisi Aset Ruangan (Doughnut Chart)
    const kondisiCtx = document.getElementById('roomKondisiChart');
    if (kondisiCtx) {
        const kondisiBaik = {{ (int)($kondisiBaik ?? 0) }};
        const kondisiKurangBaik = {{ (int)($kondisiKurangBaik ?? 0) }};
        const kondisiRusakRingan = {{ (int)($kondisiRusakRingan ?? 0) }};
        const kondisiRusakBerat = {{ (int)($kondisiRusakBerat ?? 0) }};

        new Chart(kondisiCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Baik', 'Kurang Baik', 'Rusak Ringan', 'Rusak Berat'],
                datasets: [{
                    data: [kondisiBaik, kondisiKurangBaik, kondisiRusakRingan, kondisiRusakBerat],
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
                            padding: 8
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

    window.switchSubAdminChartMode = function (mode) {
        const btnKumulatif = document.getElementById('btnKumulatifSub');
        const btnPerTahun = document.getElementById('btnPerTahunSub');
        if (!btnKumulatif || !btnPerTahun || !window.roomAstapChart) return;

        if (mode === 'kumulatif') {
            btnKumulatif.className = "px-3 py-1.5 rounded-xl bg-emerald-500 text-slate-950 font-bold transition-all shadow-md";
            btnPerTahun.className = "px-3 py-1.5 rounded-xl text-slate-400 hover:text-white transition-all";

            window.roomAstapChart.data.datasets[0].label = 'Akumulasi Valuasi (Rp Juta)';
            window.roomAstapChart.data.datasets[0].data = hargaKumulatif;
            window.roomAstapChart.data.datasets[1].label = 'Akumulasi Kuantitas (Unit)';
            window.roomAstapChart.data.datasets[1].data = volKumulatif;
        } else {
            btnPerTahun.className = "px-3 py-1.5 rounded-xl bg-teal-500 text-slate-950 font-bold transition-all shadow-md";
            btnKumulatif.className = "px-3 py-1.5 rounded-xl text-slate-400 hover:text-white transition-all";

            window.roomAstapChart.data.datasets[0].label = 'Pengadaan Valuasi (Rp Juta/Thn)';
            window.roomAstapChart.data.datasets[0].data = hargaPerTahun;
            window.roomAstapChart.data.datasets[1].label = 'Pengadaan Kuantitas (Unit/Thn)';
            window.roomAstapChart.data.datasets[1].data = volPerTahun;
        }
        window.roomAstapChart.update();
    };
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSubAdminCharts);
} else {
    initSubAdminCharts();
}
</script>
