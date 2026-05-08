<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? config('app.name') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f4f4f5; color: #18181b; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
        .header  { background: #18181b; padding: 32px 40px; text-align: center; }
        .header  a { color: #fff; text-decoration: none; font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
        .body    { padding: 40px; }
        .greeting { font-size: 20px; font-weight: 600; margin-bottom: 16px; }
        p        { font-size: 15px; line-height: 1.7; color: #3f3f46; margin-bottom: 16px; }
        .btn     { display: inline-block; margin: 8px 0 24px; padding: 14px 28px; background: #18181b; color: #fff !important; text-decoration: none; border-radius: 8px; font-size: 15px; font-weight: 600; }
        .divider { border: none; border-top: 1px solid #e4e4e7; margin: 24px 0; }
        .meta    { font-size: 13px; color: #71717a; }
        .footer  { background: #fafafa; padding: 24px 40px; text-align: center; font-size: 13px; color: #71717a; border-top: 1px solid #e4e4e7; }
        .footer a { color: #71717a; }
        table.order-items { width: 100%; border-collapse: collapse; margin: 16px 0; }
        table.order-items th { text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; color: #71717a; padding: 8px 0; border-bottom: 1px solid #e4e4e7; }
        table.order-items td { padding: 12px 0; font-size: 14px; border-bottom: 1px solid #f4f4f5; }
        .badge   { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #f4f4f5; color: #3f3f46; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>
    </div>
    <div class="body">
        @yield('content')
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p><a href="{{ config('app.url') }}/privacy">Privacy</a> &middot; <a href="{{ config('app.url') }}/terms">Terms</a> &middot; <a href="{{ config('app.url') }}/unsubscribe">Unsubscribe</a></p>
    </div>
</div>
</body>
</html>
