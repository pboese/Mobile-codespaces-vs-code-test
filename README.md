<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Stack

| Package | Version | Purpose |
|---------|---------|---------|
| [Laravel](https://laravel.com) | ^13.8 | Core framework |
| [Laravel Fortify](https://github.com/laravel/fortify) | ^1.24 | Authentication back-end (login, register, password reset, 2FA) |
| [Laravel Passkeys](https://github.com/laravel/passkeys) | ^0.2.1 | WebAuthn / FIDO2 passkey authentication |
| [Livewire](https://livewire.laravel.com) | ^4.0 | Reactive UI components |
| [Wirekit](https://docs.wirekit.app) | ^2.11 | Livewire UI component kit |

## Features

- **Email + password authentication** via Fortify
- **TOTP two-factor authentication** (Google Authenticator, Authy, etc.) via Fortify
- **Passkey authentication** (Face ID, Touch ID, hardware security keys) via the official `laravel/passkeys` package
- **Livewire** reactive components for login, registration and 2FA challenge
- **Wirekit** component library for consistent Livewire UI primitives
- Password reset and email verification-ready

## Local Setup

### Requirements

- PHP 8.4+
- Composer 2.x
- Node 20+ and npm

### Installation

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy and configure environment
cp .env.example .env
php artisan key:generate

# 3. Run database migrations
php artisan migrate

# 4. Install and build front-end assets
npm install
npm run dev
```

### Passkeys (WebAuthn) configuration

Edit `.env` to set the correct relying party values for your environment:

```dotenv
APP_URL=https://your-domain.com
```

The passkeys config (`config/passkeys.php`) derives `relying_party_id` and `allowed_origins` from `APP_URL` by default.  
For local development with `http://localhost`, the defaults work out of the box.

### Two-factor authentication

Users can enable TOTP 2FA from the **Security** page (`/user/two-factor-authentication`):

1. Click **Enable two-factor authentication**
2. Confirm your password when prompted
3. Scan the QR code with an authenticator app
4. Enter a code from the app to confirm setup
5. Save the recovery codes in a safe place

### Passkey management

From the dashboard or the **Security** page, authenticated users can:

- Register new passkeys (biometric or hardware key)
- View and delete existing passkeys

Login with a passkey is available on the login page via the **Sign in with Passkey** button.

## Running Tests

```bash
php artisan test
```

Test suite covers registration, login, 2FA enable/disable/challenge and passkey endpoints.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
