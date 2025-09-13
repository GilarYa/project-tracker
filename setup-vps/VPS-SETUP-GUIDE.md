# 🚀 Laravel VPS Ubuntu Setup & Deployment Guide

Panduan lengkap setup VPS Ubuntu untuk Laravel dengan deployment manual + GitHub Actions untuk update incremental.

## 📋 Overview

- **Manual Setup**: VPS Ubuntu, Database, File Transfer via FileZilla
- **GitHub Actions**: Hanya untuk update incremental (seperti cpanel)
- **File Transfer**: Manual via FileZilla (sudah terkoneksi)
- **Database**: Manual migration dari VPS

## 🖥️ VPS Information
- **IP**: 123.123.123.12
- **User**: root
- **Password**: gatauu
- **SSH Fingerprint**: SHA256:Xv2IZHnSQowk8t4tmOWET+qbh8Q30dE6viciGpcVNkk

---

## 🛠️ STEP 1: Setup VPS Ubuntu Server

### 1.1 Connect to VPS
```bash
ssh root@123.123.123.12
```

### 1.2 Update System
```bash
apt update && apt upgrade -y
```

**Jika muncul dialog "Daemons using outdated libraries":**
```
┌─────────────────────────────────────────────────┐
│ Which services should be restarted?             │
│                                                 │
│    [*] chrony.service                          │
│    [*] packagekit.service                      │
│    [*] systemd-resolved.service                │
│                                                 │
│          <Ok>              <Cancel>            │
└─────────────────────────────────────────────────┘
```

**Solusi:**
- **Tekan `Tab`** untuk navigasi ke tombol `<Ok>`
- **Tekan `Enter`** untuk restart services yang dipilih
- **Atau tekan `Escape`** untuk skip (aman untuk diabaikan)

**Alternatif - Skip dialog otomatis:**
```bash
# Set environment variable untuk skip dialog
export DEBIAN_FRONTEND=noninteractive
apt update && apt upgrade -y
```

**Penjelasan:**
- Dialog ini muncul karena ada services yang menggunakan library lama
- **Aman untuk di-restart** - services akan otomatis reload dengan library terbaru
- **Aman untuk di-skip** - services akan restart saat server reboot nanti
- Ini adalah fitur keamanan Ubuntu untuk memastikan services menggunakan library terbaru

### 1.3 Install PHP 8.2
```bash
# Add PHP repository
add-apt-repository ppa:ondrej/php -y
apt update

# Install PHP and extensions
apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl php8.2-dom php8.2-fileinfo
```

### 1.4 Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
```

### 1.5 Install MySQL
```bash
apt install -y mysql-server
mysql_secure_installation
```

**Dialog yang akan muncul dan pilihan yang disarankan:**

**1. VALIDATE PASSWORD COMPONENT:**
```
VALIDATE PASSWORD COMPONENT can be used to test passwords
and improve security. It checks the strength of password
and allows the users to set only those passwords which are
secure enough. Would you like to setup VALIDATE PASSWORD component?

Press y|Y for Yes, any other key for No:
```
**Pilih: `n` (No)** - Untuk kemudahan, kita akan set password manual

**2. Set root password:**
```
Skipping password set for root as authentication with auth_socket is used by default.
If you would like to use password authentication instead, this can be done with the "ALTER_USER" command.
```
**Info:** MySQL 8.0 menggunakan `auth_socket` untuk root (login tanpa password sebagai sudo)

**3. Remove anonymous users:**
```
Remove anonymous users? (Press y|Y for Yes, any other key for No) :
```
**Pilih: `y` (Yes)** - Hapus user anonymous untuk keamanan

**4. Disallow root login remotely:**
```
Disallow root login remotely? (Press y|Y for Yes, any other key for No) :
```
**Pilih: `y` (Yes)** - Blokir root login dari remote untuk keamanan

**5. Remove test database:**
```
Remove test database and access to it? (Press y|Y for Yes, any other key for No) :
```
**Pilih: `y` (Yes)** - Hapus database test yang tidak diperlukan

**6. Reload privilege tables:**
```
Reload privilege tables now? (Press y|Y for Yes, any other key for No) :
```
**Pilih: `y` (Yes)** - Reload untuk apply semua perubahan

### 1.5.1 Cek MySQL Users & Login
**Login ke MySQL sebagai root:**
```bash
# Cara 1: Menggunakan sudo (auth_socket)
sudo mysql

