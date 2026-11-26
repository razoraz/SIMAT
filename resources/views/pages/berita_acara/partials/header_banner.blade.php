<!-- ========================================================================= -->
<!-- HEADER BANNER: PUSAT DOKUMEN & PENGESAHAN BAST TERPADU (COMPACT HERO)     -->
<!-- ========================================================================= -->
<style>
    @media (max-width: 1023px) {
        .ba-header-flex {
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
        }
        .ba-header-info {
            width: 100% !important;
            text-align: center !important;
        }
        .ba-header-title {
            justify-content: center !important;
        }
        .ba-header-desc {
            margin-left: auto !important;
            margin-right: auto !important;
            text-align: center !important;
        }
        .ba-status-strip {
            align-self: center !important;
            justify-content: center !important;
            flex-wrap: wrap !important;
            width: 100% !important;
        }
        .ba-toolbar-wrap {
            flex-direction: column !important;
            align-items: stretch !important;
            text-align: center !important;
        }
        .ba-toolbar-actions {
            justify-content: center !important;
            flex-wrap: wrap !important;
            width: 100% !important;
        }
        .ba-subbar {
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
        }
        .ba-subbar-pills {
            flex-wrap: wrap !important;
            justify-content: center !important;
            overflow-x: visible !important;
            width: 100% !important;
        }
        .ba-subbar-info {
            width: 100% !important;
            justify-content: center !important;
            text-align: center !important;
        }
        .ba-card-header {
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
        }
        .swipe-hint-ba {
            display: flex !important;
        }
    }
    @media (min-width: 1024px) {
        .ba-header-flex {
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            text-align: left !important;
        }
        .ba-header-info {
            width: auto !important;
            text-align: left !important;
        }
        .ba-header-title {
            justify-content: flex-start !important;
        }
        .ba-header-desc {
            margin-left: 0 !important;
            margin-right: 0 !important;
            text-align: left !important;
        }
        .ba-status-strip {
            align-self: center !important;
            justify-content: flex-end !important;
            flex-wrap: nowrap !important;
            width: auto !important;
        }
        .ba-toolbar-wrap {
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            text-align: left !important;
        }
        .ba-toolbar-actions {
            justify-content: flex-end !important;
            width: auto !important;
        }
        .ba-subbar {
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            text-align: left !important;
        }
        .ba-subbar-pills {
            flex-wrap: nowrap !important;
            justify-content: flex-start !important;
            width: auto !important;
        }
        .ba-subbar-info {
            width: auto !important;
            justify-content: flex-end !important;
            text-align: right !important;
        }
        .ba-card-header {
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            text-align: left !important;
        }
        .swipe-hint-ba {
            display: none !important;
        }
    }
</style>

<div class="no-print bg-gradient-to-r from-slate-900 via-slate-900/95 to-slate-950 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl mb-5 relative overflow-hidden">
    {{-- Subtle Glow Accent --}}
    <div class="absolute -right-16 -top-16 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="ba-header-flex flex flex-col lg:flex-row lg:items-center justify-between gap-4 relative z-10">
        <div class="ba-header-info">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 text-[11px] font-bold mb-2.5">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                <span class="uppercase tracking-wider">Modul Terpadu 6 Kategori Dokumen BAST Resmi</span>
            </div>
            <h1 class="ba-header-title text-xl sm:text-2xl font-black text-white tracking-tight flex flex-wrap items-center gap-2">
                <span>Pusat Berita Acara Serah Terima (BAST)</span>
                <span class="text-[10px] font-mono font-bold text-slate-400 bg-slate-950/80 px-2 py-0.5 rounded-lg border border-slate-800">
                    RSUD dr. H. Koesnandi
                </span>
            </h1>
            <p class="ba-header-desc text-xs text-slate-400 mt-1 max-w-3xl leading-relaxed">
                Penerbitan dokumen, pratinjau lembar cetak standar A4, live edit pejabat penandatangan, dan validasi tanda tangan elektronik (TTE BSrE) untuk 6 kategori perolehan &amp; mutasi aset.
            </p>
        </div>

        {{-- Mini Status Strip Kanan --}}
        <div class="ba-status-strip flex items-center gap-2.5 self-start lg:self-center shrink-0">
            <div class="px-3.5 py-2 rounded-2xl bg-slate-950/80 border border-slate-800 flex items-center space-x-2 text-xs text-left">
                <span class="text-emerald-400 text-sm">🔒</span>
                <div>
                    <span class="text-[9.5px] uppercase tracking-wider font-semibold text-slate-400 block">Sertifikasi TTE</span>
                    <span class="font-extrabold text-emerald-300 text-xs">BSrE Terintegrasi</span>
                </div>
            </div>
            <div class="px-3.5 py-2 rounded-2xl bg-slate-950/80 border border-slate-800 flex items-center space-x-2 text-xs text-left">
                <span class="text-cyan-400 text-sm">📋</span>
                <div>
                    <span class="text-[9.5px] uppercase tracking-wider font-semibold text-slate-400 block">Total Kategori</span>
                    <span class="font-extrabold text-cyan-300 text-xs">6 Modul BAST</span>
                </div>
            </div>
        </div>
    </div>
</div>
