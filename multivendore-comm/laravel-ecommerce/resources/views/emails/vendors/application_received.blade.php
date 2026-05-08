@extends('layouts.email')
@section('content')
<p class="greeting">Hi {{ $vendor->owner->full_name }},</p>
<p>Thank you for applying to sell on {{ config('app.name') }}!</p>
<p>We've received your application for <strong>{{ $vendor->store_name }}</strong>.</p>
<hr class="divider">
<p>Our team will review your application within <strong>2–3 business days</strong>. You'll be notified by email once a decision is made.</p>
<a class="btn" href="{{ config('app.url') }}/onboarding/vendor/status">Check Application Status</a>
<p>In the meantime, feel free to explore our <a href="{{ config('app.url') }}/vendor/guide">Vendor Guide</a> to learn how to set up a successful store.</p>
@endsection
