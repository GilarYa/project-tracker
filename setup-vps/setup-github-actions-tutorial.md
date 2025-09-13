# Setup GitHub Actions CI/CD untuk Laravel VPS Deployment

## 🚀 Overview

Tutorial ini menjelaskan cara setup **GitHub Actions** untuk otomatis deploy Laravel ke VPS setiap kali ada push ke branch `main` atau `master`.

## 📋 Fitur CI/CD Pipeline

✅ **Automated Testing** - PHPUnit tests dengan MySQL  
✅ **Asset Building** - Vite build untuk production  
✅ **Zero-downtime Deployment** - Backup dan restore otomatis  
✅ **Health Checks** - Verifikasi aplikasi berjalan dengan baik  
✅ **Rollback Support** - Backup otomatis sebelum deploy  
✅ **Notification** - Status deployment summary  

## 🔧 Setup Instructions

### Step 1: Create Workflow File

1. **Buat folder** `.github/workflows/` di root project Laravel
2. **Create file** `deploy.yml` di dalam folder tersebut
3. **Copy** content dari `github-actions-laravel-deploy.yml`

```bash
mkdir -p .github/workflows
touch .github/workflows/deploy.yml
```

### Step 2: Setup GitHub Secrets

Di **GitHub Repository** → **Settings** → **Secrets and variables** → **Actions**, tambahkan secrets berikut:

#### Required Secrets:

| Secret Name | Description | Example Value |
|-------------|-------------|---------------|
| `VPS_HOST` | IP address VPS | `147.139.241.73` |
| `VPS_USERNAME` | SSH username | `root` |
| `VPS_SSH_KEY` | Private SSH key | `-----BEGIN OPENSSH PRIVATE KEY-----...` |
| `VPS_PORT` | SSH port (optional) | `22` |

### Step 3: Generate SSH Key untuk GitHub Actions

Di **komputer lokal** (bukan VPS):

```bash
# Generate SSH key pair
ssh-keygen -t rsa -b 4096 -C "github-actions@yourdomain.com" -f ~/.ssh/github_actions

# Copy public key
cat ~/.ssh/github_actions.pub
```

**Copy public key** dan tambahkan ke VPS:

```bash
# Di VPS
echo "ssh-rsa AAAAB3NzaC1yc2E... github-actions@yourdomain.com" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

**Copy private key** untuk GitHub Secret:

```bash
# Di komputer lokal
cat ~/.ssh/github_actions
```

Copy seluruh content (termasuk `-----BEGIN` dan `-----END`) ke GitHub Secret `VPS_SSH_KEY`.

### Step 4: Update Laravel untuk Production

#### 4.1: Add Production Environment Variables

Pastikan `.env` di VPS sudah benar:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://vps-laravel.gilarya.my.id

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=root
DB_PASSWORD=MySecurelarya123!

# Cache & Sessions
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

#### 4.2: Add Basic Tests (Optional)

Jika belum ada tests, buat basic test:

```php
<?php
// tests/Feature/BasicTest.php

namespace Tests\Feature;

use Tests\TestCase;

class BasicTest extends TestCase
{
    public function test_application_returns_a_successful_response()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
    
    public function test_database_connection()
    {
        $this->assertTrue(\DB::connection()->getPdo() instanceof \PDO);
    }
}
```

### Step 5: Workflow Explanation

#### 5.1: Test Job

```yaml
test:
  runs-on: ubuntu-latest
  services:
    mysql:
      image: mysql:8.0
      # MySQL service untuk testing
```

**Yang dilakukan:**
- Setup PHP 8.2 dan Node.js 18
- Install dependencies
- Run PHPUnit tests
- Build assets

#### 5.2: Deploy Job

```yaml
deploy:
  needs: test  # Hanya jalan jika test berhasil
  if: github.ref == 'refs/heads/main'  # Hanya untuk branch main
