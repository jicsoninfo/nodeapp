@extends('layouts.email')
@section('content')
<p class="greeting">Hi {{ $payout->vendor->owner->full_name }},</p>
<p>Your payout has been processed successfully. 💰</p>
<hr class="divider">
<p class="meta">Amount: <strong>{{ $payout->currency }} {{ number_format($payout->net_amount, 2) }}</strong></p>
<p class="meta">Reference: <strong>{{ $payout->reference_id }}</strong></p>
<p class="meta">Date: <strong>{{ $payout->paid_at->format('M d, Y') }}</strong></p>
<hr class="divider">
<p>Funds typically arrive within 1–3 business days depending on your bank.</p>
<a class="btn" href="{{ config('app.url') }}/vendor/payouts/{{ $payout->id }}">View Payout Details</a>
@endsection
