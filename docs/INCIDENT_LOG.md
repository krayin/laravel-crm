# Incident Log - Projeto BrandKit/Krayin

Registro de incidentes reais ocorridos durante o desenvolvimento, com causa raiz, correção aplicada e lições aprendidas.

---

## Incidente #001 - Arquivos Criados no Diretório Errado

**Data:** 2025-12-27  
**Severidade:** CRÍTICA  
**Status:** Identificado, pendente correção

### Contexto
Durante as Fases B, C e Micro-Fase 7, foram criados arquivos de templates, composer packages e controllers.

### Log do Problema
```bash
# Estrutura encontrada (INCORRETA):
C:\Users\Usuario\Desktop\Krayin-\
├── .git/                          # Git init feito AQUI (errado)
├── docs/templates/                # Templates criados AQUI (errado)
├── app/Http/Requests/...          # Requests criados AQUI (errado)
├── app/Http/Controllers/...       # Controller criado AQUI (errado)
├── composer.json                  # Packages instalados AQUI (errado)
└── laravel-crm/                   # PROJETO REAL está aqui
    ├── .git/                      # Repo Git CORRETO
    └── app/...                    # Código fonte REAL
```

### Causa Raiz
- O comando `mkdir -p` e `git init` foram executados no diretório raiz (`Krayin-/`) em vez de dentro de `laravel-crm/`
- Não foi verificado se já existia um projeto Laravel antes de criar a estrutura
- Falta de verificação inicial do estado do repositório

### Evidência
```bash
$ ls -la laravel-crm/.git
# Mostra que laravel-crm já tinha .git próprio

$ git remote -v  # No diretório raiz
# (vazio)

$ cd laravel-crm && git remote -v
# origin  https://github.com/krayin/laravel-crm.git
# myfork  https://github.com/vitorbb1989/Krayingproject.git
```

### Correção Necessária
```bash
# 1. Mover templates para o local correto
mv docs/templates laravel-crm/docs/

# 2. Mover Form Requests
mv app/Http/Requests/Admin/BrandKit laravel-crm/app/Http/Requests/Admin/

# 3. Remover arquivos duplicados do diretório raiz
rm -rf app/ docs/ vendor/ composer.json composer.lock

# 4. Commits devem ser feitos em laravel-crm/
cd laravel-crm && git add . && git commit -m "fix: move files to correct location"
```

### Lições Aprendidas
1. **SEMPRE** verificar `git remote -v` antes de iniciar trabalho
2. **SEMPRE** verificar se existe `artisan` e `.env` no diretório
3. Perguntar ao usuário qual é o diretório raiz do projeto

### Commit/PR
- Pendente

---

## Incidente #002 - Duplicidade de Controllers BrandKit

**Data:** 2025-12-27  
**Severidade:** ALTA  
**Status:** Identificado, pendente decisão

### Contexto
Existem dois controllers com o mesmo nome mas funcionalidades diferentes.

### Log do Problema
```bash
$ rg -n "class\s+BrandKitController" laravel-crm/app

app/Http/Controllers/Admin/BrandKit/BrandKitController.php:8:final class BrandKitController
app/Http/Controllers/BrandKitController.php:10:class BrandKitController
```

### Comparação

| Aspecto | Admin/BrandKit/BrandKitController | BrandKitController (raiz) |
|---------|-----------------------------------|---------------------------|
| Namespace | `Admin\BrandKit` | `App\Http\Controllers` |
| Métodos | 1 (index stub) | 17 (completo) |
| Injeção | Nenhuma | Repository + Resolver |
| Status | Stub/placeholder | **Funcional** |

### Causa Raiz
- Desenvolvimento em paralelo sem coordenação
- Falta de documentação sobre qual controller é o "oficial"
- Rotas duplicadas apontando para controllers diferentes

### Evidência - Controller Stub (24 linhas)
```php
// app/Http/Controllers/Admin/BrandKit/BrandKitController.php
final class BrandKitController extends Controller
{
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(["ok" => true, "message" => "Brand Kit UI wiring OK"]);
        }
        return view("admin::brandkit.index");
    }
}
```

