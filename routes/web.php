<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
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
    
    // 1. Data ASTAP Pages & Forms
    Route::get('/astap', function () {
        return view('pages.data_astap');
    })->name('astap.index');

    Route::get('/astap/create', function () {
        return view('pages.form_astap');
    })->name('astap.create');

    Route::get('/astap/{id}/edit', function ($id) {
        return view('pages.form_astap', ['id' => $id]);
    })->name('astap.edit');

    // 2. Pengadaan Pages & Forms
    Route::get('/pengadaan', function () {
        return view('pages.pengadaan');
    })->name('pengadaan.index');

    Route::get('/pengadaan/create', function () {
        return view('pages.form_pengadaan');
    })->name('pengadaan.create');

    Route::get('/pengadaan/{id}/edit', function ($id) {
        return view('pages.form_pengadaan', ['id' => $id]);
    })->name('pengadaan.edit');

    // 3. Distribusi Pages & Forms
    Route::get('/distribusi', function () {
        return view('pages.distribusi');
    })->name('distribusi.index');

    Route::get('/distribusi/create', function () {
        return view('pages.form_distribusi');
    })->name('distribusi.create');

    Route::get('/distribusi/{id}/edit', function ($id) {
        return view('pages.form_distribusi', ['id' => $id]);
    })->name('distribusi.edit');

    // 4. Berita Acara (BAST) Pages & Forms
    Route::get('/berita-acara', function () {
        return view('pages.berita_acara');
    })->name('bast.index');

    Route::get('/berita-acara/create', function () {
        return view('pages.form_berita_acara');
    })->name('bast.create');

    Route::get('/berita-acara/{id}/edit', function ($id) {
        return view('pages.form_berita_acara', ['id' => $id]);
    })->name('bast.edit');

    // 5. Pemeliharaan Pages & Forms
    Route::get('/pemeliharaan', function () {
        return view('pages.pemeliharaan');
    })->name('pemeliharaan.index');

    Route::get('/pemeliharaan/create', function () {
        return view('pages.form_pemeliharaan');
    })->name('pemeliharaan.create');

    Route::get('/pemeliharaan/{id}/edit', function ($id) {
        return view('pages.form_pemeliharaan', ['id' => $id]);
    })->name('pemeliharaan.edit');

    // 6. Mutasi Aset Pages & Forms
    Route::get('/mutasi-aset', function () {
        return view('pages.mutasi_aset');
    })->name('mutasi.index');

    Route::get('/mutasi-aset/create', function () {
        return view('pages.form_mutasi_aset');
    })->name('mutasi.create');

    Route::get('/mutasi-aset/{id}/edit', function ($id) {
        return view('pages.form_mutasi_aset', ['id' => $id]);
    })->name('mutasi.edit');

    // 7. Unit & Paviliun Pages & Forms
    Route::get('/unit-paviliun', function () {
        return view('pages.unit_paviliun');
    })->name('unit.index');

    Route::get('/unit-paviliun/create', function () {
        return view('pages.form_unit_paviliun');
    })->name('unit.create');

    Route::get('/unit-paviliun/{id}/edit', function ($id) {
        return view('pages.form_unit_paviliun', ['id' => $id]);
    })->name('unit.edit');

    // Master Data System Pages
    Route::get('/master-data/users', function () {
        return view('pages.master_users');
    })->name('master.users');

    Route::get('/master-data/jenis-astap', function () {
        return view('pages.master_jenis_astap');
    })->name('master.jenis_astap');

    Route::get('/master-data/jenis-pengadaan', function () {
        return view('pages.master_jenis_pengadaan');
    })->name('master.jenis_pengadaan');

    Route::get('/master-data/rekening-belanja', function () {
        return view('pages.master_rekening_belanja');
    })->name('master.rekening_belanja');
});