```

**Yang dilakukan:**
- Build production assets
- Create deployment archive
- Upload ke VPS
- Backup aplikasi lama
- Extract files baru
- Set permissions
- Run Laravel optimizations
- Restart services
- Health check

### Step 6: Test Deployment

#### 6.1: First Deployment

1. **Commit** workflow file:
```bash
git add .github/workflows/deploy.yml
git commit -m "Add GitHub Actions CI/CD pipeline"
git push origin main
```

2. **Check** GitHub Actions tab di repository
3. **Monitor** deployment progress
4. **Verify** aplikasi di `http://vps-laravel.gilarya.my.id/`

#### 6.2: Test Automatic Deployment

1. **Make changes** ke Laravel code
2. **Commit dan push**:
```bash
git add .
git commit -m "Test automatic deployment"
git push origin main
```

3. **Watch** GitHub Actions automatically deploy

## 🔍 Monitoring & Debugging

### Check Deployment Logs

**GitHub Actions Logs:**
- Go to **Actions** tab di GitHub repository
- Click pada workflow run
- Expand job steps untuk detail logs

**VPS Logs:**
```bash
# Check Nginx logs
sudo tail -f /var/log/nginx/error.log

# Check PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log

# Check Laravel logs
sudo tail -f /var/www/laravel-app/storage/logs/laravel.log
```

### Common Issues & Solutions

#### Issue 1: SSH Connection Failed

**Error:** `ssh: connect to host X.X.X.X port 22: Connection refused`

**Solution:**
```bash
# Check SSH service di VPS
sudo systemctl status ssh
sudo systemctl start ssh

# Check firewall
sudo ufw allow ssh
```

#### Issue 2: Permission Denied

**Error:** `Permission denied (publickey)`

**Solution:**
- Verify SSH key di GitHub Secrets
- Check authorized_keys di VPS
- Test SSH connection manual

#### Issue 3: Deployment Failed

**Error:** Various deployment errors

**Solution:**
```bash
# Check VPS disk space
df -h

# Check services status
sudo systemctl status nginx php8.2-fpm mysql

# Manual rollback jika perlu
cd /var/backups/laravel-deploy
sudo tar -xzf backup_YYYYMMDD_HHMMSS.tar.gz -C /var/www/
```

## 🛡️ Security Best Practices

### 1. SSH Key Security

- **Gunakan dedicated SSH key** untuk GitHub Actions
- **Restrict permissions** di authorized_keys
- **Regular rotation** SSH keys

### 2. Environment Variables

- **Never commit** `.env` file
- **Use GitHub Secrets** untuk sensitive data
- **Separate** production dan staging environments

### 3. Backup Strategy

- **Automatic backups** sebelum deployment
- **Keep multiple backup versions**
- **Test restore process** secara berkala

## 📈 Advanced Features

### 1. Slack Notifications

Tambahkan di workflow:

```yaml
- name: Slack Notification
  uses: 8398a7/action-slack@v3
  with:
    status: ${{ job.status }}
    channel: '#deployments'
    webhook_url: ${{ secrets.SLACK_WEBHOOK }}
```

### 2. Multiple Environments

```yaml
deploy-staging:
  if: github.ref == 'refs/heads/develop'
  # Deploy ke staging server

deploy-production:
  if: github.ref == 'refs/heads/main'
  # Deploy ke production server
```

### 3. Database Migrations dengan Approval

```yaml
- name: Run Migrations
  if: contains(github.event.head_commit.message, '[migrate]')
  run: sudo -u www-data php artisan migrate --force
```

## 🎯 Hasil Akhir

Setelah setup selesai, Anda akan memiliki:

✅ **Automatic Testing** - Setiap push akan di-test dulu  
✅ **Zero-downtime Deployment** - Deploy tanpa downtime  
✅ **Automatic Rollback** - Backup otomatis untuk rollback  
✅ **Health Monitoring** - Verifikasi aplikasi berjalan  
✅ **Notification System** - Status deployment summary  

### Workflow Trigger:

- **Push ke `main`** → Test + Deploy ke production
- **Pull Request** → Test only
- **Manual trigger** → Available di GitHub Actions tab

**Happy CI/CD! 🚀**
