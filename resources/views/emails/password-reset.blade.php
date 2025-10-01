<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - Murugo Property Platform</title>
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
            background: #dc3545;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background: #c82333;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
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
        <h1>Password Reset Request</h1>
        <p>Murugo Property Platform</p>
    </div>

    <div class="content">
        <h2>Hello {{ $user->name }}!</h2>
        
        <p>We received a request to reset your password for your Murugo Property Platform account. If you made this request, click the button below to reset your password:</p>

        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Reset My Password</a>
        </div>

        <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
        <p style="word-break: break-all; background: #e9ecef; padding: 10px; border-radius: 5px; font-family: monospace;">
            {{ $resetUrl }}
        </p>

        <div class="warning">
            <strong>⚠️ Important Security Information:</strong>
            <ul>
                <li>This password reset link will expire in 1 hour for security reasons</li>
                <li>If you didn't request this password reset, please ignore this email</li>
                <li>Your password will remain unchanged until you click the link above</li>
                <li>For your security, never share this link with anyone</li>
            </ul>
        </div>

        <p>If you're having trouble accessing your account or didn't request this reset, please contact our support team immediately.</p>

        <p>Best regards,<br>
        The Murugo Property Platform Security Team</p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Murugo Property Platform. All rights reserved.</p>
        <p>This email was sent to {{ $user->email }}</p>
        <p><strong>Security Notice:</strong> This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>
