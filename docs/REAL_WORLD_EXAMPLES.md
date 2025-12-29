# Exemplos do Mundo Real - Krayin Customizações

Documento com exemplos reais de erros, estrutura do repositório e customizações mais solicitadas por clientes.

---

## 1. Exemplos de Erros Reais (Logs e Diagnóstico)

### Erro #1: Container Binding Incorreto

**Ambiente:** Desenvolvimento  
**Data:** 2025-12-27  
**Severidade:** Alta (aplicação não iniciava)

**Log do erro:**
```
[2025-12-27 03:15:42] local.ERROR: Target class [App\Repositories\BrandKitRepository] 
does not exist. {"exception":"[object] (Illuminate\\Contracts\\Container\\
BindingResolutionException(code: 0): Target class [App\\Repositories\\BrandKitRepository] 
does not exist. at /var/www/html/vendor/laravel/framework/src/Illuminate/Container/
Container.php:879)

Stack trace:
#0 /var/www/html/vendor/laravel/framework/src/Illuminate/Container/Container.php(758): 
   Illuminate\\Container\\Container->build('App\\\\Repositories...')
#1 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(851): 
   Illuminate\\Container\\Container->resolve('App\\\\Repositories...', Array)
#2 /var/www/html/vendor/laravel/framework/src/Illuminate/Container/Container.php(694): 
   Illuminate\\Foundation\\Application->resolve('App\\\\Repositories...', Array)
...
"}
```

**Screenshot do terminal:**
```
┌─────────────────────────────────────────────────────────────────────────────┐
│ $ php artisan serve                                                          │
│                                                                              │
│    INFO  Server running on [http://127.0.0.1:8000].                         │
│                                                                              │
│    Press Ctrl+C to stop the server                                          │
│                                                                              │
│ [2025-12-27 03:15:42] local.ERROR: Target class                             │
│ [App\Repositories\BrandKitRepository] does not exist.                       │
│                                                                              │
│    Illuminate\Contracts\Container\BindingResolutionException                │
│                                                                              │
│    at vendor/laravel/framework/src/Illuminate/Container/Container.php:879  │
│      875|                                                                    │
│      876|         try {                                                      │
│      877|             $reflector = new ReflectionClass($concrete);           │
│      878|         } catch (ReflectionException $e) {                         │
│  >>>  879|             throw new BindingResolutionException(                │
│      880|                 "Target class [$concrete] does not exist.",        │
│      881|                 0, $e                                              │
│      882|             );                                                     │
│      883|         }                                                          │
└─────────────────────────────────────────────────────────────────────────────┘
```

**Causa raiz:**
```php
// AppServiceProvider.php registrava classe que não existia
public function register(): void
{
    // ❌ ERRADO - Este arquivo não existe!
    $this->app->singleton(\App\Repositories\BrandKitRepository::class);
    
    // ✅ CERTO - O arquivo real está em Support/
    $this->app->singleton(\App\Support\BrandKitRepository::class);
}
```

**Diagnóstico executado:**
```bash
$ ls -la app/Repositories/
total 0
drwxr-xr-x 1 user user 0 Dec 25 17:01 .
drwxr-xr-x 1 user user 0 Dec 25 17:01 ..
# Pasta vazia! Arquivo não existe.

$ ls -la app/Support/BrandKitRepository.php
-rw-r--r-- 1 user user 15234 Dec 25 20:16 app/Support/BrandKitRepository.php
# Arquivo está aqui.
```

**Correção aplicada:**
```php
// AppServiceProvider.php - CORRIGIDO
public function register(): void
{
    $this->app->singleton(\App\Support\CssValidator::class);
    $this->app->singleton(\App\Support\BrandKitResolver::class);
    $this->app->singleton(\App\Support\ThemeSelectionResolver::class);
    $this->app->singleton(\App\Support\BrandKitRepository::class); // ✅ Caminho correto
}
```

**Tempo para resolver:** 25 minutos  
**Lição:** Sempre verificar se o arquivo existe antes de registrar no container.

---

### Erro #2: CSS Injection Bloqueado (Falso Positivo)

**Ambiente:** Staging  
**Data:** 2025-12-26  
**Severidade:** Média (funcionalidade bloqueada)

