# Konfigurimi i Njoftimeve me Email për Carpentry App

Ky dokument përshkruan hapat e nevojshëm për të konfiguruar sistemin e dërgimit të emaileve për njoftimet e projekteve.

## Konfigurimi i Email-it në Aplikacionin ColiDecor

Ky dokument përshkruan procesin e konfigurimit dhe përdorimit të sistemit të email-it për aplikacionin ColiDecor.

## Konfigurimi i SMTP

Aplikacioni përdor SMTP për dërgimin e email-eve. Konfigurimi bëhet në fajllin `.env` të aplikacionit:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=adresa.juaj@gmail.com
MAIL_PASSWORD=fjalëkalimi_i_aplikacionit
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=adresa.juaj@gmail.com
MAIL_FROM_NAME="ColiDecor"
```

### Përdorimi i Gmail për SMTP

Nëse përdorni Gmail, duhet të ndiqni këto hapa:

1. Aktivizoni verifikimin me dy faktorë për llogarinë tuaj Google
2. Krijoni një fjalëkalim aplikacioni në [https://myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)
3. Përdorni këtë fjalëkalim në konfigurimin e SMTP

## Testimi i Konfigurimit

Për të testuar konfigurimin e email-it, mund të përdorni skriptin e testimit:

```bash
php test-email.php
```

Ky skript do të dërgojë një email testimi dhe do të shfaqë rezultatin.

Për të testuar dërgimin e emailit me të dhëna projekti:

```bash
php test-projekt-email.php
```

## Sistemi i Njoftimeve me Email

### Funksionaliteti i Njoftimeve

Aplikacioni dërgon njoftime me email në këto raste:

1. **Krijimi i një Projekti të Ri**
   - Dërgohen emaile për menaxherët, mjeshtrat e caktuar dhe montuesit
   - Emaili përmban detajet e projektit dhe dokumentet e bashkangjitura
   - Përfshihet një link direkt për të parë projektin në sistem

2. **Ndryshime të Rëndësishme në Projekt** (planifikuar për të ardhmen)
   - Ndryshime në afate
   - Ndryshime në materiale
   - Ndryshime në status
## Konfigurimi në Server

Kur të instaloni aplikacionin në VPS, sigurohuni që:

1. Konfigurimi i SMTP është i saktë në skedarin `.env`
2. PHP ka të aktivizuar ekstensionetin `fileinfo` për të trajtuar skedarët e bashkangjitur
3. Serveri ka të drejta leximi për dosjen e dokumenteve të projektit

## Zgjidhja e Problemeve

Nëse emailet nuk dërgohen, kontrolloni:

1. Log-et e aplikacionit në `storage/logs/laravel.log`
2. Konfigurimin e SMTP në `.env`
3. Firewall-in e serverit që mund të bllokojë portin e SMTP
4. Kufizimet e ofruesit të emailit (p.sh. limitet e dërgimit ditor)
