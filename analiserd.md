# Dokumen Analisis Database & ERD SIMAT-RK
**Sistem Informasi Manajemen Aset Tetap RSUD dr. H. Koesnandi Bondowoso**

> 🖼️ **Versi Gambar Vektor (SVG Beresolusi Tinggi):**
> Anda dapat melihat dan membuka langsung file gambar ERD di:
> - **[analiserd.svg](file:///d:/MAGANG%20RSUKOESNADI/SIMAT/analiserd.svg)** *(Root Project)*
> - **[public/analiserd.svg](file:///d:/MAGANG%20RSUKOESNADI/SIMAT/public/analiserd.svg)** *(Web Assets)*

---

## 1. Diagram ERD (Entity Relationship Diagram)

```mermaid
erDiagram
    %% ==========================================
    %% 1. ENTITAS MASTER PENGGUNA & ORGANISASI
    %% ==========================================
    UNITS ||--o{ USERS : "memiliki pegawai"
    UNITS ||--o{ ASTAP_REGISTERS : "memegang fisik aset"
    UNITS ||--o{ DISTRIBUSIS : "tujuan penerima distribusi"

    UNITS {
        bigint id PK
        string kode_unit UK
        string nama
        string tipe
        string kepala
        string nip
        string email
        json id_aset
        int total_aset
        string total_nilai
        datetime created_at
        datetime updated_at
    }

    USERS ||--o{ ASTAPS : "menginput/mengelola belanja aset"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        string role "master_admin, admin, sub_admin"
        text penugasan
        string status "Aktif, Nonaktif"
        text deskripsi
        bigint unit_id FK "nullable"
        datetime email_verified_at
        string remember_token
        datetime created_at
        datetime updated_at
    }

    %% ==========================================
    %% 2. ENTITAS MASTER KLASIFIKASI & SIPD
    %% ==========================================
    JENIS_ASTAPS ||--o{ ASTAPS : "klasifikasi kode 108"
    JENIS_ASTAPS {
        bigint id PK
        string jenis "1.3.1, 1.3.2, 1.3.3..."
        string nama_jenis "Tanah, Peralatan Mesin, dll"
        string sub_rincian_objek
        string uraian_sub_rincian
        string sub_sub_rincian_objek "Kode 108 Lengkap"
        string uraian_sub_sub_rincian
        datetime created_at
        datetime updated_at
    }

    REKENING_BELANJAS ||--o{ ASTAPS : "sumber pos rekening belanja"
    REKENING_BELANJAS {
        bigint id PK
        string kelompok "5.2.02..."
        string nama_kelompok
        string kode_rek "Kode Rekening SIPD"
        string nama_belanja
        datetime created_at
        datetime updated_at
    }

    JENIS_PENGADAANS ||--o{ ASTAPS : "program kegiatan SIPD"
    JENIS_PENGADAANS {
        bigint id PK
        string program_kode
        string program_nama
        string kegiatan_kode
        string kegiatan_nama
        string sub_kegiatan_kode "Kode Sub-Kegiatan SIPD"
        string sub_kegiatan_nama
        datetime created_at
        datetime updated_at
    }

    %% ==========================================
    %% 3. ENTITAS INTI KATALOG & REGISTER ASET
    %% ==========================================
    ASTAPS ||--|{ ASTAP_REGISTERS : "dipecah menjadi N-unit fisik"
    ASTAPS ||--o{ DISTRIBUSI_ITEMS : "didistribusikan ke unit"

    ASTAPS {
        bigint id PK
        bigint jenis_pengadaan_id FK "nullable"
        bigint rekening_belanja_id FK "nullable"
        bigint jenis_astap_id FK "nullable"
        bigint user_id FK "nullable"
        string nama_barang
        year tahun_perolehan
        int jumlah_volume
        string satuan
        decimal harga_satuan
        decimal total_realisasi
        decimal biaya_administrasi_proyek
        boolean is_extracomtable "true jika < Rp 300.000"
        string spk_nomor
        date spk_tanggal
        string surat_pesanan_nomor
        date surat_pesanan_tanggal
        string kwitansi_nomor
        date kwitansi_tanggal
        string faktur_nomor
        date faktur_tanggal
        string sp2d_nomor
        date sp2d_tanggal
        string bast_dokumen_nomor
        date bast_dokumen_tanggal
        text alamat_barang
        string penyedia_nama
        string penyedia_pemilik
        string penyedia_rekening_nama
        string penyedia_rekening_nomor
        text penyedia_alamat
        string ppk_nama
        string ppk_nip
        text keterangan_tambahan
        json spesifikasi_json "KIB A-F/ATB Details"
        datetime created_at
        datetime updated_at
    }

    ASTAP_REGISTERS ||--o{ ASTAP_MUTASIS : "memiliki riwayat mutasi"

    ASTAP_REGISTERS {
        bigint id PK
        bigint astap_id FK
        bigint unit_id FK "nullable"
        year tahun_perolehan
        unsigned_int no_register_int "1..N"
        string no_register
        string nibar UK "45 Karakter NIBAR BMD"
        string ruang_pemegang "nullable"
        string kondisi "Baik, Rusak Ringan, Rusak Berat"
        string status "Tersedia, Tidak Tersedia"
        string qr_code_path
        datetime created_at
        datetime updated_at
    }

    ASTAP_MUTASIS {
        bigint id PK
        bigint astap_register_id FK
        string nomor_bamb "Nomor Berita Acara Mutasi"
        date tanggal_mutasi
        string ruangan_asal
        string ruangan_tujuan
        string penanggung_jawab_asal
        string penanggung_jawab_tujuan
        text alasan_mutasi
        datetime created_at
        datetime updated_at
    }

    %% ==========================================
    %% 4. ENTITAS DISTRIBUSI & SERAH TERIMA
    %% ==========================================
    DISTRIBUSIS ||--|{ DISTRIBUSI_ITEMS : "memuat rincian barang"

    DISTRIBUSIS {
        bigint id PK
        string kode UK "DST-2026-XXX"
        string bast_nomor "Nomor BAST Serah Terima"
        date tanggal_distribusi
        bigint unit_id FK "Unit / Ruangan Penerima"
        enum status "Draft, Menunggu Konfirmasi, Dalam Pengiriman, Telah Diterima"
        boolean signed
        string tgl_signed
        text keterangan
        datetime created_at
        datetime updated_at
    }

    DISTRIBUSI_ITEMS {
        bigint id PK
        bigint distribusi_id FK
        bigint astap_id FK
        unsigned_int qty
        json nibar_list "Daftar 45-Digit NIBAR Diserahkan"
        enum kondisi "Baik, Kurang Baik, Rusak Berat"
        text keterangan
        datetime created_at
        datetime updated_at
    }
```

---

## 2. Struktur Modul & Kamus Data

### A. Modul Pengguna & Unit Kerja
1. **`units`**
   - Menyimpan seluruh data paviliun, instalasi, dan poli RSUD Dr. H. Koesnandi.
   - Atribut kunci: `kode_unit`, `nama`, `tipe`, `kepala`, `nip`.
2. **`users`**
   - Mengelola akun sistem dengan multi-level hak akses (`master_admin`, `admin`, `sub_admin`).
   - Setiap `sub_admin` terafiliasi dengan satu `unit_id`.

### B. Modul Klasifikasi Standar & Penganggaran SIPD
3. **`jenis_astaps`**
   - Klasifikasi Kode 108 BMD Permendagri (15.000+ data referensi).
   - Menentukan kategori KIB A s/d F dan ATB.
4. **`jenis_pengadaans`**
   - Data hierarki SIPD: Program $\rightarrow$ Kegiatan $\rightarrow$ Sub-Kegiatan.
5. **`rekening_belanjas`**
   - Akun kode belanja modal SIPD Pemkab Bondowoso.

### C. Modul Inti Katalog & Register Fisik (NIBAR)
6. **`astaps`**
   - Induk transaksi pengadaan belanja barang.
   - Menyimpan histori SPK, Kwitansi, SP2D, Faktur, BAST Rekanan, PPK, Penyedia.
   - `is_extracomtable`: Menandai barang bernilai $< \text{Rp } 300.000$.
   - `spesifikasi_json`: Menyimpan field dinamis spesifikasi KIB A-F/ATB.
7. **`astap_registers`**
   - Fisik per item barang bernomor **NIBAR 45 Karakter Unik**.
   - Terkoneksi dengan QR Code untuk audit ruangan dan scan publik.
8. **`astap_mutasis`**
   - Histori perpindahan aset antar ruangan (BAMB, ruangan asal, ruangan tujuan).

### D. Modul Distribusi & Serah Terima Internal
9. **`distribusis`**
   - Surat pengiriman/distribusi dan BAST internal ke unit ruangan penerima.
10. **`distribusi_items`**
    - Item detail aset yang diserahkan beserta array list NIBAR yang ditransfer.

---

## 3. Matriks Relasi Antar Tabel

| Tabel Parent | Tabel Child | Foreign Key | Tipe Relasi | Action On Delete |
| :--- | :--- | :--- | :---: | :--- |
| `units` | `users` | `users.unit_id` | **1 : N** | `SET NULL` |
| `units` | `astap_registers` | `astap_registers.unit_id` | **1 : N** | `SET NULL` |
| `units` | `distribusis` | `distribusis.unit_id` | **1 : N** | `CASCADE` |
| `jenis_astaps` | `astaps` | `astaps.jenis_astap_id` | **1 : N** | `SET NULL` |
| `jenis_pengadaans` | `astaps` | `astaps.jenis_pengadaan_id` | **1 : N** | `SET NULL` |
| `rekening_belanjas` | `astaps` | `astaps.rekening_belanja_id` | **1 : N** | `SET NULL` |
| `users` | `astaps` | `astaps.user_id` | **1 : N** | `SET NULL` |
| `astaps` | `astap_registers` | `astap_registers.astap_id` | **1 : N** | `CASCADE` |
| `astap_registers` | `astap_mutasis` | `astap_mutasis.astap_register_id` | **1 : N** | `CASCADE` |
| `distribusis` | `distribusi_items` | `distribusi_items.distribusi_id` | **1 : N** | `CASCADE` |
| `astaps` | `distribusi_items` | `distribusi_items.astap_id` | **1 : N** | `CASCADE` |

---

## 4. Format NIBAR 45 Digit

```
[12013511] [0200000028] [00002026] [132050206001] [0000001]
│          │            │          │              └── No Register Urut (7 digit)
│          │            │          └───────────────── Kode 108 BMD (12 digit)
│          │            └──────────────────────────── Tahun Perolehan (8 digit)
│          └───────────────────────────────────────── Kode Lokasi RSUD (10 digit)
└──────────────────────────────────────────────────── Kode Wilayah Pemkab (8 digit)
```
