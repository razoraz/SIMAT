<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait TrackableSoftDelete
{
    /**
     * Scope untuk mengambil hanya data yang aktif (belum dihapus / label 0).
     */
    public function scopeActive($query)
    {
        return $query->where($this->getTable() . '.is_deleted', 0);
    }

    /**
     * Scope untuk mengambil hanya data yang pernah dihapus (label 1).
     */
    public function scopeOnlyDeleted($query)
    {
        return $query->where($this->getTable() . '.is_deleted', 1);
    }

    /**
     * Relasi ke pengguna yang menghapus data ini.
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by_id');
    }

    /**
     * Periksa apakah data ini berstatus terhapus.
     */
    public function isDeleted(): bool
    {
        return (bool) ($this->is_deleted ?? false);
    }

    /**
     * Lakukan soft delete dengan menandai label is_deleted = 1,
     * serta mencatat nama pengguna, ID, dan waktu penghapusan.
     */
    public function softDelete(?string $reason = null): bool
    {
        $user = Auth::user();
        $deleterName = $user 
            ? ($user->name . ' (' . ucfirst($user->role ?? 'user') . ')') 
            : 'Administrator';

        $payload = [
            'is_deleted'    => 1,
            'deleted_by'    => $deleterName,
            'deleted_by_id' => $user?->id,
            'deleted_at'    => now(),
        ];

        // Jika model memiliki kolom alasan_hapus, simpan alasan
        if ($reason && in_array('alasan_hapus', $this->getFillable())) {
            $payload['alasan_hapus'] = $reason;
        }

        return $this->update($payload);
    }

    /**
     * Pulihkan kembali data yang terhapus (mengembalikan label is_deleted ke 0).
     */
    public function restoreData(): bool
    {
        $payload = [
            'is_deleted'    => 0,
            'deleted_by'    => null,
            'deleted_by_id' => null,
            'deleted_at'    => null,
        ];

        if (in_array('alasan_hapus', $this->getFillable())) {
            $payload['alasan_hapus'] = null;
        }

        return $this->update($payload);
    }
}