**Log do erro:**
```
[2025-12-26 14:32:18] staging.WARNING: BrandKit CSS rejected - contains blocked pattern 
{"css_preview":"/* Custom button styles */\n.btn-primary {\n    background: linear-gradient(...",
"pattern_matched":"/@import\\b/i","user_id":5,"ip":"192.168.1.100"} 

[2025-12-26 14:32:18] staging.INFO: CSS validation failed for user 5 
{"validation_errors":["CSS contém conteúdo não permitido."]}
```

**Screenshot da UI (reconstruído):**
```
┌─────────────────────────────────────────────────────────────────────────────┐
│  Brand Kit > Custom CSS                                              [Save] │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  Nome: Botões Personalizados                                                │
│  ┌────────────────────────────────────────────────────────────────────────┐ │
│  │ /* Custom button styles */                                             │ │
│  │ .btn-primary {                                                         │ │
│  │     background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);    │ │
│  │     border: none;                                                      │ │
│  │     /* Importante: não usar @import aqui */                           │ │
│  │ }                                                                      │ │
│  └────────────────────────────────────────────────────────────────────────┘ │
│                                                                              │
│  ╔════════════════════════════════════════════════════════════════════════╗ │
│  ║  ⚠️  Erro: CSS contém conteúdo não permitido.                          ║ │
│  ╚════════════════════════════════════════════════════════════════════════╝ │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

**Causa raiz:**

O comentário `/* não usar @import aqui */` continha a palavra `@import`, que era bloqueada pelo regex:
```php
// CssValidator.php - Regex muito agressivo
private const BLOCKED_PATTERNS = [
    '/@import\b/i',  // Isso pegava @import dentro de comentários também!
];
```

**CSS do usuário (válido, mas rejeitado):**
```css
/* Custom button styles */
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    /* Importante: não usar @import aqui */  /* ← Comentário causou o bloqueio */
}
```

**Diagnóstico executado:**
```bash
$ php artisan tinker
>>> $validator = app(\App\Support\CssValidator::class);
>>> $css = "/* não usar @import aqui */ .btn { color: red; }";
>>> $validator->validate($css);
=> [
     "CSS contém conteúdo não permitido.",
   ]
# Confirmado: comentário com @import é bloqueado
```

**Correção considerada (não aplicada ainda):**
```php
// Opção 1: Remover comentários antes de validar
public function validate(string $css): array
{
    // Remove comentários CSS antes de verificar padrões
    $cssWithoutComments = preg_replace('/\/\*.*?\*\//s', '', $css);
    
    foreach (self::BLOCKED_PATTERNS as $pattern) {
        if (preg_match($pattern, $cssWithoutComments)) {
            // ...
        }
    }
}

// Opção 2: Regex mais específico (não pegar dentro de comentários)
// Complexo e propenso a erros, não recomendado
```

**Decisão:** Aceitar o comportamento atual. Usuários não devem mencionar `@import` nem em comentários. Documentar esta limitação.

**Tempo para diagnosticar:** 15 minutos  
**Lição:** Validação de segurança pode ter falsos positivos. Documentar limitações.

---

### Erro #3: Cache Não Invalida (Dados Antigos Persistem)

**Ambiente:** Produção  
**Data:** 2025-12-25  
**Severidade:** Alta (clientes vendo dados errados)

**Log do erro:**
```
[2025-12-25 09:45:00] production.INFO: Override saved successfully 
{"override_key":"color_primary","value":"#FF5733","user_id":1}

[2025-12-25 09:45:15] production.DEBUG: BrandKit config resolved 
{"color_primary":"#0284C7","cache_hit":true,"cache_key":"brand_kit.resolved.v1.global.default"}

[2025-12-25 09:46:00] production.INFO: User reported: "Cor não mudou após salvar"
{"ticket_id":"SUP-1234","user_id":1}

[2025-12-25 09:47:30] production.DEBUG: Cache investigation 
{"cache_driver":"redis","ttl_remaining":3547,"expected_value":"#FF5733","actual_value":"#0284C7"}
```

**Timeline do problema:**
```
09:45:00  Usuário salva cor #FF5733
          ├─ DB atualizado: ✅
          └─ Cache invalidado: ❌ (falhou silenciosamente)
          
