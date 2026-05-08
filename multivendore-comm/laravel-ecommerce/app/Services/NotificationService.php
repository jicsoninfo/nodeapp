<?php
namespace App\Services;

use App\Jobs\SendPushNotification;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Str;

class NotificationService
{
    /** Send in-app + optional push to a single user. */
    public function send(User $user, string $type, string $title, string $body, array $data = [], bool $push = true): UserNotification
    {
        $notification = UserNotification::create([
            'user_id'  => $user->id,
            'type'     => $type,
            'channel'  => 'in_app',
            'title'    => $title,
            'body'     => $body,
            'data'     => $data,
            'is_read'  => false,
        ]);

        if ($push) {
            SendPushNotification::dispatch($user, $title, $body, $data)->onQueue('notifications');
        }

        return $notification;
    }

    /** Broadcast push to a Firebase topic (all_users, all_buyers, etc.). */
    public function broadcast(string $topic, string $title, string $body, array $data = []): void
    {
        SendPushNotification::dispatchToTopic($topic, $title, $body, $data);
    }

    /** Bulk send in-app notifications (e.g. price-drop alerts) — chunked to avoid memory issues. */
    public function sendBulk(iterable $userIds, string $type, string $title, string $body, array $data = []): int
    {
        $rows  = [];
        $now   = now();
        $count = 0;

        foreach ($userIds as $userId) {
            $rows[] = [
                'id'         => (string) Str::uuid(),
                'user_id'    => $userId,
                'type'       => $type,
                'channel'    => 'in_app',
                'title'      => $title,
                'body'       => $body,
                'data'       => json_encode($data),
                'is_read'    => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $count++;

            if (count($rows) >= 500) {
                UserNotification::insert($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            UserNotification::insert($rows);
        }

        return $count;
    }

    /** Mark all unread notifications as read for a user. */
    public function markAllRead(User $user): int
    {
        return UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    /** Return unread count for a user (cached 60s). */
    public function unreadCount(User $user): int
    {
        return \Illuminate\Support\Facades\Cache::remember(
            "unread_notifications:{$user->id}",
            60,
            fn () => UserNotification::where('user_id', $user->id)->where('is_read', false)->count()
        );
    }
}
