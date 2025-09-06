# Sistemi i Menaxhimit të Procesit të Punës

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)

## 📋 Përshkrim

Ky është një sistem i zhvilluar me Laravel për menaxhimin e projekteve në një biznes druri. Sistemi lejon menaxhimin e klientëve, projekteve, materialeve, dokumenteve dhe gjurmimin e progresit të punës.

## ✨ Veçoritë Kryesore

- **👥 Menaxhimi i Klientëve** - Regjistrimi dhe gjurmimi i të gjithë klientëve
- **📋 Menaxhimi i Projkteve** - Krijimi dhe gjurmimi i projekteve nga fillimi deri në përfundim
- **📊 Gjurmimi i Progresit** - Ndiqni progresin e çdo projekti në kohë reale
- **🛠️ Menaxhimi i Materialeve** - Gjurmimi i materialeve të përdorura në çdo projekt
- **📑 Dokumentacioni** - Ngarkimi dhe menaxhimi i dokumenteve të lidhura me projektet
- **📈 Raporte dhe Statistikat** - Gjenerimi i raporteve dhe statistikave për projektet
- **🔔 Njoftimet** - Dërgimi i njoftimeve për ndryshime në projekte

## 📚 Dokumentacioni

- [Udhëzuesi i Përdoruesit](/docs/guides/user-guide.md) - Udhëzime të hollësishme për përdoruesit
- [API Documentation](/docs/api/overview.md) - Dokumentacioni i plotë i API-ve
- [Udhëzimet për Zhvilluesit](/docs/development/getting-started.md) - Si të filloni zhvillimin
- [Vendosja në Produksion](/docs/deployment/production.md) - Udhëzime për vendosjen në server

## 🚀 Fillimi i Shpejtë

1. **Klononi repozitorinë**
   ```bash
   git clone [repository-url]
   cd carpentry-app
   ```

2. **Instaloni varësitë PHP**
   ```bash
   composer install
   ```

3. **Kopjoni skedarin .env dhe gjeneroni çelësin e aplikacionit**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfiguroni bazën e të dhënave**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=emri_i_bazes_se_te_dhenave
   DB_USERNAME=emri_i_perdoruesit
   DB_PASSWORD=fjalekalimi
   ```

5. **Ekzekutoni migrimet dhe seed-erat**
   ```bash
   php artisan migrate --seed
   ```

6. **Instaloni varësitë JavaScript dhe ndërtoni asetet**
   ```bash
   npm install
   npm run build
   ```

7. **Nisni serverin e zhvillimit**
   ```bash
   php artisan serve
   ```

## 👥 Kredencialet e Paracaktuara

- **Administratori:**
  - Email: admin@example.com
  - Fjalëkalimi: password

- **Menaxheri:**
  - Email: manager@example.com
  - Fjalëkalimi: password

- **Mjeshtri:**
  - Email: mjeshtri@example.com
  - Fjalëkalimi: password

- **Montuesi:**
  - Email: montuesi@example.com
  - Fjalëkalimi: password

## 🔒 Siguria

- Të gjitha fjalëkalimet ruhen të hash-ura
- Mbrojtje CSRF e aktivizuar
- Mbrojtje XSS e aktivizuar
- Validimi i të dhënave në të gjitha format

## 🤝 Kontributori

[Emri Juaj]

## 📄 Licenca

Ky projekt është i licencuar nën licencën MIT - shikoni skedarin [LICENSE](LICENSE) për detaje.

## Kërkesat e Sistemit

- PHP 8.1 ose më i lartë
- Composer 2.0 ose më i lartë
- MySQL 5.7+ ose MariaDB 10.3+
- Node.js 16+ dhe NPM 8+
- Web server (Apache/Nginx)

## Instalimi

1. Klononi repozitorinë
   ```bash
   git clone [URL-e e repozitorisë]
   cd carpentry-app
   ```

2. Instaloni varësitë e PHP
   ```bash
   composer install
   ```

3. Krijo një kopje të skedarit .env dhe përditësoni konfigurimet e bazës së të dhënave
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Përditësoni konfigurimet në skedarin .env
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=emri_i_bazes_se_te_dhenave
   DB_USERNAME=emri_i_perdoruesit
   DB_PASSWORD=fjalekalimi
   ```

5. Ekzekutoni migrimet dhe seed-erat
   ```bash
   php artisan migrate --seed
   ```

6. Instaloni varësitë e JavaScript dhe ndërtoni asetet
   ```bash
   npm install
   npm run build
   ```

7. Nisni serverin e zhvillimit
   ```bash
   php artisan serve
   ```

## Përdorimi

1. Hyni në sistem me kredencialet e mëposhtme:
   - **Administratori:**
     - Email: admin@example.com
     - Fjalëkalimi: password
   - **Menaxheri:**
     - Email: manager@example.com
     - Fjalëkalimi: password
   - **Mjeshtri:**
     - Email: mjeshtri@example.com
     - Fjalëkalimi: password
   - **Montuesi:**
     - Email: montuesi@example.com
     - Fjalëkalimi: password

## Dokumentacioni i API-ve

Dokumentacioni i plotë i API-ve është i disponueshëm në rrugën `/api/documentation` pasi të keni ndezur serverin e zhvillimit.

## Siguria

- Të gjitha fjalëkalimet ruhen të hash-ura
- CSRF Protection aktivizuar
- XSS Protection aktivizuar
- Validimi i të dhënave në të gjitha format

## Kontributori

- [Emri Juaj]

## Licenca

Ky projekt është i licencuar nën licencën MIT - shikoni skedarin [LICENSE](LICENSE) për detaje."

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
