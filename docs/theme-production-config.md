# Theme System - Production Configuration

> Configurações necessárias para staging e produção.

---

## 1. Cache Driver

### 1.1 Requisito

O sistema de temas usa cache intensivamente. Em produção, **evite** o driver `file`.

### 1.2 Configuração Recomendada

```env
# .env (staging/production)
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 1.3 Por quê Redis?

- **Atomicidade:** `Cache::forget()` é garantidamente atômico
- **Performance:** Leituras em memória, não I/O de disco
- **Escalabilidade:** Funciona em múltiplas instâncias (load balancer)

### 1.4 Fallback: Database

Se Redis não estiver disponível:

```env
CACHE_DRIVER=database
```

Rode a migration:
```bash
php artisan cache:table
php artisan migrate
```

---

## 2. Session Driver

### 2.1 Requisito

Preview usa session. Em produção com múltiplas instâncias, **evite** `file`.

### 2.2 Configuração Recomendada

```env
# .env (staging/production)
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
```

### 2.3 Alternativa: Database

```env
SESSION_DRIVER=database
```

Rode:
```bash
php artisan session:table
php artisan migrate
```

---

## 3. Queue (Opcional)

### 3.1 Quando usar Queue?

Se snapshots começarem a demorar (muitos overrides + CSS), mova para queue.

### 3.2 Configuração

```env
QUEUE_CONNECTION=redis
```

**Nota:** Atualmente, snapshots são síncronos. Para async, seria necessário refatorar `BrandKitSnapshotService` para usar Jobs.

---

## 4. Storage

### 4.1 Temas

Temas ficam em `storage/app/public/themes/{slug}/theme.json`.

Em produção com múltiplas instâncias:

```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=xxx
AWS_SECRET_ACCESS_KEY=xxx
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=my-bucket
```

**Atenção:** Se usar S3, ajuste `BrandKitResolver::loadAndNormalizePreset()` para usar `Storage::disk('s3')`.

### 4.2 Uploads (logos, backgrounds)

Por padrão, uploads vão para `storage/app/public/theme-manager/`.

Para S3:
```php
// No ThemeConfigRepository (ou controller)
$path = $request->file('logo_main')->store('theme-manager', 's3');
```

---

## 5. Environment Variables

### 5.1 Mínimo para Produção

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com

CACHE_DRIVER=redis
SESSION_DRIVER=redis

# Opcional
LOG_CHANNEL=stack
LOG_LEVEL=warning
```

### 5.2 Variáveis Específicas do Tema (futuro)

```env
# TTL de cache do tema (segundos) - padrão 3600
THEME_CACHE_TTL=3600

# Máximo de snapshots automáticos - padrão 10
THEME_MAX_AUTO_SNAPSHOTS=10

# Timeout de preview (minutos) - padrão 30
THEME_PREVIEW_TIMEOUT=30
```

**Nota:** Essas variáveis ainda não estão implementadas. Atualmente são constantes nas classes.

---

## 6. Deploy Checklist

### 6.1 Antes do Deploy

```bash
# Local/CI
php artisan test --filter=BrandKit
php artisan test --filter=Theme
```

### 6.2 Durante o Deploy

```bash
# 1. Modo manutenção
php artisan down

# 2. Pull do código
git pull origin main

# 3. Dependências
composer install --no-dev --optimize-autoloader

# 4. Migrations
php artisan migrate --force

# 5. Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Sair manutenção
php artisan up
```

### 6.3 Após o Deploy

```bash
# Verificar rotas
php artisan route:list | grep brand-kit | wc -l  # Deve ser 9

# Smoke test
curl -s https://seu-dominio.com/admin/settings/theme -I | head -1  # HTTP/2 200
```

---

## 7. Diferenças Local vs Produção

| Aspecto | Local | Produção |
|---------|-------|----------|
| Cache | `file` | `redis` |
| Session | `file` | `redis` ou `database` |
| Debug | `true` | `false` |
| Log Level | `debug` | `warning` |
| Asset Compilation | `npm run dev` | `npm run build` |
| Optimize | Não | `config:cache`, `route:cache` |

---

## 8. Monitoramento

### 8.1 Logs Importantes

```bash
# Erros de tema
grep -i "theme\|brandkit" /var/log/laravel/laravel.log | grep -i "error\|exception"

# Cache misses
grep "ThemeCache" /var/log/laravel/laravel.log | grep -i "miss\|flush"
```

### 8.2 Métricas Sugeridas

- **Cache hit rate:** % de requests que usam cache
- **Snapshot count:** Total de snapshots por tenant
- **Preview duration:** Tempo médio de sessão de preview
- **Save latency:** Tempo do endpoint de save

### 8.3 Alertas Sugeridos

| Condição | Severidade | Ação |
|----------|------------|------|
| Cache driver = file em prod | Warning | Migrar para Redis |
| > 100 snapshots por tema | Info | Limpar antigos |
| Erro em restore | Critical | Verificar transação |
| Theme não encontrado | Error | Verificar storage |

---

## 9. Segurança

### 9.1 Uploads

- Validar extensões: `image/*`, `.ico`, `.svg`
- Limitar tamanho: `max:2048` (2MB)
- Sanitizar nomes: `Str::slug()`

### 9.2 Custom CSS

- Validar com `CssValidator`
- Remover `@import`, `url()` externos
- Escapar `</style>` para prevenir XSS

### 9.3 Permissões

Rotas protegidas por:
- `middleware('user')` - Autenticação admin
- `ThemePermission` middleware (customizável)

---

## 10. Backup

### 10.1 O que fazer backup

- Tabela `theme_configs`
- Tabela `brand_kit_overrides`
- Tabela `brand_kit_snapshots`
- Tabela `brand_kit_custom_css`
- Diretório `storage/app/public/themes/`
- Diretório `storage/app/public/theme-manager/`

### 10.2 Restore

```bash
# 1. Restore DB
mysql -u user -p database < backup.sql

# 2. Restore storage
rsync -av backup/themes/ storage/app/public/themes/
rsync -av backup/theme-manager/ storage/app/public/theme-manager/

# 3. Clear cache
php artisan cache:clear
```

---

*Última atualização: Dezembro 2024*
