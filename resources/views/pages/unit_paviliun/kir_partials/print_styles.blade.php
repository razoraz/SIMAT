    <!-- Print Media Query Styling untuk Mencetak Dokumen KIR Kertas Putih Sempurna -->
    <style>
    @media print {
        body {
            background: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .no-print, aside, header, nav, footer {
            display: none !important;
        }
        #print-area-kir {
            display: block !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
            border: none !important;
        }
    }
    </style>
