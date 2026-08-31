<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Inject Notifikasi Sistem ke partials.topbar (aman untuk guest/public page)
        View::composer('partials.topbar', function ($view) {
            try {
                $user = Auth::user();
                if (!$user) {
                    $view->with(['systemNotifications' => collect([]), 'unreadNotifCount' => 0]);
                    return;
                }
                $notifData = NotificationService::getForUser($user, 15);
                $view->with([
                    'systemNotifications' => $notifData['notifications'],
                    'unreadNotifCount'    => $notifData['unread_count'],
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Topbar ViewComposer error: ' . $e->getMessage());
                $view->with(['systemNotifications' => collect([]), 'unreadNotifCount' => 0]);
            }
        });
    }
}
