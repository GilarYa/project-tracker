# Tutorial: Deploy Laravel ke VPS dengan Cloudflare Worker sebagai Proxy

## 📋 Overview

Tutorial ini menjelaskan cara deploy aplikasi Laravel ke VPS dan menggunakan Cloudflare Worker sebagai proxy untuk mengakses aplikasi melalui custom domain.

### 🎯 Hasil Akhir
- Laravel app running di VPS (port 60001)
- Cloudflare Worker sebagai proxy
- Custom domain: `vps-laravel.gilarya.my.id`
- HTTP setup (tanpa SSL complications)

## 🛠️ Prerequisites

- VPS dengan Ubuntu/Debian
- Domain yang sudah di-manage oleh Cloudflare
- Akun Cloudflare dengan akses ke Workers
- Basic knowledge Laravel dan server management

## 📦 STEP 1: Setup Laravel di VPS

### 1.1 Install Dependencies

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2 dan extensions
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-zip php8.2-mbstring php8.2-gd

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo apt install -y nginx

# Install Node.js 18+ (untuk Vite)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### 1.2 Setup Laravel Project

```bash
# Clone atau create Laravel project
cd /var/www
sudo git clone https://github.com/your-repo/laravel-app.git
# ATAU
sudo composer create-project laravel/laravel laravel-app

# Set permissions
sudo chown -R www-data:www-data /var/www/laravel-app
sudo chmod -R 755 /var/www/laravel-app
sudo chmod -R 775 /var/www/laravel-app/storage
sudo chmod -R 775 /var/www/laravel-app/bootstrap/cache
```

### 1.3 Configure Laravel

```bash
cd /var/www/laravel-app

# Copy environment file
sudo cp .env.example .env

# Generate app key
sudo -u www-data php artisan key:generate

# Edit .env file
sudo nano .env
```

**Update .env:**
```env
APP_NAME="Laravel VPS"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://vps-laravel.gilarya.my.id

# Database settings (sesuaikan dengan setup Anda)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=your_password
```

### 1.4 Build Assets dengan Vite

```bash
# Install Node dependencies
npm install

# Build assets untuk production
npm run build

# Verify assets
ls -la public/build/assets/
```

## 🌐 STEP 2: Configure Nginx

