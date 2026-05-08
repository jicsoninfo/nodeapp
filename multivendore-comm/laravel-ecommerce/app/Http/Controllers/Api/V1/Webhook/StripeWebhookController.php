<?php
namespace App\Http\Controllers\Api\V1\Webhook;
use App\Contracts\Services\PaymentServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __construct(private readonly PaymentServiceInterface $payments) {}

    public function __invoke(Request $request): Response
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature invalid', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Stripe webhook payload invalid', ['error' => $e->getMessage()]);
            return response('Invalid payload', 400);
        }

        try {
            $this->payments->handleWebhook($event->toArray(), 'stripe');
        } catch (\Throwable $e) {
            Log::error('Stripe webhook handler failed', ['type' => $event->type, 'error' => $e->getMessage()]);
            return response('Handler error', 500);
        }

        return response('OK', 200);
    }
}
