# Contrato de Precedência do Sistema de Temas

Este documento define as regras invioláveis de como valores de configuração são resolvidos no sistema de temas do Krayin CRM.

> **Este é o SLA interno de comportamento.** Qualquer alteração aqui requer atualização dos testes.

---

## Camadas de Configuração

O sistema resolve valores através de 5 camadas, em ordem de precedência (menor → maior):

```
┌─────────────────────────────────────────────────────────────┐
│  5. CLEAR (override com value=null)                         │  ← Maior precedência
│     Remove valor, volta para DEFAULTS                       │
├─────────────────────────────────────────────────────────────┤
│  4. brand_kit_overrides (tabela)                            │
│     Overrides específicos por scope+tema                    │
├─────────────────────────────────────────────────────────────┤
│  3. theme_configs (tabela ThemeManager)                     │
│     Configurações do admin (cores, logos, login)            │
├─────────────────────────────────────────────────────────────┤
│  2. theme.json (arquivo do tema)                            │
│     Preset do tema selecionado                              │
├─────────────────────────────────────────────────────────────┤
│  1. DEFAULTS (constantes no código)                         │  ← Menor precedência
│     Fallback absoluto                                       │
└─────────────────────────────────────────────────────────────┘
```

---

## Regras de Merge

### Valor Real
Um valor é considerado "real" e entra no merge quando:
- NÃO é `null`
- NÃO é string vazia `""`
- `false` e `0` SÃO valores reais (entram no merge)

### Fluxo de Resolução

```php
// Pseudocódigo do BrandKitResolver::resolve()

1. Carregar DEFAULTS (constantes)
2. Carregar theme.json do tema selecionado
3. Carregar theme_configs do banco (ThemeManager)
4. Carregar brand_kit_overrides (com CLEARs separados)

5. Merge:
   resultado = DEFAULTS

   para cada valor em theme.json:
       se é valor real: resultado[key] = valor

   para cada valor em theme_configs:
       se é valor real: resultado[key] = valor

   para cada valor em overrides:
       se é valor real: resultado[key] = valor

   para cada key em clears:
       resultado[key] = DEFAULTS[key] ?? null
```

---

## Clear Semantics (Semântica de "Limpar")

### Definição

CLEAR é a capacidade de **remover explicitamente** um valor customizado e **voltar para o default**.

### Quando é CLEAR?

| is_active | value | Comportamento |
|-----------|-------|---------------|
| `false` | qualquer | Ignorado (como se não existisse) |
| `true` | `null` | **CLEAR** - volta para default |
| `true` | `""` | **CLEAR** - volta para default |
| `true` | `"valor"` | Override com esse valor |

### Exemplos de CLEAR

#### Exemplo 1: Limpar cor primária para voltar ao padrão

```php
// Situação: tema tem cor roxa, admin quer voltar ao azul padrão
BrandKitOverride::setOverride(
    scopeKey: 'global',
    themeSlug: 'purple-theme',
    overrideKey: 'color_primary',
    value: null,  // ← CLEAR
);

// Resultado:
// theme.json:  color_primary = "#7C3AED" (roxo)
// CLEAR:       color_primary = null
// FINAL:       color_primary = "#1E40AF" (azul default)
```

#### Exemplo 2: CLEAR ignora theme_configs

```
DEFAULTS:          color_primary = "#1E40AF"
theme.json:        color_primary = "#7C3AED"
theme_configs:     color_primary = "#DC2626"  ← Seria aplicado...
overrides:         color_primary = CLEAR       ← ...mas CLEAR ignora tudo

RESULTADO:         color_primary = "#1E40AF"  ← Volta para DEFAULTS
```

#### Exemplo 3: Diferença entre CLEAR e Remover

```php
// CLEAR: "Quero explicitamente usar o default"
BrandKitOverride::setOverride('global', 'tema', 'color_primary', null);
// → Cria registro com is_active=true, value=null
// → Resultado: color_primary = default

// REMOVER: "Não quero mais ter override nenhum"
BrandKitOverride::removeOverride('global', 'tema', 'color_primary');
// → Marca registro com is_active=false
// → Resultado: color_primary = theme_configs ou theme.json (o que existir)
```

### Casos de Uso para CLEAR

1. **Reset para padrão**: Admin experimentou várias cores e quer voltar ao original
2. **Herança de tema**: Forçar que a cor venha do theme.json default
3. **Rollback parcial**: Limpar um campo específico sem restaurar snapshot inteiro

---

## Custom CSS - Regras

### Target (Alvo)

| Target | Aplicado em |
|--------|-------------|
| `admin` | Apenas no painel admin |
| `login` | Apenas na página de login |
| `both` | Admin **E** login |

### Precedência de CSS

CSS é ordenado por:
1. **priority** (ASC) - menor valor = carrega primeiro
2. **id** (ASC) - em caso de empate, mais antigo primeiro

```sql
-- Query interna
SELECT css_content FROM brand_kit_custom_css
WHERE scope_key = ? AND theme_slug = ? AND is_enabled = true
  AND (target = ? OR target = 'both')
ORDER BY priority ASC, id ASC
```

### Exemplo de Ordem

