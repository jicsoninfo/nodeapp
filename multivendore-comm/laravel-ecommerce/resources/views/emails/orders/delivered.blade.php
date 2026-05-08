@extends('layouts.email')
@section('content')
<p class="greeting">Hi {{ $order->user->full_name }},</p>
<p>Your order <strong>{{ $order->order_number }}</strong> has been delivered! 🎉 We hope you love it.</p>
<a class="btn" href="{{ config('app.url') }}/orders/{{ $order->id }}/review">Leave a Review</a>
<p>Your feedback helps other shoppers make great decisions. If anything is wrong, please contact our support team within 48 hours.</p>
@endsection