### Evidência - Controller Completo (350+ linhas)
```php
// app/Http/Controllers/BrandKitController.php
class BrandKitController extends Controller
{
    public function __construct(
        private BrandKitRepository $repository,
        private BrandKitResolver $resolver,
    ) {}
    
    // 17 métodos: config, themes, overrides, storeOverride, batchOverrides,
    // deleteOverride, customCss, storeCss, toggleCss, deleteCss, snapshots,
    // createSnapshot, restoreSnapshot, deleteSnapshot, reset, preview, invalidateCache
}
```

### Correção Recomendada
```bash
# Opção A: Usar controller completo, remover stub
rm laravel-crm/app/Http/Controllers/Admin/BrandKit/BrandKitController.php

# Opção B: Mover controller completo para Admin/BrandKit
mv laravel-crm/app/Http/Controllers/BrandKitController.php \
   laravel-crm/app/Http/Controllers/Admin/BrandKit/
# Atualizar namespace e rotas
```

### Lições Aprendidas
1. Definir convenção de namespace ANTES de criar controllers
2. Documentar qual arquivo é o "source of truth"
3. Usar `php artisan route:list` para verificar conflitos

### Commit/PR
- Pendente decisão do dev lead

---

## Incidente #003 - Duplicidade de Rotas BrandKit

**Data:** 2025-12-27  
**Severidade:** ALTA  
**Status:** Identificado, pendente decisão

### Contexto
Existem dois arquivos de rotas definindo endpoints para BrandKit.

### Log do Problema
```bash
$ rg -n "brand-kit|brandkit" laravel-crm/routes

routes/web.php:61:  Route::prefix("settings/brand-kit")
routes/web.php:62:      ->name("admin.brandkit.")
routes/brand-kit.php:19:  Route::prefix('brand-kit')
routes/brand-kit.php:20:      ->name('admin.brand-kit.')
```

### Comparação

| Aspecto | routes/web.php | routes/brand-kit.php |
|---------|----------------|----------------------|
| Prefixo | `/admin/settings/brand-kit` | `/admin/brand-kit` |
| Name prefix | `admin.brandkit.` | `admin.brand-kit.` |
| Controller | `Admin\BrandKit\BrandKitController` | `BrandKitController` |
| Endpoints | 9 (parcial) | 15 (completo) |
| Middleware | `BrandKitPermission` (comentado) | Nenhum específico |

### Causa Raiz
- Dois desenvolvedores trabalhando em paralelo
- Falta de convenção definida para naming de rotas
- Arquivo `routes/brand-kit.php` não foi removido quando `routes/web.php` foi atualizado

### Impacto
- Confusão sobre qual URL usar
- Possíveis conflitos de rota
- Manutenção duplicada

### Correção Recomendada
```bash
# Escolher UM arquivo e remover o outro
# Recomendação: manter routes/brand-kit.php (mais completo)

# 1. Remover rotas duplicadas de web.php
# 2. Ou remover routes/brand-kit.php e completar web.php

# Atualizar RouteServiceProvider se necessário
```

### Lições Aprendidas
1. Um endpoint = um lugar
2. Usar arquivos de rota separados só quando necessário (ex: API vs Web)
3. Documentar decisões de arquitetura de rotas

### Commit/PR
- Pendente decisão do dev lead

---

## Incidente #004 - Inconsistência Container vs Controller (Repository)

**Data:** 2025-12-27  
**Severidade:** ALTA  
**Status:** Identificado, pendente correção

### Contexto
O container IoC registra um Repository, mas o Controller usa outro.

### Log do Problema
```php
// AppServiceProvider.php - REGISTRA:
$this->app->singleton(\App\Repositories\BrandKitRepository::class);

// BrandKitController.php - USA:
use App\Support\BrandKitRepository;
```

### Arquivos Existentes
```bash
$ ls -la laravel-crm/app/Repositories/BrandKitRepository.php
# Existe - 149 linhas

$ ls -la laravel-crm/app/Support/BrandKitRepository.php  
# Existe - 450 linhas (mais completo)
```

### Comparação de Métodos

