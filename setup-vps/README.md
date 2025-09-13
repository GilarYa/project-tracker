# 🚀 Laravel VPS Ubuntu Deployment

Laravel application dengan manual setup VPS + GitHub Actions untuk incremental updates.

## 📋 Features

- 🛠️ **Manual VPS Setup** - Full control over server configuration
- 📁 **FileZilla Upload** - Manual file transfer untuk initial deployment
- ⚡ **Smart Updates** - GitHub Actions hanya sync file yang berubah
- 🔧 **Manual Migration** - Database migration manual untuk keamanan
- 🎯 **Incremental Sync** - Seperti cpanel, hanya update yang diperlukan

## 🚀 Quick Start

### 1. Setup VPS Ubuntu
Manual setup server dengan PHP 8.2, MySQL, Nginx

### 2. Upload via FileZilla
Transfer file Laravel ke `/var/www/laravel-app/`

### 3. Configure GitHub Secrets
- `VPS_HOST`: `123.123.123.12`
- `VPS_USERNAME`: `root`
- `VPS_PASSWORD`: `gatauu`

### 4. Push for Updates
Push ke `main` branch untuk sync incremental otomatis!

## 📚 Complete Guide

**📖 [VPS Setup & Deployment Guide](VPS-SETUP-GUIDE.md)**

Panduan lengkap dari setup VPS sampai deployment:
- Setup VPS Ubuntu Server
- Install PHP, MySQL, Nginx
- Configure Database
- Upload files via FileZilla
- Setup GitHub Actions
- Daily operations

## 🔄 How It Works

### Initial Setup (Manual)
1. **Setup VPS** - Install semua dependencies
2. **Upload Files** - Via FileZilla ke server
3. **Configure** - Database, .env, permissions

### Updates (Automated)
1. **Push code** ke main branch
2. **GitHub Actions** sync hanya file yang berubah:
   - `app/` - Application code
   - `config/` - Configuration
   - `resources/` - Views & assets
   - `routes/` - Routes
   - `database/migrations/` - Migrations
3. **Manual migration** jika diperlukan

## 🎯 What Gets Updated

✅ **Synced by GitHub Actions:**
- Application code (`app/`)
- Configuration (`config/`)
- Views & assets (`resources/`)
- Routes (`routes/`)
- Migrations (`database/migrations/`)

🔒 **Stays on Server:**
- Environment file (`.env`)
- Vendor dependencies (`vendor/`)
- Logs (`storage/logs/`)
- User uploads (`storage/app/`)

## 🏗️ Architecture

```
Local Development
       ↓ (FileZilla - Initial)
VPS Ubuntu Server (/var/www/laravel-app/)
       ↑ (GitHub Actions - Updates)
GitHub Repository
```

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

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

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
