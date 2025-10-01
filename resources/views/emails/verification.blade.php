<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - Murugo Property Platform</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background: #5a6fd8;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to Murugo Property Platform!</h1>
        <p>Your trusted partner in finding the perfect property in Rwanda</p>
    </div>

    <div class="content">
        <h2>Hello {{ $user->name }}!</h2>
        
        <p>Thank you for registering with Murugo Property Platform. To complete your registration and start exploring amazing properties in Rwanda, please verify your email address by clicking the button below:</p>

        <div style="text-align: center;">
            <a href="{{ $verificationUrl }}" class="button">Verify Email Address</a>
        </div>

        <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
        <p style="word-break: break-all; background: #e9ecef; padding: 10px; border-radius: 5px; font-family: monospace;">
            {{ $verificationUrl }}
        </p>

        <p><strong>Important:</strong> This verification link will expire in 24 hours for security reasons.</p>

        <p>If you didn't create an account with us, please ignore this email.</p>

        <p>Best regards,<br>
        The Murugo Property Platform Team</p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Murugo Property Platform. All rights reserved.</p>
        <p>This email was sent to {{ $user->email }}</p>
    </div>
</body>
</html>