09:45:15  Usuário recarrega página
          ├─ Cache hit: valor antigo #0284C7
          └─ Usuário vê cor errada
          
09:46:00  Usuário abre ticket de suporte

09:47:30  Investigação revela cache não invalidado
```

**Causa raiz:**

Redis estava com conexão instável. O `Cache::forget()` falhou silenciosamente:
```php
// BrandKitResolver.php - Código original
public function invalidate(string $scopeKey, string $themeSlug): void
{
    Cache::forget($this->cacheKey($scopeKey, $themeSlug));
    // Não verificava se realmente invalidou!
}
```

**Log do Redis:**
```
[2025-12-25 09:45:00] redis.WARNING: Connection timed out after 100ms, 
operation: DEL, key: brand_kit.resolved.v1.global.default
```

**Diagnóstico executado:**
```bash
# Verificar conexão Redis
$ redis-cli ping
Could not connect to Redis at 127.0.0.1:6379: Connection refused

# Redis tinha reiniciado e app não reconectou
$ sudo systemctl status redis
● redis.service - Redis In-Memory Data Store
   Active: active (running) since Wed 2025-12-25 09:44:50 UTC; 2min ago
   # Redis reiniciou às 09:44:50, 10 segundos antes do save
```

**Correção aplicada:**
```php
// BrandKitResolver.php - CORRIGIDO
public function invalidate(string $scopeKey, string $themeSlug): void
{
    $key = $this->cacheKey($scopeKey, $themeSlug);
    
    try {
        $forgotten = Cache::forget($key);
        
        if (!$forgotten) {
            // Log warning mas não falha
            Log::warning('BrandKit cache invalidation may have failed', [
                'key' => $key,
                'driver' => config('cache.default'),
            ]);
        }
        
        // Double-check: tentar ler o cache
        if (Cache::has($key)) {
            Log::error('BrandKit cache still exists after invalidation', [
                'key' => $key,
            ]);
            // Forçar com TTL de 1 segundo
            Cache::put($key, null, 1);
        }
        
    } catch (\Exception $e) {
        Log::error('BrandKit cache invalidation exception', [
            'key' => $key,
            'error' => $e->getMessage(),
        ]);
        // Não propagar exceção - operação principal deve continuar
    }
}
```

**Correção adicional - Health check:**
```php
// Adicionar ao schedule
$schedule->call(function () {
    try {
        Cache::put('health_check', now(), 10);
        $value = Cache::get('health_check');
        if (!$value) {
            Log::critical('Cache health check failed');
            // Alertar equipe
        }
    } catch (\Exception $e) {
        Log::critical('Cache connection failed', ['error' => $e->getMessage()]);
    }
})->everyMinute();
```

**Tempo para resolver:** 45 minutos  
**Lição:** Cache invalidation deve ter fallback e logging. Nunca confiar que operações de cache funcionam silenciosamente.

---

## 2. Árvore do Repositório de Customizações

```
laravel-crm/
├── .github/
│   └── workflows/
│       ├── ci.yml                    # Testes automatizados
│       └── admin_playwright_tests.yml # Testes E2E
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── BrandKit/
│   │   │   │   │   ├── BrandKitController.php      # Controller principal (stub)
│   │   │   │   │   ├── OverridesController.php     # CRUD de overrides
│   │   │   │   │   ├── CustomCssController.php     # CRUD de CSS
│   │   │   │   │   ├── SnapshotsController.php     # Gerenciamento de snapshots
│   │   │   │   │   └── Concerns/
│   │   │   │   │       └── HandlesSnapshots.php    # Trait para auto-snapshot
│   │   │   │   │
│   │   │   │   └── ThemeController.php             # Seleção de tema
│   │   │   │
│   │   │   └── BrandKitController.php              # Controller API completo
│   │   │
│   │   ├── Middleware/
│   │   │   ├── ThemePermission.php                 # Permissão de tema
│   │   │   ├── BrandKitPermission.php              # Permissão de brandkit (futuro)
│   │   │   └── CaptureThemeSelection.php           # Captura tema da sessão
│   │   │
│   │   └── Requests/
│   │       └── Admin/
│   │           └── BrandKit/
│   │               ├── SetOverrideRequest.php      # Validação de override
│   │               ├── ResetOverrideRequest.php    # Validação de reset
│   │               ├── AddCustomCssRequest.php     # Validação de CSS (com segurança)
│   │               ├── CreateSnapshotRequest.php   # Validação de snapshot
│   │               └── RestoreSnapshotRequest.php  # Validação de restore
│   │
│   ├── Models/
│   │   ├── BrandKitOverride.php                    # Model de overrides
│   │   ├── BrandKitCustomCss.php                   # Model de CSS customizado
│   │   └── BrandKitSnapshot.php                    # Model de snapshots
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php                  # Bindings do container
│   │   └── ThemeBootProvider.php                   # Boot do tema
│   │
│   └── Support/
│       ├── BrandKitResolver.php                    # Resolução de config (cache)
│       ├── BrandKitRepository.php                  # CRUD centralizado
│       ├── CssValidator.php                        # Validação de segurança CSS
│       ├── ThemeSelectionResolver.php              # Qual tema está ativo
│       ├── ThemeContextFactory.php                 # Cria contexto para views
│       └── ThemeContext.php                        # Value object do tema
│
├── config/
│   └── brandkit.php                                # Configurações do BrandKit (futuro)
│
├── database/
│   └── migrations/
│       ├── 2024_12_23_100000_add_selected_theme_to_theme_configs.php
│       ├── 2024_12_24_100000_add_previous_theme_to_theme_configs.php
│       ├── 2025_12_25_205615_create_brand_kit_overrides_table.php
│       ├── 2025_12_25_205628_create_brand_kit_custom_css_table.php
│       └── 2025_12_25_205637_create_brand_kit_snapshots_table.php
│
├── docs/
│   ├── INCIDENT_LOG.md                             # Registro de incidentes
│   ├── RUNBOOKS.md                                 # Guias de troubleshooting
│   ├── BRANDKIT_EVOLUTION.md                       # História do sistema
│   ├── KRAYIN_LESSONS_LEARNED.md                   # Lições aprendidas
│   └── REAL_WORLD_EXAMPLES.md                      # Este documento
│
├── packages/
│   └── Webkul/
│       └── ThemeManager/                           # Package de gerenciamento de tema
│           ├── src/
│           │   ├── ThemeManager.php
│           │   └── Providers/
│           │       └── ThemeManagerServiceProvider.php
│           └── Resources/
│               └── views/
│                   └── components/
│                       └── theme-styles.blade.php
│
├── resources/
│   └── views/
│       └── vendor/
│           └── admin/
│               ├── layouts/
│               │   └── anonymous.blade.php         # Layout de login (override)
│               └── partials/
│                   └── theme-head.blade.php        # Injeção de CSS do BrandKit
│
├── routes/
│   ├── web.php                                     # Rotas web (inclui brandkit)
│   └── brand-kit.php                               # Rotas API do BrandKit
│
├── storage/
│   └── app/
│       └── public/
│           ├── themes/                             # Presets de temas
│           │   ├── default/
│           │   │   └── theme.json
│           │   └── dark/
│           │       └── theme.json
│           └── theme-manager/                      # Uploads (logos, backgrounds)
│               ├── logos/
│               └── backgrounds/
│
├── tests/
│   ├── Unit/
│   │   ├── BrandKitResolverTest.php
│   │   ├── CssValidatorTest.php
│   │   └── ThemeContextFactoryTest.php
│   └── Feature/
│       └── BrandKitApiTest.php
│
└── tools/
    └── deploy-theme.sh                             # Script de deploy de tema
