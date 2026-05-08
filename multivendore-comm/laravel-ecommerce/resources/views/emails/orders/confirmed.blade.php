@extends('layouts.email')
@section('content')
<p class="greeting">Hi {{ $order->user->full_name }},</p>
<p>Great news! Your order has been confirmed and we're getting it ready.</p>

<table class="order-items">
    <thead><tr><th>Item</th><th>Qty</th><th>Price</th></tr></thead>
    <tbody>
    @foreach($order->items as $item)
        <tr>
            <td>{{ $item->variant?->sku ?? '—' }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ $order->currency }} {{ number_format($item->unit_price, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<hr class="divider">
<p class="meta">Subtotal: <strong>{{ $order->currency }} {{ number_format($order->subtotal, 2) }}</strong></p>
@if($order->discount_amount > 0)
<p class="meta">Discount: <strong>-{{ $order->currency }} {{ number_format($order->discount_amount, 2) }}</strong></p>
@endif
<p class="meta">Tax: {{ $order->currency }} {{ number_format($order->tax_amount, 2) }}</p>
<p class="meta">Shipping: {{ $order->shipping_amount > 0 ? $order->currency . ' ' . number_format($order->shipping_amount, 2) : 'Free' }}</p>
<p class="meta"><strong>Total: {{ $order->currency }} {{ number_format($order->total_amount, 2) }}</strong></p>
<hr class="divider">

<p>Order <strong>{{ $order->order_number }}</strong> &middot; Placed {{ $order->placed_at->format('M d, Y') }}</p>
<a class="btn" href="{{ config('app.url') }}/orders/{{ $order->id }}">View Order</a>
<p>We'll notify you as soon as your order ships. Thank you for shopping with us!</p>
@endsection