# Cara 2: Menggunakan mysql command
mysql -u root
```

**Cek users yang ada:**
```sql
SELECT User, Host, authentication_string FROM mysql.user;
```

**⚠️ PENTING:** Di MySQL prompt (`mysql>`), langsung ketik perintah SQL tanpa kata `sql` di depan!

**Output akan menampilkan:**
```
+------------------+-----------+------------------------------------------------------------------------+
| User             | Host      | authentication_string                                                  |
+------------------+-----------+------------------------------------------------------------------------+
| debian-sys-maint | localhost | $A$005$... (encrypted)                                                |
| mysql.infoschema | localhost | $A$005$... (encrypted)                                                |
| mysql.session    | localhost | $A$005$... (encrypted)                                                |
| mysql.sys        | localhost | $A$005$... (encrypted)                                                |
| root             | localhost |                                                                        |
+------------------+-----------+------------------------------------------------------------------------+
```

**Penjelasan:**
- **root user** menggunakan `auth_socket` (authentication_string kosong)
- Login sebagai root hanya bisa dengan `sudo mysql` atau sebagai user root
- Untuk Laravel, kita akan buat user terpisah dengan password

### 1.5.2 (Opsional) Set Password untuk Root User
**Jika ingin menambahkan password untuk root:**
```sql
-- Ganti authentication method dan set password
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'MySecurelarya123!';

-- Flush privileges
FLUSH PRIVILEGES;

-- Cek perubahan
SELECT User, Host, plugin FROM mysql.user WHERE User = 'root';
```

**Setelah set password, login dengan:**
```bash
mysql -u root -p
```

**Atau tetap gunakan auth_socket (rekomendasi):**
- Lebih aman karena hanya bisa login sebagai sudo
- Tidak perlu mengingat password
- Login dengan: `sudo mysql`

### 1.5.3 Apa itu auth_socket?

**auth_socket** adalah metode authentication MySQL yang menggunakan sistem operasi untuk verifikasi user.

**Cara kerja:**
1. **Cek user sistem** - MySQL cek siapa yang login ke sistem operasi
2. **Match dengan MySQL user** - Jika user sistem = MySQL user, langsung login
3. **Tidak perlu password** - Authentication dilakukan oleh OS, bukan MySQL

**Contoh:**
```bash
# User 'root' di sistem operasi
whoami
# Output: root

