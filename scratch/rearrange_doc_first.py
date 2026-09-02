import re

with open('resources/views/pages/form_astap.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Let's inspect the sections in each template and rearrange them so that:
# 1. Riwayat Dokumen Pembelian (SPK, Surat Pesanan, Kwitansi, Invoice) and Dokumen SP2D & BAST are at the VERY TOP as Section 1.
# 2. Followed by Identitas 108 and other fields.

print("Loaded form_astap.blade.php, preparing rearrangement...")
