# Lições Aprendidas - Projetos Krayin

Documento baseado em observações reais do desenvolvimento do BrandKit e padrões identificados em projetos que envolvem customização do Krayin CRM.

---

## Índice

1. [Padrões Observados em Troubleshooting](#1-padrões-observados-em-troubleshooting)
2. [O que Fazer Logo no Início](#2-o-que-fazer-logo-no-início)
3. [Definições Críticas para Tranquilidade](#3-definições-críticas-para-tranquilidade)
4. [Checklist de Início de Projeto](#4-checklist-de-início-de-projeto)
5. [Anti-Patterns a Evitar](#5-anti-patterns-a-evitar)

---

## 1. Padrões Observados em Troubleshooting

### 1.1 Padrão: "Onde está o código de verdade?"

**Sintoma:** Arquivos criados/editados não surtem efeito.

**Causa raiz:** Krayin tem estrutura de packages + app. É comum editar no lugar errado.

```
Estrutura típica confusa:
├── app/                          # Código da aplicação
├── packages/Webkul/Admin/        # Package do admin (vendor-like)
├── resources/views/              # Views da app
└── resources/views/vendor/       # Overrides de views de packages
```

**O que aconteceu neste projeto:**
- Arquivos criados em `Krayin-/app/` (raiz)
- Projeto real estava em `Krayin-/laravel-crm/app/`
- Resultado: nenhuma alteração funcionava

**Lição:** Sempre verificar ANTES de começar:
```bash
# Onde está o artisan?
ls -la */artisan

# Onde está o .env?
ls -la */.env

# Qual é o git remote?
git remote -v
cd laravel-crm && git remote -v
```

---

### 1.2 Padrão: "Funciona no tinker, não funciona na request"

**Sintoma:** Código funciona em `php artisan tinker` mas falha em HTTP request.

**Causas comuns:**

| Causa | Diagnóstico | Solução |
|-------|-------------|---------|
| Middleware não executou | `dd()` no middleware | Verificar ordem no Kernel |
| Service Provider não bootou | `app()->bound(Class::class)` | Verificar register vs boot |
| Cache de config | Funciona após `config:clear` | Não cachear em dev |
| Binding diferente | Container resolve classe errada | Verificar AppServiceProvider |

**O que aconteceu neste projeto:**
```php
// AppServiceProvider registrava:
$this->app->singleton(\App\Repositories\BrandKitRepository::class);

// Controller usava:
use App\Support\BrandKitRepository;  // Classe diferente!
```

**Lição:** Verificar namespaces e bindings antes de assumir que "funciona".

---

### 1.3 Padrão: "Cache infinito"

**Sintoma:** Alterações não refletem, mesmo após salvar.

**Krayin tem múltiplas camadas de cache:**

```
┌─────────────────────────────────────────────┐
│              CAMADAS DE CACHE               │
├─────────────────────────────────────────────┤
│ 1. Config cache    → php artisan config:clear │
│ 2. Route cache     → php artisan route:clear  │
│ 3. View cache      → php artisan view:clear   │
│ 4. App cache       → php artisan cache:clear  │
│ 5. Composer cache  → composer dump-autoload   │
│ 6. Vite cache      → rm -rf node_modules/.vite│
│ 7. Browser cache   → Ctrl+Shift+R             │
│ 8. OPcache         → Reiniciar PHP-FPM        │
│ 9. BrandKit cache  → Resolver->invalidate()   │
└─────────────────────────────────────────────┘
```

**Comando nuclear (usar com cuidado):**
```bash
php artisan optimize:clear && composer dump-autoload
```

**Lição:** Em dev, NÃO usar `php artisan config:cache` nem `route:cache`.

---

### 1.4 Padrão: "Funciona local, quebra em staging"

**Sintoma:** Tudo funciona na máquina do dev, falha no servidor.

**Causas comuns em Krayin:**

| Causa | Local | Servidor |
|-------|-------|----------|
| SQLite vs MySQL | SQLite | MySQL |
| Storage symlink | Existe | Não existe |
| Permissões | 777 | 755 |
| PHP version | 8.2 | 8.1 |
| Extensions | Todas | Falta gd/intl |

**O que observamos neste projeto:**
- Desenvolvimento em SQLite (`DB_CONNECTION=sqlite`)
- Produção provavelmente MySQL
- Queries podem ter sintaxe incompatível

**Lição:** Ambiente de dev deve espelhar produção ao máximo.

---

### 1.5 Padrão: "View não encontrada"

**Sintoma:** `View [admin::something] not found`

**Hierarquia de views no Krayin:**

```
1. resources/views/vendor/admin/...     (override - maior prioridade)
2. packages/Webkul/Admin/Resources/views/...  (package original)
```

**Armadilhas:**
- Criar view em `resources/views/admin/` (errado, falta `vendor/`)
- Esquecer o namespace `admin::`
- View existe mas com extensão errada (`.php` vs `.blade.php`)

**Lição:** Usar `php artisan view:clear` e verificar caminho exato.

---

### 1.6 Padrão: "Migration já rodou mas tabela não existe"

**Sintoma:** `migrate:status` mostra "Ran" mas tabela não existe.

**Causas:**
- Migration rodou em banco diferente (`.env` errado)
- Migration rodou mas teve rollback
- Tabela foi dropada manualmente
- Arquivo de migration foi renomeado

**Diagnóstico:**
```bash
# Ver o que o Laravel ACHA que rodou
php artisan migrate:status

# Ver o que REALMENTE existe no banco
php artisan tinker --execute="dump(Schema::getAllTables());"

# Comparar
```

**Lição:** Se em dúvida, `migrate:fresh` em dev (NUNCA em prod).

---

## 2. O que Fazer Logo no Início

### 2.1 Mapear a Estrutura do Projeto (Dia 1)

**Antes de escrever qualquer código:**

```bash
# 1. Identificar raiz do projeto Laravel
find . -name "artisan" -type f 2>/dev/null

# 2. Verificar git
git remote -v
git branch -a

# 3. Verificar ambiente
cat .env | grep -E "^(APP_|DB_|CACHE_)"

# 4. Verificar packages instalados
composer show | grep krayin
composer show | grep webkul

# 5. Listar estrutura de alto nível
tree -L 2 -d app/ packages/ 2>/dev/null || find app packages -maxdepth 2 -type d
```

**Criar documento de contexto:**
```markdown
# Contexto do Projeto

- **Raiz Laravel:** `/path/to/laravel-crm`
- **Branch principal:** `main` ou `master`
- **Remote origin:** `https://github.com/...`
- **DB:** MySQL 8.0 / SQLite
- **PHP:** 8.2
- **Packages Krayin:** Admin, Core, Lead, etc.
```

---

### 2.2 Entender o Fluxo de Request (Dia 1-2)

**Krayin usa middlewares específicos. Mapear:**

```bash
# Ver middlewares globais
cat app/Http/Kernel.php

# Ver middlewares de rotas admin
grep -r "middleware" routes/ | head -20

# Ver service providers
cat config/app.php | grep -A 50 "'providers'"
```

**Criar diagrama de fluxo:**
```
Request HTTP
    │
    ▼
Kernel.php (middlewares globais)
    │
    ▼
RouteServiceProvider (carrega routes/)
    │
    ▼
Middleware de rota (web, user, admin)
    │
    ▼
Controller
    │
    ▼
View (com dados do controller)
```

---

### 2.3 Verificar Padrões Existentes (Dia 2-3)

**Antes de criar algo novo, ver como Krayin faz:**

```bash
# Como são os controllers existentes?
head -50 app/Http/Controllers/*.php

# Como são os repositories?
ls packages/Webkul/*/src/Repositories/

# Como são as migrations?
ls -la database/migrations/ | tail -10

# Como são as rotas?
cat routes/web.php | head -50
```

**Perguntas a responder:**
- [ ] Controllers usam injeção de dependência ou facades?
- [ ] Existe padrão Repository? Como é usado?
- [ ] Existe padrão de Form Requests?
- [ ] Views usam componentes Blade ou includes?
- [ ] Assets usam Vite ou Mix?

---

### 2.4 Setup de Desenvolvimento (Dia 1)

**Garantir ambiente funcional ANTES de codar:**

```bash
# 1. Instalar dependências
composer install
npm install

# 2. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 3. Banco de dados
php artisan migrate

# 4. Storage link
php artisan storage:link

# 5. Build assets
npm run build

# 6. Testar
php artisan serve
# Acessar http://localhost:8000
```

**Se qualquer passo falhar, PARAR e resolver antes de continuar.**

---

## 3. Definições Críticas para Tranquilidade

### 3.1 Definir Namespace e Localização (CRÍTICO)

**Decidir ANTES de criar o primeiro arquivo:**

| Decisão | Opções | Recomendação |
|---------|--------|--------------|
| Namespace base | `App\` vs `Modules\` vs `Packages\` | `App\` para features da app |
| Controllers | `App\Http\Controllers\Admin\` vs `App\Http\Controllers\` | Com subpasta por domínio |
| Models | `App\Models\` vs `App\Entities\` | `App\Models\` (padrão Laravel) |
| Repositories | `App\Repositories\` vs `App\Support\` | Escolher UM e manter |
| Services | `App\Services\` vs `App\Support\` | Escolher UM e manter |

**O que aconteceu neste projeto:**
- Repository em `App\Repositories\` E `App\Support\`
- Controller em `App\Http\Controllers\` E `App\Http\Controllers\Admin\BrandKit\`
- Resultado: confusão, duplicidade, bugs

**Template de decisão:**
```markdown
## Convenções do Projeto BrandKit

### Localização de Arquivos
- Controllers: `app/Http/Controllers/Admin/BrandKit/`
- Requests: `app/Http/Requests/Admin/BrandKit/`
- Models: `app/Models/BrandKit*.php`
- Repository: `app/Support/BrandKitRepository.php` (ÚNICO)
- Rotas: `routes/brand-kit.php` (ÚNICO)

### Namespaces
- Controllers: `App\Http\Controllers\Admin\BrandKit`
- Models: `App\Models`
- Support: `App\Support`
```

---

### 3.2 Definir Estratégia de Upgrade (CRÍTICO)

**Krayin recebe updates. Como não quebrar?**

| Estratégia | Descrição | Risco |
|------------|-----------|-------|
| Fork e nunca atualizar | Copia código, ignora upstream | Acumula débito técnico |
| Override apenas | Só usar `vendor/` overrides, nunca editar packages | Limitado mas seguro |
| Package próprio | Criar package que estende Krayin | Mais trabalho, mais controle |
| Híbrido | Override + package para features grandes | Recomendado |

**Regras de ouro:**
1. **NUNCA** editar arquivos em `packages/Webkul/`
2. **SEMPRE** usar `resources/views/vendor/` para override de views
3. **SEMPRE** usar `config/` local para override de configs
4. **DOCUMENTAR** cada override e por quê

**Criar arquivo de tracking:**
```markdown
# Overrides do Projeto

## Views Overrideadas
| Original | Override | Motivo |
|----------|----------|--------|
| `admin::layouts.master` | `vendor/admin/layouts/master.blade.php` | Injetar BrandKit CSS |

## Configs Overrideados
| Original | Override | Motivo |
|----------|----------|--------|
| `admin.php` | `config/admin.php` | Adicionar menu BrandKit |
```

---

### 3.3 Definir Contrato de API (CRÍTICO para backends)

**Antes de criar endpoints, definir:**

```yaml
# Exemplo: brand-kit-api.yaml

endpoints:
  GET /admin/brand-kit/config:
    response:
      success: boolean
      data:
        config: object
        theme_slug: string
    
  POST /admin/brand-kit/overrides:
    request:
      override_key: string (required)
      value: string (nullable)
      scope_key: string (default: "global")
      theme_slug: string (default: "default")
    response:
      success: boolean
      data: BrandKitOverride

error_format:
  success: false
  message: string
  errors: object (validação)
```

**Por que isso importa:**
- Frontend sabe o que esperar
- Testes podem ser escritos contra contrato
- Mudanças são versionadas
- Documentação automática possível

---

### 3.4 Definir Estratégia de Cache (CRÍTICO para performance)

**Decidir antes de implementar:**

| Aspecto | Decisão |
|---------|---------|
| Driver | Redis (prod) / Array (test) / File (dev) |
| TTL padrão | 1 hora para config, 5 min para sessão |
| Invalidação | Explícita (evento) vs TTL expira |
| Prefixo | `brandkit.` para todas as keys |
| Tags | Usar se Redis, não usar se File |

**Implementação padrão:**
```php
class BrandKitCache
{
    private const PREFIX = 'brandkit.';
    private const TTL = 3600;

    public function remember(string $key, callable $callback)
    {
        return Cache::remember(
            self::PREFIX . $key,
            self::TTL,
            $callback
        );
    }

    public function invalidate(string $key): void
    {
        Cache::forget(self::PREFIX . $key);
    }

    public function invalidateAll(): void
    {
        // Implementar baseado no driver
    }
}
```

---

### 3.5 Definir Estratégia de Testes (CRÍTICO para manutenção)

**Mínimo viável:**

```
tests/
├── Unit/
│   ├── BrandKitResolverTest.php      # Lógica de resolução
│   ├── CssValidatorTest.php          # Validação de CSS
│   └── BrandKitRepositoryTest.php    # CRUD
├── Feature/
│   ├── BrandKitApiTest.php           # Endpoints HTTP
│   └── BrandKitCacheTest.php         # Invalidação funciona
└── Integration/
    └── BrandKitFlowTest.php          # Fluxo completo
```

**Testes mínimos antes de PR:**
```php
// Pelo menos testar o caminho feliz
public function test_can_set_override(): void
{
    $response = $this->postJson('/admin/brand-kit/overrides', [
        'override_key' => 'color_primary',
        'value' => '#FF0000',
    ]);

    $response->assertStatus(200)
             ->assertJsonPath('success', true);
    
    $this->assertDatabaseHas('brand_kit_overrides', [
        'override_key' => 'color_primary',
        'value' => '#FF0000',
    ]);
}
```

---

## 4. Checklist de Início de Projeto

### Antes de Escrever Código

```
□ Identificar diretório raiz do projeto Laravel
□ Verificar git remote e branch atual
□ Ler .env e entender ambiente
□ Rodar projeto localmente com sucesso
□ Acessar admin panel e fazer login
□ Entender estrutura de packages do Krayin
```

### Antes de Criar Primeira Feature

```
□ Documentar convenções de namespace
□ Documentar localização de arquivos
□ Definir estratégia de upgrade-safety
□ Definir padrões de código (Repository? Services?)
□ Criar estrutura de pastas
□ Setup de testes básico
```

### Antes de Primeiro PR

```
□ Código em local correto (não duplicado)
□ Bindings do container corretos
□ Cache invalidando corretamente
□ Pelo menos 1 teste passando
□ Documentação mínima
□ Migrations revisadas
```

### Antes de Deploy

```
□ Migrations testadas em banco similar a prod
□ Rollback de migration funciona
□ Cache não vai quebrar (TTL razoável)
□ Logs adequados para troubleshooting
□ Feature flag se necessário
□ Plano de rollback documentado
```

---

## 5. Anti-Patterns a Evitar

### 5.1 "Funciona na minha máquina"

**Errado:**
```php
// Path hardcoded
$path = '/home/usuario/projeto/storage/themes';

// Assumir OS
$separator = '\\';  // Windows only
```

**Certo:**
```php
// Usar helpers Laravel
$path = storage_path('themes');

// Usar constante
$separator = DIRECTORY_SEPARATOR;
```

---

### 5.2 "Vou arrumar depois"

**Errado:**
```php
// TODO: adicionar validação
$css = $request->input('css');
DB::table('custom_css')->insert(['css' => $css]);
```

**Certo:**
```php
// Validar AGORA
$validated = $request->validate([
    'css' => ['required', 'string', 'max:50000'],
]);

if (!$this->cssValidator->isValid($validated['css'])) {
    abort(422, 'CSS inválido');
}
```

---

### 5.3 "É só um override simples"

**Errado:**
```php
// Controller fazendo tudo
public function store(Request $request)
{
    $override = new BrandKitOverride();
    $override->key = $request->key;
    $override->value = $request->value;
    $override->save();
    
    Cache::forget('brandkit.config');
    
    return response()->json($override);
}
```

**Certo:**
```php
// Controller delega para Repository
public function store(StoreOverrideRequest $request)
{
    $override = $this->repository->setOverride(
        $request->validated('scope_key', 'global'),
        $request->validated('theme_slug', 'default'),
        $request->validated('override_key'),
        $request->validated('value'),
        $request->user()?->id,
    );
    
    return response()->json(['success' => true, 'data' => $override]);
}
```

---

### 5.4 "Copia do StackOverflow"

**Errado:**
```php
// Copiado sem entender
function sanitize_css($css) {
    return preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $css);
}
// CSS não tem tag <script>, isso não faz sentido
```

**Certo:**
```php
// Entender o problema e solução
private const DANGEROUS_PATTERNS = [
    '/@import\b/i',           // Bloqueia @import (pode carregar externo)
    '/expression\s*\(/i',     // Bloqueia expression() (JS em CSS, IE)
    '/url\s*\(.*javascript/i', // Bloqueia url(javascript:...)
];

public function isValid(string $css): bool
{
    foreach (self::DANGEROUS_PATTERNS as $pattern) {
        if (preg_match($pattern, $css)) {
            return false;
        }
    }
    return true;
}
```

---

### 5.5 "Não precisa de teste"

**Errado:**
```
"Testei manualmente, funciona"
↓
Deploy
↓
Bug em produção
↓
"Mas funcionava na minha máquina!"
```

**Certo:**
```php
public function test_css_validator_blocks_javascript(): void
{
    $validator = new CssValidator();
    
    $this->assertFalse(
        $validator->isValid('body { background: url(javascript:alert(1)) }')
    );
    
    $this->assertFalse(
        $validator->isValid('@import url("https://evil.com/track.css")')
    );
    
    $this->assertTrue(
        $validator->isValid('body { background: #fff; }')
    );
}
```

---

## Resumo Executivo

### Top 5 Coisas a Fazer no Início

1. **Mapear estrutura** - Saber onde está cada coisa
2. **Definir convenções** - Namespace, localização, padrões
3. **Setup de dev** - Ambiente funcional antes de codar
4. **Estratégia de upgrade** - Como não quebrar com updates do Krayin
5. **Testes mínimos** - Pelo menos caminho feliz

### Top 5 Erros a Evitar

1. **Criar arquivos no lugar errado** - Verificar 3x antes
2. **Duplicar código** - Um arquivo por responsabilidade
3. **Ignorar cache** - Sempre invalidar após alterações
4. **Não testar** - Pelo menos teste manual documentado
5. **Não documentar** - Próximo dev (ou você em 3 meses) vai sofrer

### Regra de Ouro

> **"Se você não sabe onde algo deve ficar, PARE e pergunte antes de criar."**

Criar no lugar errado custa 10x mais para corrigir do que perguntar antes.

---

*Documento criado em: 2025-12-27*  
*Baseado em: Experiência real do projeto BrandKit*  
*Última atualização: 2025-12-27*