```
ID | Nome           | Target | Priority | Ordem Final
---|----------------|--------|----------|------------
3  | Reset          | both   | 10       | 1º
1  | Brand Colors   | admin  | 100      | 2º
5  | Header Fixes   | admin  | 100      | 3º (empate: id maior)
2  | Login Theme    | login  | 200      | 4º (só no login)
```

### Combinação de Targets

Para a página **admin**:
```css
/* Carregado nesta ordem: */
/* 1. Reset (target=both, priority=10) */
/* 2. Brand Colors (target=admin, priority=100) */
/* 3. Header Fixes (target=admin, priority=100) */
```

Para a página **login**:
```css
/* 1. Reset (target=both, priority=10) */
/* 2. Login Theme (target=login, priority=200) */
```

---

## Snapshots - O que é capturado

### Conteúdo do Snapshot

Um snapshot captura:

| Tabela | Capturado? | Campos Salvos |
|--------|------------|---------------|
| `brand_kit_overrides` | **SIM** | override_key, value, is_active |
| `brand_kit_custom_css` | **SIM** | name, css_content, target, is_enabled, priority |
| `theme_configs` | **NÃO** | — |

### Por que theme_configs NÃO é capturado?

1. **Separação de responsabilidades**: `theme_configs` é do ThemeManager (cores, logos base)
2. **Escopo diferente**: Snapshot é para overrides/CSS do BrandKit
3. **Simplicidade**: Evita conflitos de merge complexos

### Estrutura do Snapshot (JSON)

```json
{
  "id": 123,
  "scope_key": "global",
  "theme_slug": "purple-theme",
  "name": "Backup antes de mudança",
  "snapshot_version": 1,
  "overrides_data": [
    {
      "override_key": "color_primary",
      "value": "#FF0000",
      "is_active": true
    },
    {
      "override_key": "login_card_title",
      "value": null,
      "is_active": true
    }
  ],
  "custom_css_data": [
    {
      "name": "Brand Colors",
      "css_content": ".header { background: var(--primary); }",
      "target": "admin",
      "is_enabled": true,
      "priority": 100
    }
  ],
  "is_auto": false,
  "created_by": 1,
  "created_at": "2024-12-28T10:00:00Z"
}
```

### Restore Atômico

O restore é **transacional**:

```php
DB::transaction(function() {
    // 1. Cria backup do estado atual (auto-snapshot)
    // 2. DELETE todos overrides do scope/theme
    // 3. INSERT overrides do snapshot
    // 4. DELETE todos CSS do scope/theme
    // 5. INSERT CSS do snapshot
});

// Se qualquer etapa falhar → rollback completo
```

---

## Type Casting e Clamps

### Tipos Suportados

| Tipo | Exemplo | Normalização |
|------|---------|--------------|
| `color` | `#FF0000` | Uppercase, adiciona `#` se ausente |
| `bool` | `true`, `"1"`, `"true"` | Normaliza para bool nativo |
| `int` | `100`, `"100"` | Converte para inteiro |
| `string` | qualquer | Mantém como está |

### Clamps (Limites)

| Campo | Min | Max | Comportamento |
|-------|-----|-----|---------------|
| `login_bg_opacity` | 0 | 100 | Valores fora são clamped |
| `login_card_bg_opacity` | 0 | 100 | Valores fora são clamped |
| `login_bg_zoom` | 50 | 150 | Valores fora são clamped |

```php
// Exemplo: opacity = 150 → vira 100
// Exemplo: zoom = 30 → vira 50
```

---

## Invariantes do Sistema

1. **Imutabilidade do ThemeContext**: Uma vez criado, o contexto é read-only
2. **Cache Versionado**: Keys incluem versão para evitar dados stale
3. **Preview Isolado**: Preview usa session, nunca cache global
4. **CLEAR é Explícito**: Só acontece com `is_active=true` + `value=null`
5. **Defaults Sempre Existem**: Campos core sempre têm fallback
6. **Restore é Atômico**: Ou restaura tudo, ou não restaura nada
7. **CSS Order é Determinístico**: priority ASC, id ASC

---

## Invalidação de Cache

| Evento | Ação |
|--------|------|
| Alteração em `theme_configs` | `ThemeCache::flush()` |
| Alteração em `brand_kit_overrides` | `BrandKitCacheInvalidator::afterBrandKitChange()` |
| Alteração em `brand_kit_custom_css` | `BrandKitCacheInvalidator::afterBrandKitChange()` |
| Troca de tema ativo | `BrandKitCacheInvalidator::afterThemeSwitch()` |
| Restore de snapshot | `BrandKitCacheInvalidator::afterSnapshotRestore()` |

### Regra de Ouro

> **Nunca chame `Cache::forget()` diretamente.** Sempre use `BrandKitCacheInvalidator`.

---

## Testes que Validam este Contrato

```bash
# Precedência e CLEAR
./vendor/bin/phpunit tests/Unit/BrandKitResolverTest.php

# Snapshots e Restore
./vendor/bin/phpunit tests/Unit/BrandKitSnapshotServiceTest.php

# Cache Invalidation
./vendor/bin/phpunit tests/Unit/BrandKitCacheInvalidatorTest.php

# Preview Isolation
./vendor/bin/phpunit tests/Unit/PreviewSessionTest.php
```

---

*Documento gerado em Dezembro 2024 - BrandKit v1.0*
*SLA interno de comportamento - Alterações requerem aprovação*
