<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'read_by_users' => 'array',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Helper cek apakah notifikasi ini sudah dibaca oleh user tertentu
     */
    public function isReadBy($userId): bool
    {
        $readers = $this->read_by_users ?? [];
        return in_array($userId, $readers);
    }
}
