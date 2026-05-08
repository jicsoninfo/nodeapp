@extends('layouts.email')
@section('content')
<p class="greeting">Hi {{ $user->full_name }},</p>
<p>Great news! An item on your wishlist just dropped in price. 🔥</p>
<hr class="divider">
<p><strong>{{ $productName }}</strong></p>
<p class="meta">Was: <s>{{ $currency }} {{ number_format($oldPrice, 2) }}</s></p>
<p class="meta">Now: <strong style="font-size:18px;color:#18181b;">{{ $currency }} {{ number_format($newPrice, 2) }}</strong></p>
<p class="meta">You save: {{ $currency }} {{ number_format($oldPrice - $newPrice, 2) }}</p>
<hr class="divider">
<a class="btn" href="{{ $productUrl }}">Buy Now — Limited Time</a>
<p>This price may not last long. Act fast!</p>
@endsection
