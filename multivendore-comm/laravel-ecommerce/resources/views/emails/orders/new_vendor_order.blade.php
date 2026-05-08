@extends('layouts.email')
@section('content')
<p class="greeting">Hi {{ $vendor->owner->full_name }},</p>
<p>You have a new order waiting to be processed! 🛍️</p>
<p>Order <strong>{{ $order->order_number }}</strong> &middot; Placed {{ $order->placed_at->format('M d, Y g:i A') }}</p>

<table class="order-items">
    <thead><tr><th>SKU</th><th>Qty</th><th>Price</th></tr></thead>
    <tbody>
    @foreach($vendorItems as $item)
        <tr>
            <td>{{ $item->variant?->sku ?? '—' }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ $order->currency }} {{ number_format($item->unit_price, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<a class="btn" href="{{ config('app.url') }}/vendor/orders/{{ $order->id }}">Process Order</a>
<p>Please process this order promptly to maintain your seller rating.</p>
@endsection
