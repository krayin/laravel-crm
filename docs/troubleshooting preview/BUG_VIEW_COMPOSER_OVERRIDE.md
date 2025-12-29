# Bug Resolvido: View Composer Sobrescrevendo Dados do Controller

**Data**: Dezembro 2025
**Severidade**: Alta
**Tempo de Debug**: ~2 horas
**Status**: RESOLVIDO

---

## Resumo Executivo

View Composers em Laravel rodam **DEPOIS** do controller e podem sobrescrever variáveis já passadas para a view. Dois providers (`AppServiceProvider` e `ThemeBootProvider`) estavam sobrescrevendo `$availableThemes` com uma versão incompleta (sem cores), anulando os dados corretos do `ThemeController`.

---

## Sintomas Observados

1. Cards de tema mostravam todas as cores iguais (azul padrão #1E40AF)
2. DEBUG na view mostrava `colors: NOT SET` para todos os temas
3. Arquivos `theme.json` tinham cores diferentes e estavam sendo lidos corretamente
4. `ThemeController::getAvailableThemes()` retornava dados corretos com `colors`

---

## Investigação

### Passo 1: Verificar se Controller estava correto
```php
// ThemeController::getAvailableThemes() - ESTAVA CORRETO
$themes[$slug] = [
    'slug'   => $slug,
    'name'   => $themeData['name'],
    'colors' => [
        'primary' => $themeData['color_primary'] ?? '#1E40AF',
        // ... outras cores
    ],
];
```

### Passo 2: Adicionar DEBUG na view
```blade
@foreach($availableThemes as $theme)
    {{ $theme['slug'] }}: colors={{ isset($theme['colors']) ? 'YES' : 'NO' }}
@endforeach
```
**Resultado**: `colors: NO` para todos - dados estavam sendo sobrescritos.

### Passo 3: Buscar sobrescritas
```bash
grep -R "availableThemes" -n app packages
grep -R "View::composer" -n app packages
```

### Passo 4: Encontrar os culpados
```
app/Providers/AppServiceProvider.php:54 - View::composer (comentado)
app/Providers/ThemeBootProvider.php:68 - View::composer (ATIVO!)
```

---

## Causa Raiz

### Arquivo: `app/Providers/ThemeBootProvider.php`

```php
// PROBLEMA: View Composer sobrescrevia $availableThemes
private function registerViewComposers(): void
{
    View::composer('theme-manager::admin.settings.theme.index', function ($view) {
        // Este método NÃO incluía 'colors'!
        $view->with('availableThemes', $this->getAvailableThemes());
    });
}

// Método que retornava dados INCOMPLETOS
private function discoverThemes(): array
{
    $themes[] = [
        'slug'        => $slug,
        'name'        => $themeData['name'],
        'description' => $themeData['description'],
        // 'colors' => [...] // FALTAVA ISSO!
    ];
}
```

### Fluxo do Bug

```
┌─────────────────────────────────────────────────────────────┐
│ 1. ThemeController::index()                                 │
│    $availableThemes = $this->getAvailableThemes()           │
│    ↓ (CORRETO - com colors)                                 │
│    return view(..., compact('availableThemes'))             │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. Laravel prepara a view                                   │
│    $availableThemes tem colors ✓                            │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. ThemeBootProvider::registerViewComposers() EXECUTA       │
│    $view->with('availableThemes', $this->discoverThemes())  │
│    ↓ (SEM colors - SOBRESCREVE!)                            │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. View recebe array SEM colors                             │
│    Círculos usam fallback #1E40AF (azul padrão)             │
└─────────────────────────────────────────────────────────────┘
```

---

## Solução Aplicada

### Princípio: Fonte Única de Verdade

**Não** duplicar a lógica de `colors` no `ThemeBootProvider`. Em vez disso, **remover** o View Composer que sobrescrevia.

### Correção em `app/Providers/ThemeBootProvider.php`

```php
// ANTES (ERRADO)
private function registerViewComposers(): void
{
    View::composer('theme-manager::admin.settings.theme.index', function ($view) {
        $view->with('availableThemes', $this->getAvailableThemes());
    });
}

// DEPOIS (CORRETO)
private function registerViewComposers(): void
{
    // REMOVIDO: View Composer que sobrescrevia $availableThemes
    // O ThemeController::getAvailableThemes() já fornece dados completos.
    // Manter uma única fonte de verdade evita bugs de sincronização.
}
```

### Por que NÃO adicionar `colors` no `discoverThemes()`?

1. **Duplicação de código** - Mesma lógica em dois lugares
2. **Sincronização** - Mudanças futuras precisariam ser feitas em ambos
3. **Anti-pattern** - Duas fontes de verdade para o mesmo dado
4. **Princípio KISS** - Solução mais simples é remover a sobrescrita

---

## Comandos de Debug Utilizados

```bash
# Buscar todas as referências a availableThemes
grep -R "availableThemes" -n app packages

# Buscar View Composers
grep -R "View::composer" -n app packages

# Buscar view()->share()
grep -R "view()->share" -n app packages

# Limpar caches (ESSENCIAL após mudanças em Providers)
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan optimize:clear
```

---

## Debug na View

```blade
<!-- Adicionar temporariamente para diagnosticar -->
<div style="background: #dc2626; color: white; padding: 20px; font-family: monospace;">
    @foreach($availableThemes as $idx => $theme)
        <strong>{{ $theme['slug'] }}</strong>:
        colors={{ isset($theme['colors']) ? 'YES' : 'NO' }}
        @if(isset($theme['colors']['primary']))
            | primary={{ $theme['colors']['primary'] }}
        @endif
        <br>
    @endforeach
</div>
```

---

## Lições Aprendidas

### 1. View Composers são Perigosos
View Composers em Laravel rodam **DEPOIS** do controller e podem sobrescrever qualquer variável. Use com cautela.

### 2. Sempre Usar grep
Quando uma variável não está chegando como esperado, use grep para encontrar **todas** as fontes possíveis.

### 3. Fonte Única de Verdade
Nunca ter a mesma variável sendo preenchida em múltiplos lugares. Escolha UM local canônico.

### 4. Cache é Agressivo
Providers são cacheados. Sempre limpar cache após mudanças:
```bash
php artisan optimize:clear
```

### 5. Logs são Essenciais
Adicionar `\Log::info()` temporários ajuda a rastrear qual código está executando.

---

## Checklist para Problemas Similares

- [ ] O controller está passando a variável corretamente?
- [ ] Existe algum View Composer sobrescrevendo? (`grep "View::composer"`)
- [ ] Existe algum `view()->share()` global?
- [ ] O cache foi limpo completamente?
- [ ] O arquivo correto está sendo editado (packages vs vendor)?
- [ ] Os logs mostram qual código está executando?

---

## Arquivos Modificados

| Arquivo | Mudança |
|---------|---------|
| `app/Providers/ThemeBootProvider.php` | Removido View Composer que sobrescrevia `$availableThemes` |
| `app/Providers/AppServiceProvider.php` | Já estava comentado (não era o ativo) |
| `packages/.../ThemeController.php` | Fonte de verdade para `$availableThemes` (mantido) |

---

## Referências

- [Laravel View Composers](https://laravel.com/docs/views#view-composers)
- [Laravel Service Providers](https://laravel.com/docs/providers)
- [CLAUDE.md - Regras do Projeto](../../CLAUDE.md)