| Método | Repositories/ | Support/ |
|--------|---------------|----------|
| getOverrides() | ❌ | ✅ |
| setOverride() | ✅ | ✅ |
| setOverrides() (bulk) | ❌ | ✅ |
| deleteOverride() | ❌ | ✅ |
| getCustomCss() | ❌ | ✅ |
| setCustomCss() | ❌ | ✅ |
| getSnapshots() | ❌ | ✅ |
| deleteSnapshot() | ❌ | ✅ |
| resetAll() | ❌ | ✅ |
| previewConfig() | ❌ | ✅ |

### Causa Raiz
- Refatoração incompleta
- Falta de interface/contrato definido
- AppServiceProvider não foi atualizado após mover o repository

### Correção Necessária
```php
// Opção A: Atualizar AppServiceProvider
$this->app->singleton(\App\Support\BrandKitRepository::class);

// Opção B: Criar interface e bind
$this->app->bind(
    \App\Contracts\BrandKitRepositoryInterface::class,
    \App\Support\BrandKitRepository::class
);
```

### Lições Aprendidas
1. Usar interfaces para repositories
2. Atualizar ServiceProvider ao mover classes
3. Rodar testes após refatoração

### Commit/PR
- Pendente

---

## Incidente #005 - Snapshot ID: Integer vs Filename

**Data:** 2025-12-27  
**Severidade:** MÉDIA  
**Status:** Corrigido

### Contexto
O RestoreSnapshotRequest validava snapshot como filename (.json) mas o banco usa ID inteiro.

### Log do Problema (ANTES)
```php
// RestoreSnapshotRequest.php - ANTES
public function rules(): array
{
    return [
        'snapshot_id' => [
            'required',
            'string',
            'regex:/^snapshot_\d{8}_\d{6}\.json$/',
        ],
    ];
}
```

### Causa Raiz
- Design inicial previa snapshots em arquivo
- Mudança para banco de dados não atualizou Request
- Falta de sincronização entre camadas

### Correção Aplicada
```php
// RestoreSnapshotRequest.php - DEPOIS
public function rules(): array
{
    return [
        'snapshot_id' => [
            'required',
            'integer',
            'exists:brand_kit_snapshots,id',
        ],
    ];
}
```

### Commit
```
74f54ad fix(brandkit): ajustes Micro-Fase 7 conforme review

Alterações:
- RestoreSnapshotRequest: snapshot_id agora é integer + exists:brand_kit_snapshots,id
- SetOverrideRequest: value agora é nullable (permite limpar override)
- AddCustomCssRequest: reforço segurança - bloqueia @import, @charset, url(javascript:)
```

### Lições Aprendidas
1. Validação deve refletir schema do banco
2. Usar `exists:table,column` para FKs
3. Revisar Requests quando mudar persistência

---

## Incidente #006 - SetOverrideRequest.value Required (Bloqueava Limpeza)

**Data:** 2025-12-27  
**Severidade:** MÉDIA  
**Status:** Corrigido

### Contexto
O campo `value` era `required`, impedindo limpar um override (setar para null/vazio).

### Log do Problema (ANTES)
```php
// SetOverrideRequest.php - ANTES
'value' => ['required', 'string', 'max:1000'],
```

### Impacto
- Impossível "resetar" um override para valor padrão via mesmo endpoint
- Necessitava endpoint separado para reset

### Correção Aplicada
```php
// SetOverrideRequest.php - DEPOIS
'value' => ['nullable', 'string', 'max:1000'],
```

### Commit
```
74f54ad fix(brandkit): ajustes Micro-Fase 7 conforme review
```

### Lições Aprendidas
1. Considerar casos de "limpar/remover" no design de API
2. `nullable` é diferente de `sometimes`
3. Documentar comportamento de null no endpoint

---

## Incidente #007 - CSS Custom Sem Escopo (Risco de Segurança)

**Data:** 2025-12-27  
**Severidade:** MÉDIA  
**Status:** Identificado, pendente implementação

### Contexto
O CSS customizado é injetado diretamente no `<head>` sem wrapper de escopo.

### Log do Problema
```blade
{{-- theme-head.blade.php --}}
@if (!empty($customCss))
<style id="brand-kit-custom-css">{!! $customCss !!}</style>
@endif
```

### Impacto
- CSS do usuário pode afetar TODO o admin
- Possível quebrar layout do sistema
- Embora validado (CssValidator), ainda permite seletores globais

