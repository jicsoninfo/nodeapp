@extends('layouts.email')
@section('content')
<p class="greeting">Hi {{ $order->user->full_name }},</p>
<p>Your order is on its way! 🚚</p>
<p>Order <strong>{{ $order->order_number }}</strong></p>

@if($shipment->carrier && $shipment->tracking_number)
<hr class="divider">
<p class="meta">Carrier: <strong>{{ $shipment->carrier }}</strong></p>
<p class="meta">Tracking: <strong>{{ $shipment->tracking_number }}</strong></p>
@if($shipment->estimated_at)
<p class="meta">Estimated delivery: <strong>{{ $shipment->estimated_at->format('M d, Y') }}</strong></p>
@endif
<hr class="divider">
@endif

<a class="btn" href="{{ config('app.url') }}/orders/{{ $order->id }}/tracking">Track Your Package</a>
<p>Thank you for shopping with us!</p>
@endsection
