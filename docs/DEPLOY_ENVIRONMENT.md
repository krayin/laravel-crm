# Padrão de Deploy e Ambiente

Guia completo de configuração de ambiente para projetos Krayin com BrandKit.

---

## Índice

1. [Ambientes e Configurações](#1-ambientes-e-configurações)
2. [Cache Driver](#2-cache-driver)
3. [Queue Driver](#3-queue-driver)
4. [Storage e Filesystem](#4-storage-e-filesystem)
5. [Permissões de Arquivos](#5-permissões-de-arquivos)
6. [Nginx Configuration](#6-nginx-configuration)
7. [Apache Configuration](#7-apache-configuration)
8. [PHP Configuration](#8-php-configuration)
9. [Checklist de Deploy](#9-checklist-de-deploy)
10. [Troubleshooting de Ambiente](#10-troubleshooting-de-ambiente)

---

## 1. Ambientes e Configurações

### Matriz de Ambientes

| Aspecto | Local | Staging | Produção |
|---------|-------|---------|----------|
| `APP_ENV` | `local` | `staging` | `production` |
| `APP_DEBUG` | `true` | `true` | `false` |
| `DB_CONNECTION` | `sqlite` | `mysql` | `mysql` |
| `CACHE_DRIVER` | `file` | `redis` | `redis` |
| `QUEUE_CONNECTION` | `sync` | `redis` | `redis` |
| `SESSION_DRIVER` | `file` | `redis` | `redis` |
| `LOG_CHANNEL` | `stack` | `stack` | `stack` |
| `LOG_LEVEL` | `debug` | `debug` | `warning` |

### .env Exemplo - Produção

```bash
#--------------------------------------
# APP
#--------------------------------------
APP_NAME="Krayin CRM"
APP_ENV=production
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
APP_DEBUG=false
APP_URL=https://crm.empresa.com.br

#--------------------------------------
# DATABASE
#--------------------------------------
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=krayin_prod
DB_USERNAME=krayin_user
DB_PASSWORD=senha_segura_aqui
DB_PREFIX=

#--------------------------------------
# CACHE & SESSION
#--------------------------------------
CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

#--------------------------------------
# REDIS
#--------------------------------------
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_CLIENT=phpredis

#--------------------------------------
# QUEUE
#--------------------------------------
QUEUE_CONNECTION=redis
QUEUE_RETRY_AFTER=90

#--------------------------------------
# MAIL
#--------------------------------------
MAIL_MAILER=smtp
MAIL_HOST=smtp.empresa.com.br
MAIL_PORT=587
MAIL_USERNAME=crm@empresa.com.br
MAIL_PASSWORD=senha_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=crm@empresa.com.br
MAIL_FROM_NAME="${APP_NAME}"

#--------------------------------------
# FILESYSTEM
#--------------------------------------
FILESYSTEM_DISK=public

#--------------------------------------
# LOGGING
#--------------------------------------
LOG_CHANNEL=stack
LOG_LEVEL=warning
LOG_SLACK_WEBHOOK_URL=https://hooks.slack.com/services/xxx

#--------------------------------------
# BRANDKIT ESPECÍFICO
#--------------------------------------
BRANDKIT_CACHE_TTL=3600
BRANDKIT_CSS_MAX_SIZE=51200
BRANDKIT_SNAPSHOT_AUTO=true
```

---

## 2. Cache Driver

### Opções Disponíveis

| Driver | Uso Recomendado | Performance | Persistência |
|--------|-----------------|-------------|--------------|
| `array` | Testes | Alta | Não |
| `file` | Dev local | Média | Sim |
| `redis` | Staging/Prod | Muito Alta | Sim |
| `memcached` | Prod (alternativa) | Muito Alta | Sim |
| `database` | Não recomendado | Baixa | Sim |

### Configuração Redis (Recomendado para Produção)

```bash
# .env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=sua_senha_redis
REDIS_PORT=6379
REDIS_CLIENT=phpredis
```

```php
// config/database.php
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),
    
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_DB', 0),
        'read_timeout' => 60,
    ],
    
    'cache' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_CACHE_DB', 1),  // DB separado para cache
    ],
    
    'session' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_SESSION_DB', 2),  // DB separado para sessão
    ],
],
```

### Cache Keys do BrandKit

```
Padrão: brand_kit.resolved.v{VERSION}.{SCOPE}.{THEME}

Exemplos:
├── brand_kit.resolved.v1.global.default
├── brand_kit.resolved.v1.global.dark
├── brand_kit.resolved.v1.empresa_123.default
└── theme.selected_slug.v1
```

### Verificar Cache Redis

```bash
# Conectar ao Redis
redis-cli

# Listar keys do BrandKit
KEYS brand_kit.*

# Ver TTL de uma key
TTL brand_kit.resolved.v1.global.default

# Ver valor
GET brand_kit.resolved.v1.global.default

# Limpar keys do BrandKit
KEYS brand_kit.* | xargs redis-cli DEL
```

---

## 3. Queue Driver

### Por que usar Queue?

| Operação | Sem Queue | Com Queue |
|----------|-----------|-----------|
| Salvar override | Síncrono (bloqueia) | Síncrono (rápido) |
| Enviar email | 2-5s (bloqueia UI) | Instantâneo (background) |
| Gerar snapshot | 1-3s (bloqueia) | Instantâneo (background) |
| Invalidar cache em cluster | N * latência | Background |

### Configuração Redis Queue

```bash
# .env
QUEUE_CONNECTION=redis
QUEUE_RETRY_AFTER=90
```

### Supervisor para Workers (Produção)

```ini
# /etc/supervisor/conf.d/krayin-worker.conf

[program:krayin-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/krayin/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/krayin/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Comandos Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start krayin-worker:*
sudo supervisorctl status
```

### Monitorar Queue

```bash
# Ver jobs pendentes
php artisan queue:monitor redis:default

# Ver jobs falhados
php artisan queue:failed

# Reprocessar falhados
php artisan queue:retry all

# Limpar falhados
php artisan queue:flush
```

---

## 4. Storage e Filesystem

### Estrutura de Storage

```
storage/
├── app/
│   ├── public/                    # Acessível via symlink
│   │   ├── themes/                # Presets de temas
│   │   │   ├── default/
│   │   │   │   └── theme.json
│   │   │   └── dark/
│   │   │       └── theme.json
│   │   └── theme-manager/         # Uploads de usuários
│   │       ├── logos/
│   │       │   ├── logo_empresa_123.png
│   │       │   └── logo_empresa_456.svg
│   │       └── backgrounds/
│   │           └── bg_login_001.jpg
│   └── private/                   # Não acessível publicamente
│       └── backups/
│           └── brandkit/
│               └── snapshot_20251227.json
├── framework/
│   ├── cache/                     # Cache de arquivo
│   ├── sessions/                  # Sessões de arquivo
│   └── views/                     # Views compiladas
└── logs/
    ├── laravel.log
    └── worker.log
```

### Criar Symlink

```bash
# Criar link public/storage -> storage/app/public
php artisan storage:link

# Verificar
ls -la public/storage
# lrwxrwxrwx 1 www-data www-data 41 Dec 27 10:00 public/storage -> /var/www/krayin/storage/app/public
```

### Configuração de Disco

```php
// config/filesystems.php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
    
    'themes' => [
        'driver' => 'local',
        'root' => storage_path('app/public/themes'),
        'url' => env('APP_URL').'/storage/themes',
        'visibility' => 'public',
    ],
    
    'brandkit' => [
        'driver' => 'local',
        'root' => storage_path('app/public/theme-manager'),
        'url' => env('APP_URL').'/storage/theme-manager',
        'visibility' => 'public',
    ],
],
```

---

## 5. Permissões de Arquivos

### Permissões Corretas (Linux)

```bash
# Dono: www-data (usuário do nginx/apache)
sudo chown -R www-data:www-data /var/www/krayin

# Diretórios: 755 (rwxr-xr-x)
sudo find /var/www/krayin -type d -exec chmod 755 {} \;

# Arquivos: 644 (rw-r--r--)
sudo find /var/www/krayin -type f -exec chmod 644 {} \;

# Storage e Bootstrap/cache: escrita
sudo chmod -R 775 /var/www/krayin/storage
sudo chmod -R 775 /var/www/krayin/bootstrap/cache

# Artisan executável
sudo chmod +x /var/www/krayin/artisan
```

### Verificar Permissões

```bash
# Testar escrita em storage
sudo -u www-data touch /var/www/krayin/storage/test.txt && rm /var/www/krayin/storage/test.txt && echo "OK"

# Testar escrita em cache
sudo -u www-data touch /var/www/krayin/bootstrap/cache/test.txt && rm /var/www/krayin/bootstrap/cache/test.txt && echo "OK"
```

### Problemas Comuns

| Erro | Causa | Solução |
|------|-------|---------|
| `Permission denied: storage/logs` | Dono errado | `chown www-data:www-data` |
| `Unable to write to cache` | Permissão 644 | `chmod 775` |
| `failed to open stream` | SELinux | `setsebool -P httpd_unified 1` |

---

## 6. Nginx Configuration

### Configuração Completa

```nginx
# /etc/nginx/sites-available/krayin

server {
    listen 80;
    server_name crm.empresa.com.br;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name crm.empresa.com.br;
    
    root /var/www/krayin/public;
    index index.php;
    
    # SSL
    ssl_certificate /etc/letsencrypt/live/crm.empresa.com.br/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/crm.empresa.com.br/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256;
    ssl_prefer_server_ciphers off;
    
    # Logs
    access_log /var/log/nginx/krayin_access.log;
    error_log /var/log/nginx/krayin_error.log;
    
    # Gzip
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;
    gzip_min_length 1000;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    
    # Upload size (para logos/backgrounds)
    client_max_body_size 10M;
    
    # Cache de assets estáticos
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }
    
    # Storage público (logos, themes)
    location /storage {
        alias /var/www/krayin/storage/app/public;
        expires 7d;
        add_header Cache-Control "public";
        try_files $uri =404;
    }
    
    # Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        
        # Timeouts
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
    }
    
    # Bloquear acesso a arquivos sensíveis
    location ~ /\.(?!well-known) {
        deny all;
    }
    
    location ~ ^/(\.env|composer\.(json|lock)|package\.json|webpack\.mix\.js) {
        deny all;
    }
}
```

### Testar Configuração

```bash
# Testar sintaxe
sudo nginx -t

# Recarregar
sudo systemctl reload nginx

# Ver logs de erro
sudo tail -f /var/log/nginx/krayin_error.log
```

---

## 7. Apache Configuration

### .htaccess (public/)

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Security
<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

# Gzip
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/css application/json application/javascript
</IfModule>

# Cache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/svg+xml "access plus 1 month"
    ExpiresByType text/css "access plus 1 week"
    ExpiresByType application/javascript "access plus 1 week"
</IfModule>
```

### VirtualHost

```apache
# /etc/apache2/sites-available/krayin.conf

<VirtualHost *:443>
    ServerName crm.empresa.com.br
    DocumentRoot /var/www/krayin/public
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/crm.empresa.com.br/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/crm.empresa.com.br/privkey.pem
    
    <Directory /var/www/krayin/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Alias para storage
    Alias /storage /var/www/krayin/storage/app/public
    <Directory /var/www/krayin/storage/app/public>
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/krayin_error.log
    CustomLog ${APACHE_LOG_DIR}/krayin_access.log combined
</VirtualHost>
```

---

## 8. PHP Configuration

### php.ini Recomendado

```ini
; /etc/php/8.2/fpm/conf.d/99-krayin.ini

; Memory
memory_limit = 256M

; Upload
upload_max_filesize = 10M
post_max_size = 12M
max_file_uploads = 20

; Execution
max_execution_time = 300
max_input_time = 300
default_socket_timeout = 300

; OPcache (IMPORTANTE para performance)
opcache.enable = 1
opcache.memory_consumption = 256
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 10000
opcache.validate_timestamps = 0  ; Desabilitar em prod (requer restart para atualizar)
opcache.revalidate_freq = 0

; Session
session.gc_maxlifetime = 7200

; Error handling
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log
```

### Extensões Necessárias

```bash
# Verificar extensões instaladas
php -m | grep -E "redis|gd|intl|mbstring|pdo_mysql|zip|bcmath"

# Instalar extensões faltantes (Ubuntu/Debian)
sudo apt install php8.2-redis php8.2-gd php8.2-intl php8.2-mbstring php8.2-mysql php8.2-zip php8.2-bcmath

# Reiniciar PHP-FPM
sudo systemctl restart php8.2-fpm
```

### Extensões por Funcionalidade

| Extensão | Obrigatória | Uso |
|----------|-------------|-----|
| `pdo_mysql` | Sim | Banco de dados |
| `mbstring` | Sim | Strings UTF-8 |
| `openssl` | Sim | HTTPS, criptografia |
| `tokenizer` | Sim | Laravel |
| `xml` | Sim | Laravel |
| `ctype` | Sim | Laravel |
| `json` | Sim | APIs |
| `bcmath` | Sim | Cálculos financeiros |
| `gd` | Sim | Processamento de imagens (logos) |
| `intl` | Sim | Internacionalização |
| `zip` | Recomendada | Backups, exports |
| `redis` | Recomendada | Cache/Session/Queue |

---

## 9. Checklist de Deploy

### Antes do Deploy

```bash
□ Backup do banco de dados
  mysqldump -u user -p krayin_prod > backup_$(date +%Y%m%d).sql

□ Backup de arquivos customizados
  tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/app/public/

□ Verificar .env de produção
  - APP_DEBUG=false
  - APP_ENV=production
  - Credenciais corretas

□ Verificar migrations pendentes
  php artisan migrate:status

□ Notificar usuários (se necessário)
```

### Durante o Deploy

```bash
□ Ativar modo de manutenção
  php artisan down --secret="bypass-token-aqui"

□ Pull do código
  git pull origin main

□ Instalar dependências
  composer install --no-dev --optimize-autoloader

□ Rodar migrations
  php artisan migrate --force

□ Limpar caches
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache

□ Invalidar cache do BrandKit
  php artisan tinker --execute="app(\App\Support\BrandKitResolver::class)->invalidateAllGlobal();"

□ Reiniciar queue workers
  php artisan queue:restart

□ Verificar permissões
  sudo chown -R www-data:www-data storage bootstrap/cache

□ Desativar modo de manutenção
  php artisan up
```

### Após o Deploy

```bash
□ Verificar logs de erro
  tail -f storage/logs/laravel.log

□ Testar funcionalidades críticas
  - Login
  - Dashboard
  - BrandKit (CSS aplicando)

□ Verificar queue funcionando
  php artisan queue:monitor

□ Verificar cache funcionando
  php artisan tinker --execute="Cache::put('test', 'ok', 60); echo Cache::get('test');"

□ Monitorar por 15 minutos
```

### Script de Deploy Automatizado

```bash
#!/bin/bash
# deploy.sh

set -e  # Parar em caso de erro

echo "🚀 Iniciando deploy..."

# Variáveis
APP_DIR="/var/www/krayin"
BACKUP_DIR="/var/backups/krayin"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup
echo "📦 Criando backup..."
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_$DATE.sql
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz $APP_DIR/storage/app/public/

# Manutenção
echo "🔧 Ativando manutenção..."
cd $APP_DIR
php artisan down --secret="deploy-$DATE"

# Deploy
echo "📥 Atualizando código..."
git pull origin main

echo "📚 Instalando dependências..."
composer install --no-dev --optimize-autoloader

echo "🗄️ Rodando migrations..."
php artisan migrate --force

echo "🧹 Limpando caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart

echo "🎨 Invalidando BrandKit cache..."
php artisan tinker --execute="app(\App\Support\BrandKitResolver::class)->invalidateAllGlobal();"

echo "🔐 Ajustando permissões..."
chown -R www-data:www-data storage bootstrap/cache

echo "✅ Desativando manutenção..."
php artisan up

echo "🎉 Deploy concluído!"
```

---

## 10. Troubleshooting de Ambiente

### Problema: 502 Bad Gateway

```bash
# Verificar PHP-FPM rodando
sudo systemctl status php8.2-fpm

# Verificar socket existe
ls -la /var/run/php/php8.2-fpm.sock

# Verificar logs PHP-FPM
sudo tail -f /var/log/php8.2-fpm.log

# Reiniciar
sudo systemctl restart php8.2-fpm
```

### Problema: Storage não acessível (404)

```bash
# Verificar symlink
ls -la public/storage

# Se não existe, criar
php artisan storage:link

# Verificar permissões
ls -la storage/app/public/

# Verificar Nginx location
grep -A5 "location /storage" /etc/nginx/sites-enabled/krayin
```

### Problema: Redis connection refused

```bash
# Verificar Redis rodando
sudo systemctl status redis

# Testar conexão
redis-cli ping

# Verificar .env
grep REDIS .env

# Verificar config Laravel
php artisan tinker --execute="dump(config('database.redis'));"
```

### Problema: Queue jobs não processam

```bash
# Verificar worker rodando
sudo supervisorctl status

# Ver logs do worker
tail -f storage/logs/worker.log

# Testar manualmente
php artisan queue:work --once

# Verificar jobs pendentes
php artisan tinker --execute="dump(\Queue::size());"
```

### Problema: Lentidão geral

```bash
# Verificar OPcache
php -i | grep opcache.enable

# Verificar cache de config
test -f bootstrap/cache/config.php && echo "Config cached" || echo "Config NOT cached"

# Verificar cache de rotas
test -f bootstrap/cache/routes-v7.php && echo "Routes cached" || echo "Routes NOT cached"

# Habilitar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

*Documento criado em: 2025-12-27*  
*Última atualização: 2025-12-27*
