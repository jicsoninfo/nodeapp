@extends('layouts.email')
@section('content')
<p class="greeting">Hi {{ $order->user->full_name }},</p>
<p>Your order <strong>{{ $order->order_number }}</strong> has been cancelled.</p>
@if($reason)
<p>Reason: <em>{{ $reason }}</em></p>
@endif
@if($order->payment && $order->payment->status->value === 'captured')
<p>A full refund of <strong>{{ $order->currency }} {{ number_format($order->total_amount, 2) }}</strong> will be processed within 5–7 business days.</p>
@endif
<a class="btn" href="{{ config('app.url') }}">Continue Shopping</a>
@endsection
