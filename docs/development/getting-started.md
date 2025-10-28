# Development Guide

## Local Development Setup

1. **Clone the repository**
   ```bash
   git clone [repository-url]
   cd carpentry-app
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Setup**
   ```bash
   php artisan migrate --seed
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   npm run dev
   ```

## Coding Standards

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) for PHP code
- Use Laravel's naming conventions
- Write tests for new features
- Document complex logic with PHPDoc

## Git Workflow

1. Create a new branch for your feature
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. Make your changes and commit with a descriptive message
   ```bash
   git add .
   git commit -m "Add feature description"
   ```

3. Push your changes and create a pull request
   ```bash
   git push origin feature/your-feature-name
   ```

## Testing

Run the test suite:
```bash
php artisan test
```

## Debugging

- Use `dd()`, `dump()`, or `logger()` for debugging
- Enable debug mode in `.env`:
  ```
  APP_DEBUG=true
  ```

## Database Management

To create a new migration:
```bash
php artisan make:migration create_table_name_table
```

To run migrations:
```bash
php artisan migrate
```

To rollback the last migration:
```bash
php artisan migrate:rollback
```
