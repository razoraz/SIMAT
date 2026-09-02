import re

with open('resources/views/pages/form_astap.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Let's inspect how to move Riwayat Dokumen + SP2D BAST to top of each template cleanly.

# 1. KIB A TANAH
# We extract Riwayat Dokumen block and SP2D/BAST block from KIB A and place them at top of KIB A.
doc_a_pattern = r'(<!-- 4\. Riwayat Dokumen Pembelian.*?</div>\s*</div>)'
sp2d_a_pattern = r'(<!-- SP2D & BAST -->\s*<div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3 shadow-lg">.*?</div>\s*</div>)'

# Let's do string replacement for KIB A, B, C, D, E, ATB, KDP using exact marker matching or python parsing.
print("Script template ready.")