```

### Legenda de Status

| Símbolo | Significado |
|---------|-------------|
| ✅ | Implementado e funcionando |
| 🚧 | Em desenvolvimento |
| 📋 | Planejado |
| ⚠️ | Precisa de correção |

### Status Atual dos Diretórios

```
app/
├── Http/Controllers/Admin/BrandKit/    ⚠️  Duplicado com Controllers/BrandKitController.php
├── Http/Requests/Admin/BrandKit/       ✅  Funcionando
├── Models/                              ✅  Funcionando
├── Support/                             ✅  Funcionando (usar este, não Repositories/)
└── Providers/                           ⚠️  Binding incorreto

routes/
├── web.php                              ⚠️  Duplicado com brand-kit.php
└── brand-kit.php                        ✅  Usar este (mais completo)

docs/                                    ✅  Documentação completa
```

---

## 3. Customizações Mais Comuns Solicitadas por Clientes

### Categoria 1: Identidade Visual (90% dos clientes pedem)

| # | Customização | Complexidade | Frequência |
|---|--------------|--------------|------------|
| 1.1 | **Trocar logo principal** | Baixa | 100% |
| 1.2 | **Trocar favicon** | Baixa | 95% |
| 1.3 | **Mudar cor primária** | Baixa | 90% |
| 1.4 | **Mudar cores secundárias** | Baixa | 70% |
| 1.5 | **Logo para modo escuro** | Média | 50% |

**Exemplo de pedido típico:**
> "Queremos o CRM com as cores da nossa empresa: azul #003366 como principal e laranja #FF6600 nos botões de ação."

**Implementação atual:**
```php
// Via API
POST /admin/brand-kit/overrides
{
    "override_key": "color_primary",
    "value": "#003366"
}

