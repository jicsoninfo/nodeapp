<?php
namespace App\Http\Controllers\Api\V1\Webhook;
use App\Contracts\Services\PaymentServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    public function __construct(private readonly PaymentServiceInterface $payments) {}

    public function __invoke(Request $request): Response
    {
        $payload   = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');
        $secret    = config('services.razorpay.webhook_secret');

        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($expectedSignature, $signature ?? '')) {
            Log::warning('Razorpay webhook signature invalid');
            return response('Invalid signature', 400);
        }

        try {
            $this->payments->handleWebhook($request->all(), 'razorpay');
        } catch (\Throwable $e) {
            Log::error('Razorpay webhook handler failed', ['error' => $e->getMessage()]);
            return response('Handler error', 500);
        }

        return response('OK', 200);
    }
}
