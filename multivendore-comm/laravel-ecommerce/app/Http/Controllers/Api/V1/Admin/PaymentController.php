<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse { return response()->json(['data' => Payment::with('order.user')->when($request->status,fn($q)=>$q->where('status',$request->status))->latest()->paginate(25)]); }
    public function show(Payment $payment): JsonResponse { return response()->json(['data' => $payment->load('order.user')]); }
    public function refund(Request $request): JsonResponse
    {
        $request->validate(['payment_id'=>'required|uuid|exists:payments,id','amount'=>'required|numeric|min:0.01']);
        $payment = Payment::findOrFail($request->payment_id);
        app(\App\Contracts\Services\PaymentServiceInterface::class)->refund($payment, $request->amount);
        return response()->json(['message' => 'Refund initiated.']);
    }
}
