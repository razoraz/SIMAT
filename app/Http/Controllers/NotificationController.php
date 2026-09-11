<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Tandai semua notifikasi telah dibaca untuk user yang login
     */
    public function markAllRead()
    {
        NotificationService::markAllAsReadForUser(auth()->user());
        return response()->json(['success' => true, 'message' => 'Semua notifikasi telah ditandai sebagai dibaca.']);
    }

    /**
     * Ambil daftar notifikasi untuk user yang login
     */
    public function list()
    {
        $res = NotificationService::getForUser(auth()->user());
        return response()->json([
            'success'       => true,
            'unread_count'  => $res['unread_count'],
            'notifications' => $res['notifications'],
        ]);
    }
}