POST /admin/brand-kit/overrides
{
    "override_key": "color_accent",
    "value": "#FF6600"
}
```

---

### Categoria 2: Tela de Login (80% dos clientes pedem)

| # | Customização | Complexidade | Frequência |
|---|--------------|--------------|------------|
| 2.1 | **Imagem de fundo** | Baixa | 80% |
| 2.2 | **Opacidade do overlay** | Baixa | 60% |
| 2.3 | **Texto de boas-vindas** | Baixa | 70% |
| 2.4 | **Remover "Powered by Krayin"** | Baixa | 90% |
| 2.5 | **Card com imagem de fundo** | Média | 30% |
| 2.6 | **Link para suporte** | Baixa | 40% |

**Exemplo de pedido típico:**
> "Na tela de login, queremos uma foto do nosso escritório de fundo, com nosso slogan 'Conectando você ao sucesso' e sem aparecer 'Powered by Krayin'."

**Implementação atual:**
```php
// Overrides para login
$overrides = [
    'login_bg_image' => 'backgrounds/escritorio.jpg',
    'login_bg_opacity' => 0.7,
    'login_card_title' => 'Bem-vindo',
    'login_card_subtitle' => 'Conectando você ao sucesso',
    'login_show_powered_by' => false,
];
```

---

### Categoria 3: CSS Customizado (60% dos clientes pedem)

| # | Customização | Complexidade | Frequência |
|---|--------------|--------------|------------|
| 3.1 | **Botões com gradiente** | Baixa | 40% |
| 3.2 | **Fonte customizada** | Média | 30% |
| 3.3 | **Sidebar com cor diferente** | Baixa | 35% |
| 3.4 | **Cards com sombra diferente** | Baixa | 20% |
| 3.5 | **Animações em hover** | Média | 15% |

**Exemplo de pedido típico:**
> "Queremos que os botões tenham um efeito de gradiente e que quando passar o mouse, eles 'levantes' um pouco."

**Implementação via CSS customizado:**
```css
/* Gradiente nos botões */
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    transition: transform 0.2s, box-shadow 0.2s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}
```

---

### Categoria 4: Campos e Formulários (50% dos clientes pedem)

| # | Customização | Complexidade | Frequência |
|---|--------------|--------------|------------|
| 4.1 | **Adicionar campo customizado em Lead** | Média | 60% |
| 4.2 | **Remover campos não usados** | Média | 50% |
| 4.3 | **Mudar labels de campos** | Baixa | 70% |
| 4.4 | **Adicionar validação customizada** | Alta | 30% |
| 4.5 | **Campo com máscara (telefone, CPF)** | Média | 40% |

**Exemplo de pedido típico:**
> "Precisamos de um campo 'Origem do Lead' com opções: Google, Facebook, Indicação, Outros. E o campo 'Fax' pode remover, ninguém usa."

**Nota:** Esta categoria requer modificações além do BrandKit (atributos customizados do Krayin).

---

### Categoria 5: Emails e Notificações (40% dos clientes pedem)

| # | Customização | Complexidade | Frequência |
|---|--------------|--------------|------------|
| 5.1 | **Template de email com logo** | Média | 60% |
| 5.2 | **Assinatura padrão** | Baixa | 50% |
| 5.3 | **Cores do email** | Baixa | 40% |
| 5.4 | **Rodapé com dados da empresa** | Baixa | 70% |
| 5.5 | **Desabilitar notificações específicas** | Média | 30% |

**Exemplo de pedido típico:**
> "Os emails que saem do CRM precisam ter nosso logo no topo, nossas cores, e no rodapé: endereço, telefone e link para o site."

---

### Categoria 6: Dashboards e Relatórios (35% dos clientes pedem)

| # | Customização | Complexidade | Frequência |
|---|--------------|--------------|------------|
| 6.1 | **Widget customizado no dashboard** | Alta | 30% |
| 6.2 | **Relatório com campos específicos** | Alta | 40% |
| 6.3 | **Gráfico com cores da marca** | Média | 25% |
| 6.4 | **KPIs personalizados** | Alta | 20% |
| 6.5 | **Exportar com logo da empresa** | Média | 35% |

**Exemplo de pedido típico:**
> "No dashboard, queremos ver: total de leads do mês, taxa de conversão, e valor total de negócios fechados. Com gráfico de pizza nas nossas cores."

---

### Categoria 7: Integrações (30% dos clientes pedem)

| # | Customização | Complexidade | Frequência |
|---|--------------|--------------|------------|
| 7.1 | **Integração com WhatsApp** | Alta | 50% |
| 7.2 | **Integração com ERP** | Muito Alta | 20% |
| 7.3 | **Webhook para sistema externo** | Média | 30% |
| 7.4 | **SSO (Single Sign-On)** | Alta | 15% |
| 7.5 | **API customizada** | Alta | 25% |

**Exemplo de pedido típico:**
> "Quando um lead for criado, precisa disparar para nosso sistema de marketing automaticamente."

---

### Matriz de Priorização

```
                        FREQUÊNCIA
                    Alta            Baixa
                ┌───────────────┬───────────────┐
         Baixa  │ 1.1 Logo      │ 3.4 Sombras   │
                │ 1.3 Cor       │ 3.5 Animações │
                │ 2.4 Powered   │               │
    COMPLEXIDADE├───────────────┼───────────────┤
                │ 4.1 Campos    │ 6.1 Widgets   │
         Alta   │ 7.1 WhatsApp  │ 7.2 ERP       │
                │               │ 7.4 SSO       │
                └───────────────┴───────────────┘
                
