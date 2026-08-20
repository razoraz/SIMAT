<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JenisAstapController;
use App\Http\Controllers\JenisPengadaanController;
use App\Http\Controllers\RekeningBelanjaController;
use App\Http\Controllers\UnitController;
use App\Http\Middleware\RoleMiddleware;

// Auth Routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Redirect / or /dashboard to specific role dashboard
Route::middleware('auth')->get('/', function () {
    $role = Auth::user()->role;
    return match ($role) {
        'master_admin' => redirect()->route('masteradmin.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        'sub_admin' => redirect()->route('subadmin.dashboard'),
        default => redirect()->route('login'),
    };
});

Route::middleware('auth')->get('/dashboard', function () {
    $role = Auth::user()->role;
    return match ($role) {
        'master_admin' => redirect()->route('masteradmin.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        'sub_admin' => redirect()->route('subadmin.dashboard'),
        default => redirect()->route('login'),
    };
});

// Dashboard Master Admin
Route::middleware(['auth', RoleMiddleware::class . ':master_admin'])->group(function () {
    Route::get('/master-admin/dashboard', function () {
        return view('dashboards.master_admin');
    })->name('masteradmin.dashboard');
});

// Dashboard Admin
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('dashboards.admin');
    })->name('admin.dashboard');
});

// Dashboard Sub Admin
Route::middleware(['auth', RoleMiddleware::class . ':sub_admin'])->group(function () {
    Route::get('/sub-admin/dashboard', function () {
        return view('dashboards.sub_admin');
    })->name('subadmin.dashboard');
});

