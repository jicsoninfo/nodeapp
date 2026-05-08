@extends('layouts.email')
@section('content')
<p class="greeting">Congratulations, {{ $vendor->owner->full_name }}! 🎉</p>
<p>Your store <strong>{{ $vendor->store_name }}</strong> has been approved and is now live on the marketplace.</p>
<hr class="divider">
<p><strong>What to do next:</strong></p>
<p>1. Complete your store profile and add your logo</p>
<p>2. List your first products</p>
<p>3. Set up your return and shipping policies</p>
<p>4. Add your bank account for payouts</p>
<hr class="divider">
<a class="btn" href="{{ config('app.url') }}/vendor/dashboard">Go to Vendor Dashboard</a>
<p>Welcome to the marketplace family. We're thrilled to have you!</p>
@endsection
