# Description

Simple Laravel Jetstream Inertia.js web app developed to simulate food ordering and delivery with an idea to help other students finish their IT studies

## Features

- Laravel 8.x
- Inertia.js (authentication - Login, Register, Forgot Password, Email Verification)
- Vue
- Vuetify
- Laravel Mix/browserSync

## Installation

Clone repository locally
```bash
git init
git clone https://github.com/IQ-deficient/naruci.me
cd naruci.me
```

Install PHP & NPM Dependencies
```bash
composer install
npm install
```

Configure .env file DB_DATABASE as your local database

Generate application key
```bash
php artisan key:generate
```

Run Database migrations and Seeders
```bash
php artisan migrate:fresh --seed
```

Serve the application in terminal
```bash
php artisan serve
```

Run Laravel Mix to compile the code as:
```bash
npm run dev
```
- OR
```bash
npm run watch
```
to auto-compile for fluid editing

## Additional Info

- configure axios baseURL at root\resources\js\bootstrap.js if needed
