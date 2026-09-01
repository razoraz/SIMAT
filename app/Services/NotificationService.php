<?php

namespace App\Services;

use App\Models\SystemNotification;
use App\Models\User;
use App\Models\Unit;
use App\Models\Astap;
use App\Models\Distribusi;
use App\Models\AstapMutasi;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Kirim notifikasi ke Admin & Super Admin
     */
    public static function sendToAdminAndMaster(string $title, string $message, string $type = 'info', ?string $link = null): SystemNotification
    {
        return SystemNotification::create([
            'role_target'   => 'admin,master_admin',
            'unit_id'       => null,
            'ruangan_target'=> null,
            'user_id'       => null,
            'title'         => $title,
            'message'       => $message,
            'type'          => $type,
            'link'          => $link,
            'read_by_users' => [],
        ]);
    }

    /**
     * Kirim notifikasi ke Sub Admin dari Unit / Ruangan Tertentu
     */
    public static function sendToUnitSubAdmin(?int $unitId, ?string $ruanganName, string $title, string $message, string $type = 'info', ?string $link = null): SystemNotification
    {
        return SystemNotification::create([
            'role_target'   => 'sub_admin',
            'unit_id'       => $unitId,
            'ruangan_target'=> $ruanganName,
            'user_id'       => null,
            'title'         => $title,
            'message'       => $message,
            'type'          => $type,
            'link'          => $link,
            'read_by_users' => [],
        ]);
    }

    /**
     * Kirim notifikasi ke Pengguna Spesifik
     */
    public static function sendToUser(int $userId, string $title, string $message, string $type = 'info', ?string $link = null): SystemNotification
    {
        return SystemNotification::create([
            'role_target'   => 'user',
            'unit_id'       => null,
            'ruangan_target'=> null,
            'user_id'       => $userId,
            'title'         => $title,
            'message'       => $message,
            'type'          => $type,
            'link'          => $link,
            'read_by_users' => [],
        ]);
    }

    /**
     * Ambil daftar notifikasi yang relevan untuk pengguna tertentu
     */
    public static function getForUser(?User $user, int $limit = 20): array
    {
        if (!$user) {
            return [
                'notifications' => collect([]),
                'unread_count'  => 0,
            ];
        }

        $userId = $user->id;
        $role = $user->role;
        $userUnitId = $user->unit_id;
        $userUnitNama = $user->unit;

        // Hapus otomatis notifikasi yang usianya sudah lebih dari 24 jam
        try {
            SystemNotification::where('created_at', '<', now()->subHours(24))->delete();
        } catch (\Throwable $e) {
            // Ignore cleanup error if table transient
        }

        $query = SystemNotification::query()->where('created_at', '>=', now()->subHours(24));

        if (in_array($role, ['admin', 'master_admin'])) {
            $query->where(function ($q) use ($userId) {
                $q->where('role_target', 'admin,master_admin')
                  ->orWhere('role_target', 'all')
                  ->orWhere('user_id', $userId);
            });
        } else {
            $query->where(function ($q) use ($userId, $userUnitId, $userUnitNama) {
                $q->where('user_id', $userId)
                  ->orWhere(function ($subQ) use ($userUnitId, $userUnitNama) {
                      $subQ->where('role_target', 'sub_admin')
                           ->where(function ($inner) use ($userUnitId, $userUnitNama) {
                               if ($userUnitId) {
                                   $inner->where('unit_id', $userUnitId);
                               }
                               if ($userUnitNama) {
                                   $inner->orWhere('ruangan_target', 'LIKE', '%' . $userUnitNama . '%');
                               }
                               $inner->orWhere(function ($noTarget) {
                                   $noTarget->whereNull('unit_id')->whereNull('ruangan_target');
                               });
                           });
                  })
                  ->orWhere('role_target', 'all');
            });
        }

        $notifications = $query->orderBy('id', 'desc')->take($limit)->get();

        $unreadCount = $notifications->filter(function ($notif) use ($userId) {
            $readers = $notif->read_by_users ?? [];
            return !in_array($userId, $readers);
        })->count();

        // Format waktu relatif dalam Bahasa Indonesia (singkat & rapi)
        Carbon::setLocale('id');

        $formatted = $notifications->map(function ($notif) use ($userId) {
            $readers = $notif->read_by_users ?? [];
            $isUnread = !in_array($userId, $readers);

            $createdAt = $notif->created_at ? Carbon::parse($notif->created_at) : now();
            $timeAgo = $createdAt->locale('id')->diffForHumans([
                'parts' => 1,
                'short' => false,
            ]);

            return [
                'id'         => $notif->id,
                'title'      => $notif->title,
                'message'    => $notif->message,
                'type'       => $notif->type,
                'link'       => $notif->link ?: '#',
                'is_unread'  => $isUnread,
                'time_ago'   => $timeAgo,
                'created_at' => $createdAt->format('d/m/Y H:i'),
            ];
        });

        return [
            'notifications' => $formatted,
            'unread_count'  => $unreadCount,
        ];
    }

    /**
     * Tandai semua notifikasi pengguna sebagai telah dibaca
     */
    public static function markAllAsReadForUser(?User $user): bool
    {
        if (!$user) return false;

        $res = self::getForUser($user, 100);
        $ids = $res['notifications']->pluck('id')->toArray();

        if (empty($ids)) return true;

        $notifs = SystemNotification::whereIn('id', $ids)->get();
        foreach ($notifs as $notif) {
            $readers = $notif->read_by_users ?? [];
            if (!in_array($user->id, $readers)) {
                $readers[] = $user->id;
                $notif->read_by_users = array_values(array_unique($readers));
                $notif->save();
            }
        }

        return true;
    }

    /**
     * Isi data notifikasi awal dari transaksi riil yang sudah ada di sistem (format singkat & padat)
     */
    public static function seedInitialNotifications(): void
    {
        SystemNotification::truncate();

        // 1. Notifikasi ASTAP Baru
        $recentAstaps = Astap::latest('id')->take(4)->get();
        foreach ($recentAstaps as $a) {
            self::sendToAdminAndMaster(
                "Aset Baru: {$a->nama_barang}",
                "{$a->jumlah_volume} {$a->satuan} • " . ($a->tahun_perolehan ?: date('Y')),
                'astap',
                route('astap.index')
            );
        }

        // 2. Notifikasi Distribusi
        $recentDistribusis = Distribusi::with('unit')->latest('id')->take(4)->get();
        foreach ($recentDistribusis as $d) {
            $unitName = $d->unit?->nama ?? 'Ruangan RSUD';
            $status = $d->status ?: 'Telah Diterima';
            
            // Untuk Admin & Master Admin
            self::sendToAdminAndMaster(
                "Distribusi: {$unitName}",
                "{$d->kode} • Status: {$status}",
                'distribusi',
                route('distribusi.index')
            );

            // Untuk Sub Admin unit tersebut
            if ($d->unit_id) {
                self::sendToUnitSubAdmin(
                    $d->unit_id,
                    $unitName,
                    "Distribusi Masuk: {$unitName}",
                    "{$d->kode} • Status: {$status}",
                    'distribusi',
                    route('distribusi.index')
                );
            }
        }

        // 3. Notifikasi Mutasi
        $recentMutasis = AstapMutasi::latest('id')->take(4)->get();
        foreach ($recentMutasis as $m) {
            $status = $m->status ?: 'Selesai';

            // Untuk Admin & Master Admin
            self::sendToAdminAndMaster(
                "Mutasi: {$m->ruangan_asal} → {$m->ruangan_tujuan}",
                "{$m->nomor_bamb} • Status: {$status}",
                'mutasi',
                route('mutasi.index')
            );

            // Untuk Sub Admin ruangan tujuan
            $targetUnit = Unit::where('nama', $m->ruangan_tujuan)->first();
            self::sendToUnitSubAdmin(
                $targetUnit?->id,
                $m->ruangan_tujuan,
                "Mutasi Masuk: dari {$m->ruangan_asal}",
                "{$m->nomor_bamb} • Status: {$status}",
                'mutasi',
                route('mutasi.index')
            );
        }
    }
}
