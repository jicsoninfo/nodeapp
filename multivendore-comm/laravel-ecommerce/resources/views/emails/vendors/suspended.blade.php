@extends('layouts.email')
@section('content')
<p class="greeting">Hi {{ $vendor->owner->full_name }},</p>
<p>We regret to inform you that your store <strong>{{ $vendor->store_name }}</strong> has been temporarily suspended.</p>
<p>Reason: <em>{{ $reason }}</em></p>
<hr class="divider">
<p>If you believe this is an error or wish to appeal, please contact our Trust & Safety team.</p>
<a class="btn" href="{{ config('app.url') }}/support">Contact Support</a>
@endsection
