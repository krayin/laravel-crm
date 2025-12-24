# ThemeManager - Comandos de Teste

Este documento lista todos os comandos para testar o ThemeManager.

---

## 🧪 Scripts de Teste Automatizados

### 1. Testes Básicos (10 testes)
```bash
php test_theme.php
```

### 2. Testes Avançados (8 testes)
```bash
php test_theme_advanced.php
```

### 3. Teste de Sintaxe PHP
```powershell
$files = @(
    'packages\Webkul\ThemeManager\src\Providers\ThemeManagerServiceProvider.php',
    'packages\Webkul\ThemeManager\src\Providers\ModuleServiceProvider.php',
    'packages\Webkul\ThemeManager\src\Models\ThemeConfig.php',
    'packages\Webkul\ThemeManager\src\Repositories\ThemeConfigRepository.php',
    'packages\Webkul\ThemeManager\src\Helpers\ThemeHelper.php',
    'packages\Webkul\ThemeManager\src\Http\Controllers\ThemeController.php',
    'packages\Webkul\ThemeManager\src\Http\Middleware\ThemeMiddleware.php'
)

foreach ($file in $files) {
    php -l $file
}
```

---

## 🔍 Comandos Artisan

### Verificar Rotas
```bash
php artisan route:list --name=theme
```

### Verificar Cache
```bash
php artisan tinker --execute="app('theme')->getConfig();"
```

### Limpar Cache
```bash
php artisan optimize:clear
php artisan view:clear
php artisan config:clear
```

### Verificar Menu
```bash
php artisan tinker --execute="dump(config('menu.admin'));"
```

### Verificar Traduções
```bash
php artisan tinker --execute="app()->setLocale('pt_BR'); echo trans('theme-manager::app.menu.theme');"
```

### Verificar Tabela
```bash
php artisan tinker --execute="dump(\Illuminate\Support\Facades\Schema::hasTable('theme_configs'));"
```

### Verificar Middleware
```bash
php artisan tinker --execute="dump(app('router')->getMiddlewareGroups()['web']);"
```

---

## 🧩 Testes Manuais no Tinker

### 1. Testar Model
```php
php artisan tinker

$config = \Webkul\ThemeManager\Models\ThemeConfig::getInstance();
dump($config->toArray());
```

### 2. Testar Helper
```php
php artisan tinker

$helper = app('theme');
dump($helper->isActive());
dump($helper->getConfig());
```

### 3. Testar CSS Variables
```php
php artisan tinker

$css = app('theme')->getCssVariables();
echo $css;
```

### 4. Testar Cache
```php
php artisan tinker

// Limpar cache
app('theme')->clearCache();

// Testar velocidade sem cache
$start = microtime(true);
app('theme')->getConfig();
$time1 = microtime(true) - $start;

// Testar velocidade com cache
$start = microtime(true);
app('theme')->getConfig();
$time2 = microtime(true) - $start;

echo "Sem cache: " . ($time1 * 1000) . "ms\n";
echo "Com cache: " . ($time2 * 1000) . "ms\n";
echo "Ganho: " . (($time1 - $time2) / $time1 * 100) . "%\n";
```

### 5. Testar Repository
```php
php artisan tinker

$repo = app(\Webkul\ThemeManager\Repositories\ThemeConfigRepository::class);
$config = $repo->get();
dump($config);
```

### 6. Testar Update
```php
php artisan tinker

$repo = app(\Webkul\ThemeManager\Repositories\ThemeConfigRepository::class);
$data = ['color_primary' => '#FF0000'];
$config = $repo->update($data, 1);
dump($config->color_primary);
```

---

## 🌐 Testes via Browser

### 1. Acessar Página de Configuração
```
http://127.0.0.1:8000/admin/settings/theme
```

### 2. Verificar Menu
```
1. Fazer login em http://127.0.0.1:8000/admin/login
2. Ir em Settings (menu lateral)
3. Expandir "Other Settings"
4. Clicar em "Theme"
```

### 3. Testar Upload de Logo
```
1. Acessar http://127.0.0.1:8000/admin/settings/theme
2. Na seção "Logos", clicar em "Choose File" para logo_main
3. Selecionar uma imagem SVG
4. Clicar em "Save"
5. Verificar se aparece preview
```

### 4. Testar Alteração de Cor
```
1. Acessar http://127.0.0.1:8000/admin/settings/theme
2. Na seção "Colors", clicar no color picker de "Primary Color"
3. Escolher uma cor
4. Clicar em "Save"
5. Verificar se a cor mudou na interface
```

### 5. Verificar CSS Dinâmico
```
1. Acessar http://127.0.0.1:8000/admin/settings/theme
2. Abrir DevTools (F12)
3. Na aba Elements, procurar por <style> no <head>
4. Verificar se contém :root com variáveis CSS
```