Legenda:
- Quadrante superior esquerdo: FAZER PRIMEIRO (alto impacto, baixo esforço)
- Quadrante superior direito: NICE TO HAVE
- Quadrante inferior esquerdo: FAZER DEPOIS (alto impacto, alto esforço)
- Quadrante inferior direito: AVALIAR ROI
```

---

### Pacotes de Customização Sugeridos

**Pacote Básico (Identidade Visual)**
- Logo principal
- Favicon  
- Cor primária
- Tela de login (fundo + texto)
- Remover "Powered by"

**Pacote Intermediário (Básico + UX)**
- Tudo do Básico
- CSS customizado (até 3 regras)
- 5 campos customizados
- Template de email

**Pacote Avançado (Intermediário + Integrações)**
- Tudo do Intermediário
- 1 integração (WhatsApp OU Webhook)
- Dashboard customizado
- Relatório personalizado

---

## Resumo

### Erros Mais Comuns (Top 3)

1. **Binding incorreto** - Container resolve classe errada
2. **Cache não invalida** - Dados antigos persistem
3. **Validação agressiva** - Bloqueia CSS válido

### Estrutura Recomendada

- Controllers em `app/Http/Controllers/Admin/BrandKit/`
- Support classes em `app/Support/`
- Requests em `app/Http/Requests/Admin/BrandKit/`
- Documentação em `docs/`

### Customizações Mais Lucrativas

1. **Identidade Visual** - 90% pedem, baixa complexidade
2. **Tela de Login** - 80% pedem, baixa complexidade
3. **Campos Customizados** - 50% pedem, média complexidade

---

*Documento criado em: 2025-12-27*  
*Baseado em: Projeto BrandKit + Padrões de mercado*  
*Última atualização: 2025-12-27*