# Login MySQL sebagai root (tanpa password)
mysql
# MySQL cek: "User sistem = root, MySQL user = root" → Login berhasil
```

**Keuntungan auth_socket:**
- ✅ **Lebih aman** - Tidak ada password yang bisa dicuri
- ✅ **Tidak bisa brute force** - Harus akses sistem dulu
- ✅ **Otomatis** - Tidak perlu ketik password
- ✅ **Audit trail** - Tahu siapa yang login dari sistem

**Kekurangan:**
- ❌ **Hanya local** - Tidak bisa remote login
- ❌ **Perlu sudo** - Harus jadi user root sistem

**Perbandingan:**

| Method | Login Command | Security | Remote Access |
|--------|---------------|----------|---------------|
| auth_socket | `sudo mysql` | ⭐⭐⭐⭐⭐ | ❌ No |
| password | `mysql -u root -p` | ⭐⭐⭐ | ✅ Yes |

**Untuk production server:** auth_socket lebih aman karena attacker harus hack sistem operasi dulu sebelum bisa akses MySQL.

### 1.6 Install Nginx
```bash
apt install -y nginx
systemctl start nginx
systemctl enable nginx
```

**✅ Nginx mendukung:**
- 🌐 **Multiple Laravel apps** - Bisa host banyak aplikasi Laravel
- 🔗 **Multiple domains** - Setiap domain bisa point ke Laravel berbeda
- ☁️ **Cloudflare integration** - Domain dari Cloudflare bisa point ke IP server ini
- 🔒 **SSL certificates** - Support Let's Encrypt untuk HTTPS

### 1.7 Create Application Directory
```bash
mkdir -p /var/www/laravel-app
chown -R www-data:www-data /var/www/laravel-app
chmod -R 755 /var/www/laravel-app
```

### 1.8 Configure Nginx

#### 1.8.1 Single Laravel App (Basic Setup)
```bash
nano /etc/nginx/sites-available/laravel-app
```

Isi dengan:
```nginx
server {
    listen 60001;
    server_name 123.123.123.12 yourdomain.com www.yourdomain.com;
    root /var/www/laravel-app/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### 1.8.2 Aktifkan Site
```bash
ln -s /etc/nginx/sites-available/laravel-app /etc/nginx/sites-enabled/
rm /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx
```

#### 1.8.3 Cloudflare Setup (Opsional)

**Di Cloudflare Dashboard:**
1. **Add A Record:**
   - Type: `A`
   - Name: `@` (untuk root domain) atau `app1` (untuk subdomain)
   - Content: `123.123.123.12`
   - TTL: `Auto`
   - Proxy status: `Proxied` (orange cloud)

2. **Add CNAME Record (untuk www):**
   - Type: `CNAME`
   - Name: `www`
   - Content: `yourdomain.com`
   - TTL: `Auto`
   - Proxy status: `Proxied`

**Contoh DNS Records:**
```
Type    Name    Content              Proxy
A       @       123.123.123.12       Proxied
CNAME   www     yourdomain.com       Proxied
```

**Cloudflare Benefits:**
- 🔒 **Free SSL** - Otomatis HTTPS
- 🚀 **CDN** - Website lebih cepat
- 🛡️ **DDoS Protection** - Keamanan tambahan
- 📊 **Analytics** - Traffic monitoring

---

## 🗄️ STEP 2: Setup Database

### 2.1 Login to MySQL
```bash
mysql -u root -p
```

### 2.2 Create Database & User
```sql
CREATE DATABASE laravel_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'laravel_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON laravel_production.* TO 'laravel_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 📁 STEP 3: Upload Laravel Files via FileZilla

### 3.1 FileZilla Connection (sudah terkoneksi)
- **Host**: 123.123.123.12
- **Username**: root
- **Password**: gatauu
- **Port**: 22 (SFTP)

### 3.2 Upload Files
Upload semua file Laravel ke `/var/www/laravel-app/`

**Exclude files ini** (jangan upload):
- `node_modules/`
- `.env` (akan dibuat manual)
- `vendor/` (akan diinstall via composer)
- `storage/logs/*`
- `storage/framework/cache/*`
- `storage/framework/sessions/*`
- `storage/framework/views/*`

### 3.3 Set Permissions
```bash
chown -R www-data:www-data /var/www/laravel-app
chmod -R 755 /var/www/laravel-app
chmod -R 775 /var/www/laravel-app/storage
chmod -R 775 /var/www/laravel-app/bootstrap/cache
```

---

## ⚙️ STEP 4: Configure Laravel

### 4.1 Install Dependencies
```bash
cd /var/www/laravel-app
sudo -u www-data composer install --no-dev --optimize-autoloader
```

### 4.2 Create .env File
```bash
cp .env.example .env
nano .env
```

Configure .env:
```env
APP_NAME="Laravel App"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://123.123.123.12

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_production
DB_USERNAME=laravel_user
DB_PASSWORD=your_secure_password

CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### 4.3 Generate App Key
```bash
sudo -u www-data php artisan key:generate
```

### 4.4 Run Migrations (Manual)
```bash
sudo -u www-data php artisan migrate
```

### 4.5 Cache Configuration
```bash
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

---

## 🔧 STEP 5: Setup GitHub Actions

### 5.1 GitHub Secrets
Buka repository → Settings → Secrets and variables → Actions

Tambahkan secrets:
- `VPS_HOST`: `123.123.123.12`
- `VPS_USERNAME`: `root`
- `VPS_PASSWORD`: `gatauu`

### 5.2 Workflow Explanation
File `.github/workflows/deploy.yml` akan:

**Incremental Mode (default):**
- Sync hanya file yang berubah
- Folder: `app/`, `config/`, `resources/`, `routes/`, `database/migrations/`
- Exclude: `vendor/`, `.env`, `storage/logs/`
- Clear & cache Laravel configs

**Full Mode (manual trigger):**
- Sync semua file Laravel
- Gunakan saat ada perubahan besar

### 5.3 Deployment Process
1. **Push ke main/master** → Otomatis incremental update
2. **Manual trigger** → Pilih full atau incremental

---

## 🔄 STEP 6: Daily Operations

### 6.1 Update Code (GitHub Actions)
```bash
# Push changes
git add .
git commit -m "Update feature"
git push origin main
```
GitHub Actions akan otomatis sync file yang berubah.

### 6.2 Manual Migration (di VPS)
```bash
cd /var/www/laravel-app
sudo -u www-data php artisan migrate
```

### 6.3 Clear Cache (jika diperlukan)
```bash
cd /var/www/laravel-app
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan route:clear
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

---

## 📊 Monitoring & Troubleshooting

### Check Logs
```bash
# Laravel logs
tail -f /var/www/laravel-app/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

### Check Services
```bash
systemctl status nginx
systemctl status php8.2-fpm
systemctl status mysql
```

### Test Application
```bash
curl -I http://123.123.123.12
```

---

## ✅ Deployment Summary

### What GitHub Actions Updates:
- ✅ `app/` - Application logic
- ✅ `config/` - Configuration files
- ✅ `resources/` - Views, assets, lang
- ✅ `routes/` - Route definitions
- ✅ `database/migrations/` - Database migrations
- ✅ `public/` - Public assets (excluding server files)

### What Stays on Server:
- 🔒 `.env` - Environment configuration
- 🔒 `vendor/` - Composer dependencies
- 🔒 `storage/logs/` - Application logs
- 🔒 `storage/framework/` - Framework cache

### Manual Tasks:
- 🔧 Database migrations: `php artisan migrate`
- 🔧 Composer updates: `composer install`
- 🔧 Environment changes: Edit `.env`

---

## 🎯 Benefits

- ⚡ **Fast Updates**: Hanya sync file yang berubah
- 🔒 **Secure**: Server files tidak terganggu
- 🛡️ **Safe**: Backup otomatis sebelum update
- 🎮 **Control**: Migration manual untuk keamanan
- 📱 **Simple**: FileZilla untuk upload besar, GitHub untuk update kecil

**Setup selesai!** Aplikasi Laravel siap dengan deployment incremental otomatis. 🎉
