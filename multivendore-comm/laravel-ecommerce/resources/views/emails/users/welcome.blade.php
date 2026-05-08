@extends('layouts.email')
@section('content')
<p class="greeting">Welcome, {{ $user->full_name ?? $user->email }}! 🎉</p>
<p>We're thrilled to have you on board. Here's what you can do right now:</p>
<hr class="divider">
<p>🛍️ &nbsp;<strong>Browse</strong> thousands of products from verified vendors</p>
<p>❤️ &nbsp;<strong>Wishlist</strong> your favourite items for later</p>
<p>⚡ &nbsp;<strong>Fast delivery</strong> across the globe</p>
<p>🔒 &nbsp;<strong>Secure payments</strong> with buyer protection</p>
<hr class="divider">
<a class="btn" href="{{ config('app.url') }}">Start Shopping</a>
<p>Use code <strong>NEWUSER</strong> for an instant discount on your first order.</p>
@endsection
