# Runbooks - Diagnóstico e Resolução de Problemas

Checklists de diagnóstico organizados por sintoma. Siga os passos na ordem até resolver o problema.

---

## Índice

1. [CSS não aplica](#runbook-001---css-não-aplica)
2. [Override não carrega](#runbook-002---override-não-carrega)
3. [Cache preso](#runbook-003---cache-preso)
4. [Vite build falha](#runbook-004---vite-build-falha)
5. [Snapshot não restaura](#runbook-005---snapshot-não-restaura)
6. [Tema não muda](#runbook-006---tema-não-muda)
7. [Erro 500 no BrandKit](#runbook-007---erro-500-no-brandkit)
8. [Logo não atualiza](#runbook-008---logo-não-atualiza)
9. [Login background não aparece](#runbook-009---login-background-não-aparece)
10. [Permissão negada](#runbook-010---permissão-negada)

---

## Runbook #001 - CSS Não Aplica

**Sintoma:** CSS customizado foi salvo mas não aparece no admin/login.

### Checklist de Diagnóstico

```
□ Passo 1: Verificar se CSS está habilitado
```
```bash
php artisan tinker --execute="
    dump(App\Models\BrandKitCustomCss::where('is_enabled', true)->get(['id','name','target']));
"
```
**Esperado:** Lista de CSS com `is_enabled = true`  
**Se vazio:** O CSS foi salvo mas não está ativo. Ativar via toggle.

---

```
□ Passo 2: Verificar target (admin vs login)
```
```bash
php artisan tinker --execute="
    \$css = App\Models\BrandKitCustomCss::latest()->first();
    dump(['target' => \$css->target, 'is_enabled' => \$css->is_enabled]);
"
```
**Esperado:** `target` deve ser `admin`, `login` ou `both`  
**Se errado:** Atualizar o target do CSS.

---

```
□ Passo 3: Verificar se CssValidator bloqueou
```
```bash
php artisan tinker --execute="
    \$validator = app(App\Support\CssValidator::class);
    \$css = App\Models\BrandKitCustomCss::latest()->first();
    dump(\$validator->validate(\$css->css_content));
"
```
**Esperado:** Array vazio `[]`  
**Se tem erros:** CSS contém padrões bloqueados. Ver lista em `CssValidator::BLOCKED_PATTERNS`.

---

```
□ Passo 4: Verificar cache do Resolver
```
```bash
php artisan tinker --execute="
    app(App\Support\BrandKitResolver::class)->invalidate('global', 'default');
    echo 'Cache invalidado';
"
```
Depois, recarregar a página.

---

```
□ Passo 5: Verificar se partial está incluído no layout
```
```bash
grep -r "theme-head" resources/views/vendor/admin/layouts/
```
**Esperado:** `@include('admin::partials.theme-head')` ou similar

---

```
□ Passo 6: Verificar ThemeContext no middleware
```
```bash
grep -r "themeContext" app/Http/Middleware/
```
**Esperado:** Middleware que injeta `$themeContext` na view

---

```
□ Passo 7: Inspecionar HTML gerado
```
No browser, View Source e procurar por:
```html
<style id="brand-kit-custom-css">
```
**Se não existe:** Partial não está sendo renderizado.  
**Se existe mas vazio:** CSS não está chegando no ThemeContext.

---

### Correções Rápidas

```bash
# Forçar refresh do cache
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Invalidar cache específico do BrandKit
php artisan tinker --execute="
    app(App\Support\BrandKitResolver::class)->invalidateAllGlobal();
"
```

---

## Runbook #002 - Override Não Carrega

**Sintoma:** Override foi salvo no banco mas valor não aparece na UI.

### Checklist de Diagnóstico

```
□ Passo 1: Verificar se override existe no banco
```
```bash
php artisan tinker --execute="
    dump(App\Models\BrandKitOverride::where('override_key', 'color_primary')->first());
"
```
**Esperado:** Registro com `value` preenchido e `is_active = true`

---

```
□ Passo 2: Verificar is_active
```
```bash
php artisan tinker --execute="
    dump(App\Models\BrandKitOverride::where('is_active', false)->pluck('override_key'));
"
```
**Se o override está aqui:** Foi desativado. Reativar.

---

```
□ Passo 3: Verificar scope_key e theme_slug
```
```bash
php artisan tinker --execute="
    \$override = App\Models\BrandKitOverride::where('override_key', 'color_primary')->first();
    dump(['scope' => \$override->scope_key, 'theme' => \$override->theme_slug]);
"
```
**Esperado:** `scope_key = 'global'`, `theme_slug` = tema atual  
**Se diferente:** Override está em outro scope/tema.

---

```
□ Passo 4: Verificar tema selecionado
```
```bash
php artisan tinker --execute="
    dump(app(App\Support\ThemeSelectionResolver::class)->getSelectedThemeSlug());
"
```
**Esperado:** Deve bater com o `theme_slug` do override

---

```
□ Passo 5: Verificar resolução final
```
```bash
php artisan tinker --execute="
    \$resolved = app(App\Support\BrandKitResolver::class)->resolve('global', 'default');
    dump(\$resolved['config']['color_primary'] ?? 'NÃO ENCONTRADO');
"
```
**Esperado:** Valor do override  
**Se valor padrão:** Override não está sendo aplicado na cadeia de resolução.

---

```
□ Passo 6: Verificar KEY_MAP no Resolver
```
```bash
grep -A5 "KEY_MAP" app/Support/BrandKitResolver.php
```
**Esperado:** A chave do override deve estar mapeada  
**Se não está:** Adicionar ao KEY_MAP.

---

```
□ Passo 7: Invalidar cache e testar novamente
```
```bash
php artisan tinker --execute="
    app(App\Support\BrandKitResolver::class)->invalidate('global', 'default');
    \$resolved = app(App\Support\BrandKitResolver::class)->resolve('global', 'default');
    dump(\$resolved['config']);
"
```

---

## Runbook #003 - Cache Preso

**Sintoma:** Alterações no BrandKit não refletem, mesmo após salvar.

### Checklist de Diagnóstico

```
□ Passo 1: Identificar driver de cache
```
```bash
grep "CACHE_DRIVER" .env
```
**Comum:** `file`, `redis`, `array`, `database`

---

```
□ Passo 2: Limpar cache geral
```
```bash
php artisan cache:clear
```

---

```
□ Passo 3: Limpar cache específico do BrandKit
```
```bash
php artisan tinker --execute="
    // Cache key pattern: brand_kit.resolved.v1.{scope}.{theme}
    Cache::forget('brand_kit.resolved.v1.global.default');
    echo 'BrandKit cache cleared';
"
```

---

```
□ Passo 4: Limpar TODOS os caches do BrandKit
```
```bash
php artisan tinker --execute="
    app(App\Support\BrandKitResolver::class)->invalidateAllGlobal();
    echo 'All BrandKit caches cleared';
"
```

---

```
□ Passo 5: Verificar se cache está funcionando
```
```bash
php artisan tinker --execute="
    Cache::put('test_key', 'test_value', 60);
    dump(Cache::get('test_key'));
    Cache::forget('test_key');
"
```
**Esperado:** `'test_value'`  
**Se null:** Problema com driver de cache.

---

```
□ Passo 6: Se Redis, verificar conexão
```
```bash
php artisan tinker --execute="
    try {
        Redis::ping();
        echo 'Redis OK';
    } catch (\Exception \$e) {
        echo 'Redis ERRO: ' . \$e->getMessage();
    }
"
```

---

```
□ Passo 7: Se file cache, verificar permissões
```
```bash
ls -la storage/framework/cache/data/
# Deve ter permissão de escrita

# Limpar cache de arquivo manualmente
rm -rf storage/framework/cache/data/*
```

---

```
□ Passo 8: Nuclear option - limpar tudo
```
```bash
php artisan optimize:clear
# Equivale a: cache:clear + config:clear + route:clear + view:clear
```

---

## Runbook #004 - Vite Build Falha

**Sintoma:** `npm run build` ou `npm run dev` falha.

### Checklist de Diagnóstico

```
□ Passo 1: Verificar versão do Node
```
```bash
node -v
# Esperado: v18+ ou v20+

npm -v
# Esperado: 9+ ou 10+
```

---

```
□ Passo 2: Limpar node_modules e reinstalar
```
```bash
rm -rf node_modules
rm package-lock.json
npm install
```

---

```
□ Passo 3: Verificar erro específico
```
```bash
npm run build 2>&1 | head -50
```
Procurar por:
- `Cannot find module` → Dependência faltando
- `SyntaxError` → Erro de sintaxe em JS/CSS
- `ENOENT` → Arquivo não encontrado
- `EACCES` → Problema de permissão

---

```
□ Passo 4: Verificar vite.config.js
```
```bash
cat vite.config.js
```
**Verificar:**
- Paths estão corretos
- Plugins estão instalados
- Input files existem

---

```
□ Passo 5: Verificar se arquivos de entrada existem
```
```bash
# Ver inputs no vite.config.js e verificar se existem
ls -la resources/css/app.css
ls -la resources/js/app.js
```

---

```
□ Passo 6: Testar build em modo verbose
```
```bash
npm run build -- --debug
```

---

```
□ Passo 7: Verificar espaço em disco
```
```bash
df -h .
# Vite precisa de espaço para cache
```

---

```
□ Passo 8: Limpar cache do Vite
```
```bash
rm -rf node_modules/.vite
npm run build
```

---

### Erros Comuns e Soluções

| Erro | Causa | Solução |
|------|-------|---------|
| `ENOENT: no such file` | Arquivo não existe | Verificar path no vite.config.js |
| `Cannot find module 'vite'` | Dependência não instalada | `npm install` |
| `Unexpected token` | Sintaxe inválida | Verificar arquivo mencionado no erro |
| `EACCES: permission denied` | Permissão | `sudo chown -R $USER node_modules` |
| `JavaScript heap out of memory` | Pouca RAM | `NODE_OPTIONS=--max_old_space_size=4096 npm run build` |

---

## Runbook #005 - Snapshot Não Restaura

**Sintoma:** Ao restaurar snapshot, dados não voltam ao estado anterior.

### Checklist de Diagnóstico

```
□ Passo 1: Verificar se snapshot existe
```
```bash
php artisan tinker --execute="
    dump(App\Models\BrandKitSnapshot::find(ID_DO_SNAPSHOT));
"
```
**Esperado:** Objeto com `overrides_data` e `custom_css_data`

---

```
□ Passo 2: Verificar conteúdo do snapshot
```
```bash
php artisan tinker --execute="
    \$snap = App\Models\BrandKitSnapshot::find(ID_DO_SNAPSHOT);
    dump([
        'overrides_count' => count(\$snap->overrides_data ?? []),
        'css_count' => count(\$snap->custom_css_data ?? []),
    ]);
"
```
**Se zeros:** Snapshot foi criado quando não havia dados.

---

```
□ Passo 3: Verificar scope/theme do snapshot
```
```bash
php artisan tinker --execute="
    \$snap = App\Models\BrandKitSnapshot::find(ID_DO_SNAPSHOT);
    dump(['scope' => \$snap->scope_key, 'theme' => \$snap->theme_slug]);
"
```
**Esperado:** Deve bater com scope/theme atual

---

```
□ Passo 4: Testar restore manual
```
```bash
php artisan tinker --execute="
    \$repo = app(App\Support\BrandKitRepository::class);
    \$result = \$repo->restoreSnapshot(ID_DO_SNAPSHOT);
    dump(\$result);
"
```
**Esperado:** `true`  
**Se `false`:** Snapshot não encontrado.

---

```
□ Passo 5: Verificar logs de erro
```
```bash
tail -50 storage/logs/laravel.log | grep -i "snapshot\|brandkit"
```

---

```
□ Passo 6: Verificar transaction
```
O restore usa transaction. Se falhar no meio, faz rollback.
```bash
php artisan tinker --execute="
    DB::enableQueryLog();
    \$repo = app(App\Support\BrandKitRepository::class);
    \$repo->restoreSnapshot(ID_DO_SNAPSHOT);
    dump(DB::getQueryLog());
"
```

---

```
□ Passo 7: Verificar se dados foram restaurados
```
```bash
php artisan tinker --execute="
    dump([
        'overrides' => App\Models\BrandKitOverride::count(),
        'css' => App\Models\BrandKitCustomCss::count(),
    ]);
"
```

---

## Runbook #006 - Tema Não Muda

**Sintoma:** Alterou tema selecionado mas UI continua com tema anterior.

### Checklist de Diagnóstico

```
□ Passo 1: Verificar tema no banco
```
```bash
php artisan tinker --execute="
    dump(DB::table('theme_configs')->where('id', 1)->first());
"
```
**Verificar:** `selected_theme` e `is_active`

---

```
□ Passo 2: Verificar ThemeSelectionResolver
```
```bash
php artisan tinker --execute="
    dump(app(App\Support\ThemeSelectionResolver::class)->getSelectedThemeSlug());
"
```
**Esperado:** Tema selecionado no banco

---

```
□ Passo 3: Invalidar cache do tema
```
```bash
php artisan tinker --execute="
    app(App\Support\ThemeSelectionResolver::class)->invalidate();
    echo 'Theme cache invalidated';
"
```

---

```
□ Passo 4: Verificar se tema existe no disco
```
```bash
ls -la storage/app/public/themes/
# Deve existir pasta com nome do tema

ls -la storage/app/public/themes/NOME_DO_TEMA/theme.json
# Deve existir theme.json
```

---

```
□ Passo 5: Verificar preview mode (session)
```
```bash
php artisan tinker --execute="
    // Se estiver em preview, session override tema do banco
    dump(session('theme_preview'));
"
```
**Se tem valor:** Está em modo preview. Limpar session.

---

```
□ Passo 6: Verificar config fallback
```
```bash
grep "theme.current" config/
php artisan tinker --execute="dump(config('theme.current'));"
```

---

```
□ Passo 7: Nuclear - limpar tudo
```
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan tinker --execute="
    app(App\Support\ThemeSelectionResolver::class)->invalidate();
    app(App\Support\BrandKitResolver::class)->invalidateAllGlobal();
"
```

---

## Runbook #007 - Erro 500 no BrandKit

**Sintoma:** Endpoints do BrandKit retornam erro 500.

### Checklist de Diagnóstico

```
□ Passo 1: Verificar log de erro
```
```bash
tail -100 storage/logs/laravel.log
```
Procurar por stack trace mais recente.

---

```
□ Passo 2: Erros comuns e causas
```

| Erro | Causa Provável |
|------|----------------|
| `Class not found` | Autoload desatualizado |
| `Target class does not exist` | Binding incorreto no container |
| `SQLSTATE` | Problema de banco/migration |
| `Call to undefined method` | Versão errada de classe |

---

```
□ Passo 3: Regenerar autoload
```
```bash
composer dump-autoload
```

---

```
□ Passo 4: Verificar bindings do container
```
```bash
php artisan tinker --execute="
    try {
        app(App\Support\BrandKitRepository::class);
        echo 'Repository: OK';
    } catch (\Exception \$e) {
        echo 'Repository ERRO: ' . \$e->getMessage();
    }
"
```

---

```
□ Passo 5: Verificar migrations
```
```bash
php artisan migrate:status | grep brand_kit
```
**Esperado:** Todas com `Ran`

---

```
□ Passo 6: Verificar se tabelas existem
```
```bash
php artisan tinker --execute="
    dump([
        'overrides' => Schema::hasTable('brand_kit_overrides'),
        'css' => Schema::hasTable('brand_kit_custom_css'),
        'snapshots' => Schema::hasTable('brand_kit_snapshots'),
    ]);
"
```

---

```
□ Passo 7: Testar endpoint específico
```
```bash
# Via curl (ajustar URL conforme ambiente)
curl -X GET "http://localhost/admin/brand-kit/config" \
  -H "Accept: application/json" \
  -H "Cookie: laravel_session=SEU_COOKIE"
```

---

```
□ Passo 8: Habilitar debug mode
```
```bash
# Temporariamente em .env
APP_DEBUG=true

# Ver erro completo no browser/response
```

---

## Runbook #008 - Logo Não Atualiza

**Sintoma:** Upload de logo novo mas UI mostra logo antigo.

### Checklist de Diagnóstico

```
□ Passo 1: Verificar se arquivo foi salvo
```
```bash
ls -la storage/app/public/theme-manager/
# Procurar pelo arquivo do logo
```

---

```
□ Passo 2: Verificar symlink do storage
```
```bash
ls -la public/storage
# Deve apontar para storage/app/public

# Se não existe, criar:
php artisan storage:link
```

---

```
□ Passo 3: Verificar URL do logo
```
```bash
php artisan tinker --execute="
    \$resolved = app(App\Support\BrandKitResolver::class)->resolve('global', 'default');
    dump(\$resolved['config']['logo_main'] ?? 'NÃO DEFINIDO');
"
```

---

```
□ Passo 4: Verificar cache do browser
```
- Ctrl+Shift+R (hard refresh)
- Ou abrir em aba anônima
- Ou adicionar `?v=timestamp` na URL

---

```
□ Passo 5: Verificar JavaScript de substituição
```
No browser, abrir Console (F12) e procurar por:
```
ThemeManager: Iniciando troca de logos...
```

---

```
□ Passo 6: Verificar se logo está no config
```
```bash
php artisan tinker --execute="
    dump(DB::table('theme_configs')->first());
"
```
Procurar campos `logo_main`, `logo_light`, `logo_icon`.

---

```
□ Passo 7: Limpar cache de assets
```
```bash
php artisan view:clear
# E fazer hard refresh no browser
```

---

## Runbook #009 - Login Background Não Aparece

**Sintoma:** Imagem de fundo da tela de login não aparece.

### Checklist de Diagnóstico

```
□ Passo 1: Verificar se imagem existe
```
```bash
ls -la storage/app/public/theme-manager/
# Procurar arquivo de background
```

---

```
□ Passo 2: Verificar config
```
```bash
php artisan tinker --execute="
    \$resolved = app(App\Support\BrandKitResolver::class)->resolve('global', 'default');
    dump([
        'bg_image' => \$resolved['config']['login_bg_image'] ?? 'NÃO DEFINIDO',
        'bg_opacity' => \$resolved['config']['login_bg_opacity'] ?? 'NÃO DEFINIDO',
    ]);
"
```

---

```
□ Passo 3: Verificar CSS variables no HTML
```
No browser, View Source na página de login, procurar:
```css
--theme-login-bg-url: url('...');
```

---

```
□ Passo 4: Verificar classe do body
```
No browser, inspecionar `<body>`:
```html
<body class="theme-login-bg">
```
**Se não tem a classe:** Background não será aplicado.

---

```
□ Passo 5: Verificar partial de login
```
```bash
grep -r "theme-login-bg" resources/views/
```

---

```
□ Passo 6: Verificar permissões do arquivo
```
```bash
ls -la storage/app/public/theme-manager/ARQUIVO_BG
# Deve ter permissão de leitura
```

---

```
□ Passo 7: Testar URL direta
```
```bash
# No browser, acessar diretamente:
# http://localhost/storage/theme-manager/ARQUIVO_BG.jpg
```

---

## Runbook #010 - Permissão Negada

**Sintoma:** Usuário não consegue acessar BrandKit (403 ou redirecionado).

### Checklist de Diagnóstico

```
□ Passo 1: Verificar middleware de permissão
```
```bash
grep -n "BrandKitPermission" routes/web.php
```
**Nota:** No MVP, middleware está comentado.

---

```
□ Passo 2: Verificar se usuário está logado
```
```bash
php artisan tinker --execute="
    // Simular request autenticado
    dump(auth()->check());
"
```

---

```
□ Passo 3: Verificar role/permission do usuário
```
```bash
php artisan tinker --execute="
    \$user = App\Models\User::find(ID_DO_USUARIO);
    dump(\$user->roles ?? 'Sem roles');
    dump(\$user->permissions ?? 'Sem permissions');
"
```

---

```
□ Passo 4: Verificar middleware na rota
```
```bash
php artisan route:list | grep brand-kit
```
Ver coluna de middleware.

---

```
□ Passo 5: Verificar Authorize no FormRequest
```
```bash
grep -n "authorize" app/Http/Requests/Admin/BrandKit/
```
**Nota:** Todos retornam `true` no MVP.

---

```
□ Passo 6: Verificar logs de acesso negado
```
```bash
tail -50 storage/logs/laravel.log | grep -i "403\|unauthorized\|forbidden"
```

---

```
□ Passo 7: Bypass temporário (DEBUG ONLY)
```
```php
// Em AppServiceProvider::boot() - APENAS PARA DEBUG
Gate::before(function ($user, $ability) {
    if (app()->environment('local')) {
        return true;
    }
});
```

---

## Quick Reference - Comandos Úteis

### Cache
```bash
# Limpar tudo
php artisan optimize:clear

# Só cache
php artisan cache:clear

# BrandKit específico
php artisan tinker --execute="app(App\Support\BrandKitResolver::class)->invalidateAllGlobal();"
```

### Debug
```bash
# Ver último erro
tail -50 storage/logs/laravel.log

# Queries executadas
php artisan tinker --execute="DB::enableQueryLog(); /* código */; dump(DB::getQueryLog());"

# Dump de config resolvida
php artisan tinker --execute="dump(app(App\Support\BrandKitResolver::class)->resolve('global','default'));"
```

### Database
```bash
# Status de migrations
php artisan migrate:status

# Re-rodar migrations (CUIDADO: perde dados)
php artisan migrate:fresh

# Verificar tabela
php artisan tinker --execute="dump(Schema::getColumnListing('brand_kit_overrides'));"
```

### Assets
```bash
# Rebuild assets
npm run build

# Recriar symlink
php artisan storage:link

# Limpar views compiladas
php artisan view:clear
```

---

*Documento criado em: 2025-12-27*  
*Última atualização: 2025-12-27*
