<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .container {
            background-color: #f9fafb;
            padding: 40px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .success-icon {
            font-size: 48px;
            color: #10b981;
            margin-bottom: 20px;
        }
        h1 {
            color: #1f2937;
            margin-bottom: 20px;
        }
        p {
            color: #6b7280;
            margin-bottom: 15px;
        }
        .back-link {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        .back-link:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✓</div>
        <h1>Successfully Unsubscribed</h1>
        <p>Hello {{ $user->name }},</p>
        <p>You have been successfully unsubscribed from all email communications from {{ config('app.name') }}.</p>
        <p>You will no longer receive promotional emails, newsletters, or announcements from us.</p>
        <p>If you change your mind, you can always update your email preferences by logging into your account.</p>
        
        <a href="{{ route('login') }}" class="back-link">Back to {{ config('app.name') }}</a>
    </div>
</body>
</html>
