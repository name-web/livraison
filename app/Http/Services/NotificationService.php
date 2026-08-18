<?php

namespace App\Http\Services;

use App\Models\Backend\NewsOffer;
use App\Models\Backend\Notification;
use App\Models\Backend\Support;
use App\Models\Backend\SupportChat;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Récupère toutes les notifications (système, support, actualités) pour l'utilisateur courant.
     */
    public function getForCurrentUser(): Collection
    {
        $notifications = [];

        // Notifications système
        $notifications = array_merge($notifications, $this->getSystemNotifications());

        // Notifications support (7 derniers jours)
        $notifications = array_merge($notifications, $this->getSupportNotifications());

        // Actualités (5 dernières)
        $notifications = array_merge($notifications, $this->getNewsNotifications());

        return collect($notifications)->sortByDesc('created_at');
    }

    /**
     * Récupère une news/offre pour une date donnée.
     */
    public function getNewsForDate(string $date): ?NewsOffer
    {
        $from = Carbon::parse($date)->startOfDay()->toDateTimeString();
        $to = Carbon::parse($date)->endOfDay()->toDateTimeString();

        return NewsOffer::whereBetween('created_at', [$from, $to])
            ->orderBy('id', 'desc')
            ->first();
    }

    // ─── Privés ────────────────────────────────

    private function getSystemNotifications(): array
    {
        $notifications = [];

        foreach (Notification::all() as $notification) {
            $notifications[] = [
                'type' => $notification->type,
                'user_id' => $notification->created_by,
                'subject' => $notification->title,
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                'created_by' => $notification->created_by,
            ];
        }

        return $notifications;
    }

    private function getSupportNotifications(): array
    {
        $notifications = [];
        $sevenDaysAgo = Carbon::today()->subDays(7)->startOfDay()->toDateTimeString();
        $today = Carbon::today()->endOfDay()->toDateTimeString();
        $userId = Auth::id();

        // Supports créés par d'autres
        $supports = Support::whereNot('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->select('id', 'user_id', 'subject', 'created_at')
            ->whereBetween('created_at', [$sevenDaysAgo, $today])
            ->get();

        foreach ($supports as $support) {
            $notifications[] = [
                'type' => 'support',
                'support_id' => $support->id,
                'user_id' => $support->user_id,
                'subject' => $support->subject,
                'created_at' => $support->created_at->format('Y-m-d H:i:s'),
            ];
        }

        // Messages de support
        $chatsBySupport = SupportChat::orderBy('created_at', 'DESC')
            ->select('id', 'support_id', 'user_id', 'created_at')
            ->whereBetween('created_at', [$sevenDaysAgo, $today])
            ->get()
            ->groupBy('support_id');

        foreach ($chatsBySupport as $supportId => $chats) {
            $supportCheck = Support::find($supportId);
            if (! $supportCheck) {
                continue;
            }

            if ($supportCheck->user_id == $userId) {
                // L'utilisateur est le propriétaire du support
                foreach ($chats as $chat) {
                    if ($chat->user_id !== $userId) {
                        $notifications[] = $this->buildSupportChatNotification($chat);
                    }
                }
            } else {
                // L'utilisateur a participé au chat
                $chatsUserIds = $chats->pluck('user_id')->toArray();
                if (in_array($userId, $chatsUserIds)) {
                    $firstChatCheck = SupportChat::where([
                        'support_id' => $supportId,
                        'user_id' => $userId,
                    ])->first();

                    $firstChatDate = strtotime($firstChatCheck->created_at->format('Y-m-d H:i:s'));

                    foreach ($chats as $chat) {
                        if ($chat->user_id !== $userId) {
                            $chatDateTime = strtotime($chat->created_at->format('Y-m-d H:i:s'));
                            if ($firstChatDate <= $chatDateTime) {
                                $notifications[] = $this->buildSupportChatNotification($chat);
                            }
                        }
                    }
                }
            }
        }

        return $notifications;
    }

    private function buildSupportChatNotification($chat): array
    {
        return [
            'type' => 'support',
            'support_id' => $chat->support_id,
            'user_id' => $chat->user_id,
            'subject' => $chat->support->subject,
            'created_at' => $chat->created_at->format('Y-m-d H:i:s'),
        ];
    }

    private function getNewsNotifications(): array
    {
        $notifications = [];

        foreach (NewsOffer::orderBy('created_at', 'DESC')->limit(5)->get() as $news) {
            $notifications[] = [
                'type' => 'newsoffer',
                'user_id' => $news->author,
                'subject' => $news->title,
                'created_at' => $news->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $notifications;
    }
}
