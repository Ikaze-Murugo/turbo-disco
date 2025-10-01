# 📧 SMTP Configuration Guide for Murugo Property Platform

## 🚀 **Production SMTP Providers**

### **Option 1: Gmail SMTP (Recommended for Development/Testing)**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="Murugo Property Platform"
```

**Setup Steps:**
1. Enable 2-Factor Authentication on your Gmail account
2. Generate an App Password: Google Account → Security → App passwords
3. Use the App Password (not your regular password)

### **Option 2: SendGrid (Recommended for Production)**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@murugo.com"
MAIL_FROM_NAME="Murugo Property Platform"
```

**Setup Steps:**
1. Sign up at [SendGrid](https://sendgrid.com/)
2. Create an API Key
3. Verify your domain
4. Use the API key as password

### **Option 3: Mailgun (Great for Production)**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-mailgun-username
MAIL_PASSWORD=your-mailgun-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Murugo Property Platform"
```

### **Option 4: AWS SES (Enterprise)**

```env
MAIL_MAILER=smtp
MAIL_HOST=email-smtp.us-east-1.amazonaws.com
MAIL_PORT=587
MAIL_USERNAME=your-ses-smtp-username
MAIL_PASSWORD=your-ses-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Murugo Property Platform"
```

## 🔧 **For Rwanda - Local Email Providers**

### **MTN Business Email**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mtn.co.rw
MAIL_PORT=587
MAIL_USERNAME=your-email@mtn.co.rw
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@murugo.com"
MAIL_FROM_NAME="Murugo Property Platform"
```

### **Airtel Business Email**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.airtel.co.rw
MAIL_PORT=587
MAIL_USERNAME=your-email@airtel.co.rw
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@murugo.com"
MAIL_FROM_NAME="Murugo Property Platform"
```

## ⚙️ **Configuration Steps**

1. **Choose your provider** based on your needs
2. **Update the .env file** with the appropriate settings
3. **Test the configuration** using the test script
4. **Monitor email delivery** in production

## 🧪 **Testing Your Configuration**

After updating your .env file, run:
```bash
php artisan config:clear
php artisan tinker
```

Then test:
```php
Mail::raw('Test email', function($message) {
    $message->to('test@example.com')->subject('Test Email');
});
```

## 📊 **Production Considerations**

- **Rate Limits**: Most providers have sending limits
- **Deliverability**: Use verified domains for better delivery
- **Monitoring**: Set up email delivery monitoring
- **Backup**: Have a backup email provider ready
- **Compliance**: Ensure GDPR compliance for EU users

## 🔒 **Security Best Practices**

- Never commit real credentials to version control
- Use environment variables for all sensitive data
- Rotate API keys regularly
- Monitor for unusual sending patterns
- Use SPF, DKIM, and DMARC records for your domain