// Frontend Menu & Form Pages (Auth Protected)
Route::middleware('auth')->group(function () {
    
    // 1. Data ASTAP Pages
    Route::get('/astap', function () {
        return view('pages.data_astap');
    })->name('astap.index');

    // 2. Distribusi Pages & Forms
    Route::get('/distribusi', function () {
        return view('pages.distribusi');
    })->name('distribusi.index');

    Route::get('/distribusi/create', function () {
        $units = \App\Models\Unit::orderBy('id', 'asc')->get()->map(function($u) {
            return [
                'id' => $u->id,
                'nama' => $u->nama,
                'tipe' => $u->tipe,
                'kepala' => $u->kepala,
                'nip' => $u->nip ?: '-',
                'jabatan' => 'Kepala / Penanggung Jawab ' . $u->nama
            ];
        });
        return view('pages.form_distribusi', compact('units'));
    })->name('distribusi.create');

    Route::get('/distribusi/{id}/edit', function ($id) {
        $units = \App\Models\Unit::orderBy('id', 'asc')->get()->map(function($u) {
            return [
                'id' => $u->id,
                'nama' => $u->nama,
                'tipe' => $u->tipe,
                'kepala' => $u->kepala,
                'nip' => $u->nip ?: '-',
                'jabatan' => 'Kepala / Penanggung Jawab ' . $u->nama
            ];
        });
        return view('pages.form_distribusi', compact('units', 'id'));
    })->name('distribusi.edit');

    // 4. Mutasi Aset Pages & Forms
    Route::get('/mutasi-aset', function () {
        return view('pages.mutasi_aset');
    })->name('mutasi.index');

    Route::get('/mutasi-aset/create', function () {
        return view('pages.form_mutasi_aset');
    })->name('mutasi.create');

    Route::get('/mutasi-aset/{id}/edit', function ($id) {
        return view('pages.form_mutasi_aset', ['id' => $id]);
    })->name('mutasi.edit');

    // 5. Unit & Paviliun Index (Read-only for Sub Admin, full for Admin)
    Route::get('/unit-paviliun', [UnitController::class, 'index'])->name('unit.index');

    // 6. Pemeliharaan Index (Read-only for Sub Admin, full for Admin)
    Route::get('/pemeliharaan', function () {
        return view('pages.pemeliharaan');
    })->name('pemeliharaan.index');

    // Rute Khusus Master Admin & Admin Operasional (Sub Admin Dibatasi)
    Route::middleware([RoleMiddleware::class . ':master_admin,admin'])->group(function () {
        // Berita Acara (BAST)
        Route::get('/berita-acara', function () {
            return view('pages.berita_acara');
        })->name('bast.index');

        Route::get('/berita-acara/create', function () {
            return view('pages.form_berita_acara');
        })->name('bast.create');

        Route::get('/berita-acara/{id}/edit', function ($id) {
            return view('pages.form_berita_acara', ['id' => $id]);
        })->name('bast.edit');

        // Form Tambah & Edit ASTAP
        Route::get('/astap/create', function () {
            $dbMaster108 = \App\Models\JenisAstap::getNested108();
            return view('pages.form_astap', compact('dbMaster108'));
        })->name('astap.create');

        Route::get('/astap/{id}/edit', function ($id) {
            $dbMaster108 = \App\Models\JenisAstap::getNested108();
            return view('pages.form_astap', ['id' => $id, 'dbMaster108' => $dbMaster108]);
        })->name('astap.edit');

        // Form Tambah, Simpan, Edit, Update & Hapus Unit / Paviliun
        Route::get('/unit-paviliun/create', [UnitController::class, 'create'])->name('unit.create');
        Route::post('/unit-paviliun', [UnitController::class, 'store'])->name('unit.store');
        Route::get('/unit-paviliun/{id}/edit', [UnitController::class, 'edit'])->name('unit.edit');
        Route::put('/unit-paviliun/{id}', [UnitController::class, 'update'])->name('unit.update');
        Route::delete('/unit-paviliun/{id}', [UnitController::class, 'destroy'])->name('unit.destroy');

        // Form Tambah & Edit Pemeliharaan
        Route::get('/pemeliharaan/create', function () {
            return view('pages.form_pemeliharaan');
        })->name('pemeliharaan.create');

        Route::get('/pemeliharaan/{id}/edit', function ($id) {
            return view('pages.form_pemeliharaan', ['id' => $id]);
        })->name('pemeliharaan.edit');
    });

    // Master Data Users CRUD Routes
    Route::get('/master-data/users', [UserController::class, 'index'])->name('master.users');
    Route::post('/master-data/users', [UserController::class, 'store'])->name('master.users.store');
    Route::put('/master-data/users/{id}', [UserController::class, 'update'])->name('master.users.update');
    Route::delete('/master-data/users/{id}', [UserController::class, 'destroy'])->name('master.users.destroy');
    Route::post('/master-data/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('master.users.reset_password');

    Route::get('/master-data/jenis-astap', [JenisAstapController::class, 'index'])->name('master.jenis_astap');
    Route::post('/master-data/jenis-astap', [JenisAstapController::class, 'store'])->name('master.jenis_astap.store');
    Route::post('/master-data/jenis-astap/import', [JenisAstapController::class, 'import'])->name('master.jenis_astap.import');
    Route::get('/master-data/jenis-astap/download-template', [JenisAstapController::class, 'downloadTemplate'])->name('master.jenis_astap.template');
    Route::put('/master-data/jenis-astap/{id}', [JenisAstapController::class, 'update'])->name('master.jenis_astap.update');
    Route::delete('/master-data/jenis-astap/{id}', [JenisAstapController::class, 'destroy'])->name('master.jenis_astap.destroy');

    // Master Data Jenis Pengadaan (SIPD)
    Route::get('/master-data/jenis-pengadaan', [JenisPengadaanController::class, 'index'])->name('master.jenis_pengadaan');
    Route::post('/master-data/jenis-pengadaan', [JenisPengadaanController::class, 'store'])->name('master.jenis_pengadaan.store');
    Route::put('/master-data/jenis-pengadaan/{id}', [JenisPengadaanController::class, 'update'])->name('master.jenis_pengadaan.update');
    Route::delete('/master-data/jenis-pengadaan/{id}', [JenisPengadaanController::class, 'destroy'])->name('master.jenis_pengadaan.destroy');

    // Master Data Rekening Belanja (SIPD)
    Route::get('/master-data/rekening-belanja', [RekeningBelanjaController::class, 'index'])->name('master.rekening_belanja');
    Route::post('/master-data/rekening-belanja', [RekeningBelanjaController::class, 'store'])->name('master.rekening_belanja.store');
    Route::put('/master-data/rekening-belanja/{id}', [RekeningBelanjaController::class, 'update'])->name('master.rekening_belanja.update');
    Route::delete('/master-data/rekening-belanja/{id}', [RekeningBelanjaController::class, 'destroy'])->name('master.rekening_belanja.destroy');
});