---

## 🗄️ Testes de Banco de Dados

### Verificar Estrutura da Tabela
```sql
sqlite3 database/database.sqlite
.schema theme_configs
.quit
```

### Verificar Dados
```sql
sqlite3 database/database.sqlite
SELECT id, is_active, color_primary, logo_main FROM theme_configs;
.quit
```

### Contar Colunas
```sql
sqlite3 database/database.sqlite
PRAGMA table_info(theme_configs);
.quit
```

---

## 📊 Testes de Performance

### 1. Benchmark de Cache
```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

\$helper = app('theme');
\$helper->clearCache();

\$times = [];
for (\$i = 0; \$i < 100; \$i++) {
    \$start = microtime(true);
    \$helper->getConfig();
    \$times[] = (microtime(true) - \$start) * 1000;
}

echo 'Média: ' . array_sum(\$times) / count(\$times) . 'ms\n';
echo 'Min: ' . min(\$times) . 'ms\n';
echo 'Max: ' . max(\$times) . 'ms\n';
"
```

### 2. Tamanho do CSS Gerado
```bash
php artisan tinker --execute="echo strlen(app('theme')->getCssVariables()) . ' bytes';"
```

---

## 🔧 Comandos de Manutenção

### Rebuild do Package
```bash
composer dump-autoload
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

### Reexecutar Migration
```bash
php artisan migrate:refresh --path=packages/Webkul/ThemeManager/Database/Migrations
```

### Publicar Assets
```bash
php artisan vendor:publish --tag=theme-manager-assets --force
```

---

## 📝 Logs e Debug

### Ver Logs do Laravel
```bash
# Windows
Get-Content storage\logs\laravel.log -Tail 50

# Unix
tail -f storage/logs/laravel.log
```

### Verificar Stack Trace
```bash
php artisan tinker

try {
    app('theme')->getConfig();
} catch (\Exception $e) {
    dump($e->getTraceAsString());
}
```

### Debug de Views
```bash
php artisan view:clear
php artisan view:cache
```

---

## ✅ Checklist de Testes Pré-Deploy

Executar antes de fazer deploy em produção:

```bash
# 1. Sintaxe PHP
php -l packages/Webkul/ThemeManager/src/**/*.php

# 2. Testes automatizados
php test_theme.php
php test_theme_advanced.php

# 3. Verificar rotas
php artisan route:list --name=theme

# 4. Verificar migrations
php artisan migrate:status

# 5. Verificar cache
php artisan tinker --execute="dump(app('theme')->getConfig());"

# 6. Limpar tudo
php artisan optimize:clear

# 7. Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Verificar tradução
php artisan tinker --execute="echo trans('theme-manager::app.menu.theme');"

# 9. Teste de acesso (manual)
# Acessar: http://127.0.0.1:8000/admin/settings/theme

# 10. Verificar logs
# Verificar se não há erros em storage/logs/laravel.log
```

---

## 🎯 Testes Específicos por Funcionalidade

### Cores
```php
php artisan tinker

$config = \Webkul\ThemeManager\Models\ThemeConfig::getInstance();
echo "Primary: " . $config->color_primary . "\n";
echo "Success: " . $config->color_success . "\n";
echo "Danger: " . $config->color_danger . "\n";
```

### Logos
```php
php artisan tinker

$config = \Webkul\ThemeManager\Models\ThemeConfig::getInstance();
echo "Main: " . $config->logo_main . "\n";
echo "Light: " . $config->logo_light . "\n";
```

### Login Background
```php
php artisan tinker

$config = \Webkul\ThemeManager\Models\ThemeConfig::getInstance();
echo "BG Image: " . $config->login_bg_image . "\n";
echo "Zoom: " . $config->login_bg_zoom . "\n";
echo "Opacity: " . $config->login_bg_opacity . "\n";
```

### Login Card
```php
php artisan tinker

$config = \Webkul\ThemeManager\Models\ThemeConfig::getInstance();
echo "Enabled: " . ($config->login_card_enabled ? 'YES' : 'NO') . "\n";
echo "Title: " . $config->login_card_title . "\n";
echo "Subtitle: " . $config->login_card_subtitle . "\n";
```

### Empty States
```php
php artisan tinker

$config = \Webkul\ThemeManager\Models\ThemeConfig::getInstance();
$states = [
    'activities', 'calls', 'emails', 'meetings',
    'notes', 'organizations', 'persons', 'leads', 'products'
];

foreach ($states as $state) {
    $field = "empty_state_$state";
    echo "$field: " . ($config->$field ?? 'NULL') . "\n";
}
```

---

**Última atualização**: 21/12/2024