### 2.1 Create Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/laravel-app
```

**Nginx config:**
```nginx
server {
    listen 60001;
    server_name backend.gilarya.my.id;
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

### 2.2 Enable Site

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/laravel-app /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm

# Enable auto-start
sudo systemctl enable nginx
sudo systemctl enable php8.2-fpm
```

### 2.3 Test Laravel

```bash
# Test local access
curl http://localhost:60001

# Check if Laravel welcome page loads
curl -I http://backend.gilarya.my.id:60001
```

## ☁️ STEP 3: Setup Cloudflare Worker

### 3.1 Create Worker

1. Login ke **Cloudflare Dashboard**
2. Pilih domain Anda
3. **Workers Routes** → **Manage Workers**
4. **Create a Worker**
5. Beri nama: `vpstestcicd-mountain-b8b1` (atau nama lain)

### 3.2 Worker Code

```javascript
addEventListener('fetch', event => {
  event.respondWith(handleRequest(event.request))
})

async function handleRequest(request) {
  const targetHost = 'backend.gilarya.my.id'
  const targetPort = '60001'
  
  const url = new URL(request.url)
  
  // Force HTTP redirect if HTTPS is accessed
  if (url.protocol === 'https:') {
    const httpUrl = `http://${url.host}${url.pathname}${url.search}`
    return Response.redirect(httpUrl, 301)
  }
  
  // Build complete target URL
  const targetUrl = `http://${targetHost}:${targetPort}${url.pathname}${url.search}`
  
  console.log(`Proxying: ${request.url} -> ${targetUrl}`)
  
  try {
    // Create new request with minimal headers
    const proxyRequest = new Request(targetUrl, {
      method: request.method,
      headers: {
        'Host': `${targetHost}:${targetPort}`,
        'User-Agent': request.headers.get('User-Agent') || 'Cloudflare-Worker',
        'Accept': request.headers.get('Accept') || '*/*',
        'Accept-Language': request.headers.get('Accept-Language') || 'en-US,en;q=0.9',
        'Cache-Control': request.headers.get('Cache-Control') || 'no-cache'
      },
      body: request.method !== 'GET' && request.method !== 'HEAD' ? request.body : null
    })
    
    const response = await fetch(proxyRequest)
    
    // Create response with proper headers for HTTP
    const newHeaders = new Headers(response.headers)
    
    // Remove any HTTPS-related headers
    newHeaders.delete('strict-transport-security')
    newHeaders.delete('content-security-policy')
    
    // Set proper content type for assets
    if (url.pathname.endsWith('.css')) {
      newHeaders.set('Content-Type', 'text/css')
    } else if (url.pathname.endsWith('.js')) {
      newHeaders.set('Content-Type', 'application/javascript')
    }
    
    // Add CORS headers
    newHeaders.set('Access-Control-Allow-Origin', '*')
    
    return new Response(response.body, {
      status: response.status,
      statusText: response.statusText,
      headers: newHeaders
    })
    
  } catch (error) {
    console.error('Proxy error:', error)
    return new Response(`Proxy Error: ${error.message}\nTarget: ${targetUrl}`, { 
      status: 502,
      headers: { 'Content-Type': 'text/plain' }
    })
  }
}
```

### 3.3 Deploy Worker

1. **Paste** code di atas ke Worker editor
2. **Save and Deploy**
3. **Test** Worker URL: `https://vpstestcicd-mountain-b8b1.largwa22.workers.dev`

## 🔗 STEP 4: Setup Custom Domain

### 4.1 Add Custom Domain to Worker

1. **Worker Settings** → **Triggers**
2. **Add Custom Domain**
3. **Domain:** `vps-laravel.gilarya.my.id`
4. **Add Domain**

### 4.2 Configure DNS (jika diperlukan)

Jika ada konflik DNS:
1. **DNS** → **Records**
2. **Delete** existing A record untuk `vps-laravel`
3. Cloudflare akan auto-create DNS record untuk Worker

### 4.3 Disable HTTPS Redirect

1. **SSL/TLS** → **Edge Certificates**
2. **Always Use HTTPS:** `Off`
3. **SSL/TLS encryption mode:** `Off` atau `Flexible`

## ✅ STEP 5: Testing & Verification

### 5.1 Test Endpoints

```bash
# Test main page
curl -I http://vps-laravel.gilarya.my.id/

# Test CSS assets
curl -I http://vps-laravel.gilarya.my.id/build/assets/app-[hash].css

# Test JS assets
curl -I http://vps-laravel.gilarya.my.id/build/assets/app-[hash].js
```

### 5.2 Browser Testing

1. **Open:** `http://vps-laravel.gilarya.my.id/`
2. **Check:** Laravel welcome page dengan proper styling
3. **F12 → Network:** Verify semua assets load dengan status 200
4. **F12 → Console:** No error messages

## 🔧 Troubleshooting

### Issue: Assets tidak load (404)

**Solusi:**
```bash
# Rebuild assets
cd /var/www/laravel-app
npm run build

# Check build output
ls -la public/build/assets/

# Clear Laravel cache
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan view:cache
```

### Issue: HTTPS redirect loop

**Solusi:**
1. Disable "Always Use HTTPS" di Cloudflare
2. Set SSL mode ke "Off"
3. Clear browser cache atau gunakan incognito mode

### Issue: Worker timeout

**Solusi:**
1. Check VPS firewall: `sudo ufw allow 60001`
2. Check Nginx status: `sudo systemctl status nginx`
3. Check PHP-FPM: `sudo systemctl status php8.2-fpm`

### Issue: Node.js version conflict

**Solusi:**
```bash
# Remove old Node.js
sudo apt remove -y nodejs npm

# Install Node.js 18+
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Verify version
node --version  # Should be v18+
```

## 🎯 Best Practices

1. **Security:** Gunakan firewall untuk restrict akses ke port 60001
2. **Performance:** Enable Cloudflare caching untuk static assets
3. **Monitoring:** Setup log monitoring untuk Worker dan Nginx
4. **Backup:** Regular backup database dan application files
5. **Updates:** Keep Laravel, PHP, dan Node.js up to date

## 📚 Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Cloudflare Workers Documentation](https://developers.cloudflare.com/workers/)
- [Nginx Configuration Guide](https://nginx.org/en/docs/)
- [Vite Documentation](https://vitejs.dev/)

---

**🎉 Selamat! Laravel app Anda sekarang dapat diakses melalui custom domain dengan Cloudflare Worker sebagai proxy.**
