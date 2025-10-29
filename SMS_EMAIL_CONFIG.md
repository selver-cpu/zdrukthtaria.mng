# Konfigurimi i Email dhe SMS

## 📧 Email Configuration

Shto këto në `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Gmail Setup:
1. Shko në Google Account Settings
2. Security → 2-Step Verification (aktivizo)
3. App Passwords → Krijo një password të ri
4. Përdor atë password në `MAIL_PASSWORD`

### Alternativa:
- **Mailtrap** (për testing): https://mailtrap.io
- **SendGrid**: https://sendgrid.com
- **Mailgun**: https://www.mailgun.com

---

## 📱 SMS Configuration

Shto këto në `.env` file:

```env
# SMS Provider Settings
SMS_API_URL=https://api.sms-provider.com/send
SMS_API_KEY=your-api-key-here
SMS_SENDER=ColiDecor
```

### SMS Providers për Kosovë/Shqipëri:

#### 1. **Twilio** (Ndërkombëtar)
```env
SMS_API_URL=https://api.twilio.com/2010-04-01/Accounts/YOUR_ACCOUNT_SID/Messages.json
SMS_API_KEY=your-twilio-auth-token
SMS_SENDER=+383xxxxxxxx
```
- Website: https://www.twilio.com
- Çmimi: ~$0.0075 për SMS
- Mbështet: Kosovë, Shqipëri, të gjitha vendet

#### 2. **Vonage (Nexmo)** (Ndërkombëtar)
```env
SMS_API_URL=https://rest.nexmo.com/sms/json
SMS_API_KEY=your-api-key
SMS_SENDER=ColiDecor
```
- Website: https://www.vonage.com
- Çmimi: ~$0.01 për SMS

#### 3. **Provider Lokal (Kosovë/Shqipëri)**
Kontakto operatorët lokalë:
- **IPKO** (Kosovë)
- **Vala** (Kosovë)
- **Vodafone** (Shqipëri)
- **ALBtelecom** (Shqipëri)

---

## 🧪 Testimi

### Test Email:
```bash
php artisan tinker
```
```php
$njoftim = \App\Models\Njoftimet::create([
    'perdorues_id' => 1,
    'mesazhi' => 'Test email notification',
    'lloji_njoftimit' => 'email',
    'lexuar' => false
]);

event(new \App\Events\NjoftimIRi($njoftim));
```

### Test SMS:
```php
$njoftim = \App\Models\Njoftimet::create([
    'perdorues_id' => 1,
    'mesazhi' => 'Test SMS notification',
    'lloji_njoftimit' => 'sms',
    'lexuar' => false
]);

event(new \App\Events\NjoftimIRi($njoftim));
```

---

## 📝 Shënime

1. **Queue**: Për performancë më të mirë, përdor queue:
   ```bash
   php artisan queue:work
   ```

2. **Logs**: Kontrollo logs për debugging:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Testing Mode**: Nëse nuk ke konfiguruar API, njoftimet do të shfaqen vetëm në logs.

4. **Kostot**: SMS ka kosto, testo mirë para se të aktivizosh në production!
