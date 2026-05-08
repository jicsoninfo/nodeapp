<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends a push notification to one or many users via Firebase Cloud Messaging (FCM).
 *
 * Single user:
 *   SendPushNotification::dispatch($user, 'Order Shipped!', 'Your order ORD-001 is on its way.');
 *
 * Multiple users (topic broadcast):
 *   SendPushNotification::dispatchToTopic('all_buyers', 'Flash Sale!', 'Up to 50% off today only.');
 *
 * With custom data payload:
 *   SendPushNotification::dispatch($user, 'New Message', 'You have a reply.', ['order_id' => '123']);
 */
class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int    $tries   = 3;
    public int    $timeout = 30;
    public string $queue   = 'notifications';
    public int    $backoff = 10;

    /**
     * @param User|null  $user    Target user (null when using topic)
     * @param string     $title   Notification title
     * @param string     $body    Notification body text
     * @param array      $data    Custom key-value payload (deep-link routing etc.)
     * @param string|null $topic  FCM topic name (used instead of $user when set)
     * @param string      $icon   Notification icon name
     * @param string|null $imageUrl  Rich notification image URL
     * @param string|null $clickAction  Deep-link URL or activity name
     */
    public function __construct(
        public readonly ?User   $user,
        public readonly string  $title,
        public readonly string  $body,
        public readonly array   $data        = [],
        public readonly ?string $topic       = null,
        public readonly string  $icon        = 'ic_notification',
        public readonly ?string $imageUrl    = null,
        public readonly ?string $clickAction = null,
    ) {}

    // ── Static convenience constructors ──────────────────

    public static function dispatchToTopic(
        string $topic,
        string $title,
        string $body,
        array  $data = [],
    ): void {
        static::dispatch(null, $title, $body, $data, $topic);
    }

    // ── Handle ────────────────────────────────────────────

    public function handle(): void
    {
        $tokens = $this->resolveTokens();

        if (empty($tokens) && ! $this->topic) {
            Log::info('SendPushNotification: no FCM tokens found', [
                'user_id' => $this->user?->id,
            ]);
            return;
        }

        $serverKey = config('services.firebase.server_key');

        if (! $serverKey) {
            Log::warning('SendPushNotification: FIREBASE_SERVER_KEY not configured');
            return;
        }

        $payload = $this->buildPayload($tokens);

        $response = Http::withHeaders([
            'Authorization' => "key={$serverKey}",
            'Content-Type'  => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', $payload);

        if ($response->failed()) {
            Log::error('FCM push failed', [
                'status'  => $response->status(),
                'body'    => $response->body(),
                'user_id' => $this->user?->id,
                'topic'   => $this->topic,
            ]);
            $this->fail(new \RuntimeException("FCM returned HTTP {$response->status()}"));
            return;
        }

        $result = $response->json();

        // Handle partial failures — tokens that are stale or unregistered
        if (isset($result['results'])) {
            foreach ($result['results'] as $i => $res) {
                if (isset($res['error']) && in_array($res['error'], ['NotRegistered', 'InvalidRegistration'])) {
                    $this->invalidateToken($tokens[$i] ?? null);
                }
            }
        }

        Log::info('Push notification sent', [
            'user_id'  => $this->user?->id,
            'topic'    => $this->topic,
            'title'    => $this->title,
            'success'  => $result['success'] ?? 0,
            'failure'  => $result['failure'] ?? 0,
        ]);

        // Store in-app notification record
        $this->storeInAppNotification();
    }

    // ── Helpers ───────────────────────────────────────────

    private function resolveTokens(): array
    {
        if (! $this->user) return [];

        // Fetch FCM tokens from user meta / device table
        // Adjust this to wherever you store device tokens
        return \DB::table('user_device_tokens')
            ->where('user_id', $this->user->id)
            ->where('is_active', true)
            ->pluck('fcm_token')
            ->toArray();
    }

    private function buildPayload(array $tokens): array
    {
        $notification = array_filter([
            'title'     => $this->title,
            'body'      => $this->body,
            'icon'      => $this->icon,
            'image'     => $this->imageUrl,
            'click_action' => $this->clickAction,
            'sound'     => 'default',
        ]);

        $data = array_merge($this->data, [
            'title' => $this->title,
            'body'  => $this->body,
        ]);

        // Topic broadcast
        if ($this->topic) {
            return [
                'to'           => "/topics/{$this->topic}",
                'notification' => $notification,
                'data'         => $data,
                'priority'     => 'high',
            ];
        }

        // Single user with multiple device tokens
        if (count($tokens) === 1) {
            return [
                'to'           => $tokens[0],
                'notification' => $notification,
                'data'         => $data,
                'priority'     => 'high',
            ];
        }

        // Multiple tokens (multicast)
        return [
            'registration_ids' => $tokens,
            'notification'     => $notification,
            'data'             => $data,
            'priority'         => 'high',
        ];
    }

    private function invalidateToken(?string $token): void
    {
        if (! $token) return;

        \DB::table('user_device_tokens')
            ->where('fcm_token', $token)
            ->update(['is_active' => false]);
    }

    private function storeInAppNotification(): void
    {
        if (! $this->user) return;

        \DB::table('user_notifications')->insert([
            'id'         => \Illuminate\Support\Str::uuid(),
            'user_id'    => $this->user->id,
            'type'       => $this->data['type'] ?? 'push',
            'channel'    => 'push',
            'title'      => $this->title,
            'body'       => $this->body,
            'data'       => json_encode($this->data),
            'is_read'    => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('SendPushNotification permanently failed', [
            'user_id' => $this->user?->id,
            'title'   => $this->title,
            'error'   => $e->getMessage(),
        ]);
    }
}