### Correção Recomendada
```blade
{{-- Opção A: Wrapper no HTML --}}
<div id="brand-kit-scope">
    @yield('content')
</div>

{{-- CSS seria prefixado automaticamente ou manual --}}
<style id="brand-kit-custom-css">
#brand-kit-scope {
    {!! $customCss !!}
}
</style>
```

```php
// Opção B: Prefixar no CssValidator
public function prefixSelectors(string $css, string $prefix = '#brand-kit-scope'): string
{
    // Implementar parser para adicionar prefixo a cada seletor
}
```

### Lições Aprendidas
1. CSS de usuário deve ser isolado
2. Considerar usar Shadow DOM ou iframe para isolamento total
3. Validação não é suficiente, precisa de contenção

### Commit/PR
- Pendente implementação

---

## Incidente #008 - Composer no PATH (Windows/Git Bash)

**Data:** 2025-12-27  
**Severidade:** BAIXA  
**Status:** Resolvido (workaround)

### Contexto
Comandos `composer` não funcionavam no Git Bash do Windows.

### Log do Problema
```bash
$ composer require krayin/krayin-package-generator
bash: composer: command not found

$ where composer
C:\php\composer.bat

$ C:\php\composer.bat require ...
bash: C:phpcomposer.bat: command not found
```

### Causa Raiz
- Git Bash não reconhece paths Windows com backslash
- Precisa usar paths Unix-style (`/c/php/composer.bat`)

### Correção Aplicada
```bash
# Usar path Unix-style no Git Bash
/c/php/composer.bat require krayin/krayin-package-generator
```

### Lições Aprendidas
1. No Git Bash, usar `/c/` em vez de `C:\`
2. Ou usar `cmd /c "comando"` para executar via CMD
3. Documentar ambiente de desenvolvimento

---

## Incidente #009 - Blade Tracer Package Não Encontrado

**Data:** 2025-12-27  
**Severidade:** BAIXA  
**Status:** Resolvido

### Contexto
O package sugerido `beyondcode/laravel-blade-tracer` não existe.

### Log do Problema
```bash
$ composer require --dev beyondcode/laravel-blade-tracer

Could not find a matching version of package beyondcode/laravel-blade-tracer
```

### Causa Raiz
- Nome do package incorreto na documentação/sugestão
- Package real é do próprio Krayin

### Correção Aplicada
```bash
# Package correto
$ composer search blade tracer
krayin/krayin-blade-tracer    # <- Este é o correto

$ composer require --dev krayin/krayin-blade-tracer:@dev
```

### Commit
```
4eb8692 chore(dev): add krayin package generator and blade tracer
```

### Lições Aprendidas
1. Verificar existência do package antes de documentar
2. Usar `composer search` para encontrar packages similares
3. Packages em dev podem precisar de `@dev` stability

---

## Resumo Estatístico

| Severidade | Total | Resolvidos | Pendentes |
|------------|-------|------------|-----------|
| CRÍTICA | 1 | 0 | 1 |
| ALTA | 3 | 0 | 3 |
| MÉDIA | 4 | 2 | 2 |
| BAIXA | 2 | 2 | 0 |
| **TOTAL** | **10** | **4** | **6** |

---

## Ações Pendentes (Prioridade)

1. **[CRÍTICO]** Mover arquivos de `Krayin-/` para `Krayin-/laravel-crm/`
2. **[ALTA]** Escolher e consolidar 1 Controller
3. **[ALTA]** Escolher e consolidar 1 arquivo de rotas
4. **[ALTA]** Corrigir AppServiceProvider (Repository binding)
5. **[MÉDIA]** Implementar escopo CSS (#brand-kit-scope)
6. **[MÉDIA]** Adicionar snapshot automático em toggleCss e deleteCss

---

## Anexo: Commits Relacionados

```
74f54ad fix(brandkit): ajustes Micro-Fase 7 conforme review
74df185 feat(brandkit): add Form Requests and Controller with auto-snapshots
4eb8692 chore(dev): add krayin package generator and blade tracer
5b0ea34 docs: add templates for package README and quality gates
```

---

*Documento gerado em: 2025-12-27*  
*Última atualização: 2025-12-27*
