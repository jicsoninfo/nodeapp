@extends('layouts.email')
@section('content')
<p class="greeting">Hi {{ $user->full_name }},</p>
<p>Your {{ config('app.name') }} account has been temporarily suspended.</p>
@if($reason)
<p>Reason: <em>{{ $reason }}</em></p>
@endif
<hr class="divider">
<p>If you believe this is a mistake, please contact our support team.</p>
<a class="btn" href="{{ config('app.url') }}/support">Contact Support</a>
@endsection
