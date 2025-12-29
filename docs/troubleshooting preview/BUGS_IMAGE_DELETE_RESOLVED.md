=================================================================================
# DOCUMENTAÇÃO DE BUGS RESOLVIDOS - IMAGE DELETE & PREVIEW
=================================================================================
Data: 29/12/2025
Package: ThemeManager v1.0.0
=================================================================================

## SUMÁRIO DE BUGS CORRIGIDOS

| Bug # | Descrição | Status |
|-------|-----------|--------|
| 1 | Logo/Favicon delete não funcionava | ✅ RESOLVIDO |
| 2 | Login background delete não funcionava | ✅ RESOLVIDO |
| 3 | Login card overlay aparecia quando imagem deletada | ✅ RESOLVIDO |
| 4 | Image previews gigantes no admin | ✅ RESOLVIDO |
| 5 | CSS variables de login não eram geradas | ✅ RESOLVIDO |

=================================================================================

## BUG #1 & #2: DELETE DE IMAGENS NÃO FUNCIONAVA

### PROBLEMA
Ao marcar checkbox "Remover imagem atual" e salvar, a imagem não era removida
do banco de dados. O valor continuava no campo ao invés de ficar NULL.

### CAUSA RAIZ
O Repository não processava os campos `*_delete` corretamente, especialmente
quando o usuário mudava de tema ao mesmo tempo.

### SOLUÇÃO

**Arquivo:** `ThemeConfigRepository.php`

```php
// ANTES: Campos de delete eram perdidos ao mudar de tema

// DEPOIS: Capturar campos de delete ANTES de carregar o novo tema
$deleteFields = array_filter($data, function ($key) {
    return str_ends_with($key, '_delete');
}, ARRAY_FILTER_USE_KEY);

// Carregar configurações do novo tema
$themeSettings = $this->loadThemeSettings($newTheme, $data);

// Restaurar os campos de delete para processamento posterior
$data = array_merge($themeSettings, $formDataToKeep);
$data = array_merge($data, $deleteFields);
```

**Arquivo:** `loadThemeSettings()` - Respeitar flags de delete ao copiar assets

```php
foreach ($fileFields as $field) {
    // Se o usuário marcou para deletar, não copiar o asset do tema
    if (!empty($originalData["{$field}_delete"])) {
        Log::info('[Theme] Skipping asset copy due to delete flag', ['field' => $field]);
        $settings[$field] = null; // Garantir que será null no banco
        continue;
    }
    // ... copiar asset normalmente
}
```

=================================================================================

## BUG #3: OVERLAY DO LOGIN CARD APARECIA SEM IMAGEM

### PROBLEMA
Após deletar a imagem de fundo do login card, o overlay colorido (::before)
continuava aparecendo, criando uma camada semi-transparente sobre o card branco.

### CAUSA RAIZ
O CSS do ::before era sempre renderizado, sem verificar se havia imagem.

### SOLUÇÃO

**Arquivo:** `login.blade.php` (linhas 232-247)

```blade
.login-card-custom::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    @if($loginConfig['card_bg_image'])
        background-color: rgba(255, 255, 255, calc(1 - var(--card-bg-opacity, 0.62)));
        @if($loginConfig['card_overlay_color'])
            background: {{ $loginConfig['card_overlay_color'] }};
        @endif
    @else
        display: none; /* CORREÇÃO: Sem overlay quando não há imagem */
    @endif
}
```

**Arquivo:** `login.blade.php` (linhas 223-230) - Card background

```blade
.login-card-custom {
    position: relative;
    @if($loginConfig['card_bg_image'])
        background-image: url('{{ $loginConfig['card_bg_image'] }}');
        background-size: cover;
        background-position: center;
    @else
        background: white; /* CORREÇÃO: Sem imagem quando NULL */
    @endif
}
```

=================================================================================

## BUG #4: PREVIEWS DE IMAGEM GIGANTES NO ADMIN

### PROBLEMA
Ao fazer upload de imagens grandes, os previews ocupavam toda a largura da tela
no painel admin (/admin/settings/theme), quebrando o layout.

### CAUSA RAIZ
Os containers de preview usavam `w-full` (100% largura) sem limitação adequada.

### SOLUÇÃO

**Arquivo:** `index.blade.php` - Formato thumbnail compacto

```blade
{{-- ANTES: Ocupava largura toda --}}
<div class="w-full max-w-sm h-16 overflow-hidden ...">

{{-- DEPOIS: Thumbnail compacto (128px largura) --}}
<div class="flex items-center justify-center h-16 w-32 overflow-hidden rounded border ...">
    <img src="..."
         class="max-h-full max-w-full object-contain"
         style="max-height: 60px !important; max-width: 120px !important;">
</div>
```

### DIMENSÕES PADRONIZADAS

| Elemento | Container | Max Size | object-fit |
|----------|-----------|----------|------------|
| Main Logo | `w-32 h-16` | 120×60px | contain |
| Light Logo | `w-32 h-16` | 120×60px | contain |
| Logo Icon | `w-16 h-16` | 64×64px | contain |
| Favicon | `w-8 h-8` | 32×32px | contain |
| Login BG | `w-32 h-20` | 128×80px | cover |
| Card BG | `w-32 h-20` | 128×80px | cover |

=================================================================================

## BUG #5: CSS VARIABLES DE LOGIN NÃO ERAM GERADAS

### PROBLEMA
As variáveis CSS de login (zoom, opacity, url) não eram incluídas no :root,
impossibilitando uso de variáveis CSS para estilização dinâmica.

### CAUSA RAIZ
O método `getCssVariables()` no ThemeHelper só gerava variáveis de cor.

### SOLUÇÃO

**Arquivo:** `ThemeHelper.php` (linhas 175-187)

```php
public function getCssVariables()
{
    // ... variáveis de cor existentes ...

    // ADICIONADO: Login background variables (only if image exists)
    if (!empty($config->login_bg_image)) {
        $variables['--theme-login-bg-url'] = "url('" . asset('storage/theme-manager/' . $config->login_bg_image) . "')";
        $variables['--theme-login-bg-opacity'] = max(0, min(100, (int) $config->login_bg_opacity)) / 100;
        $variables['--theme-login-bg-zoom'] = max(50, min(200, (int) $config->login_bg_zoom)) / 100;
    }

    // ADICIONADO: Login card background variables (only if image exists)
    if (!empty($config->login_card_bg_image)) {
        $variables['--theme-login-card-bg-url'] = "url('" . asset('storage/theme-manager/' . $config->login_card_bg_image) . "')";
        $variables['--theme-login-card-bg-opacity'] = max(0, min(100, (int) $config->login_card_bg_opacity)) / 100;
        $variables['--theme-login-card-overlay'] = $this->sanitizeRgbaColor($config->login_card_overlay_color, 'rgba(10, 45, 15, 0.78)');
    }

    // ... resto do método ...
}
```

### VARIÁVEIS CSS GERADAS

Quando há imagem de login background:
```css
:root {
    --theme-login-bg-url: url('/storage/theme-manager/...');
    --theme-login-bg-opacity: 0.5;  /* 0-1 normalizado */
    --theme-login-bg-zoom: 1;       /* 0.5-2 normalizado */
}
```

Quando há imagem de login card:
```css
:root {
    --theme-login-card-bg-url: url('/storage/theme-manager/...');
    --theme-login-card-bg-opacity: 0.62;
    --theme-login-card-overlay: rgba(10, 45, 15, 0.78);
}
```

=================================================================================

## ARQUIVOS MODIFICADOS

### Core Files
1. `ThemeConfigRepository.php` - Delete flag handling
2. `ThemeHelper.php` - CSS variables generation

### View Files
3. `login.blade.php` - Conditional overlay/background
4. `theme-styles.blade.php` - @else blocks for NULL handling
5. `index.blade.php` - Thumbnail preview sizing

=================================================================================

## PADRÃO: TRATAMENTO DE NULL EM IMAGENS

### Blade Template Pattern

```blade
@if($config->image_field)
    {{-- Renderizar com a imagem --}}
    <div style="background-image: url('{{ asset(...) }}');">
        <div class="overlay">...</div>
    </div>
@else
    {{-- Renderizar SEM imagem e SEM overlay --}}
    <div style="background: #f3f4f6;">
        {{-- Overlay com display: none --}}
    </div>
@endif
```

### JavaScript Pattern

```javascript
@if($config->image_field)
    // Aplicar imagem
    element.style.backgroundImage = 'url(...)';
    // Criar overlay
    var overlay = document.createElement('div');
    // ...
@else
    // REMOVER qualquer background/overlay existente
    element.style.backgroundImage = 'none';
    element.style.backgroundColor = '#f3f4f6';
    var existingOverlay = element.querySelector('.overlay');
    if (existingOverlay) existingOverlay.remove();
@endif
```

### CSS Pattern

```css
@if($config->image_field)
    .element::before {
        content: '';
        background: var(--overlay-color);
        /* ... overlay styles ... */
    }
@else
    .element::before {
        display: none; /* CRÍTICO: Esconder quando sem imagem */
    }
@endif
```

=================================================================================

## TESTE DE VERIFICAÇÃO

### Checklist Manual

1. [ ] Upload de logo grande → Preview fica em 128×64px
2. [ ] Marcar "Remover" + Salvar → Campo fica NULL no banco
3. [ ] Login sem bg_image → Fundo cinza neutro (#f3f4f6)
4. [ ] Login card sem bg_image → Card branco, sem overlay
5. [ ] Mudar tema + Marcar delete → Delete é respeitado
6. [ ] DevTools > :root → Variáveis de login aparecem (quando há imagem)

### Comandos de Limpeza

```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
Remove-Item storage\framework\views\* -Force -ErrorAction SilentlyContinue
```

=================================================================================

## LIÇÕES APRENDIDAS

1. **Flags de delete** devem ser preservados mesmo ao mudar de tema
2. **Overlays CSS** devem ter `display: none` quando não há imagem
3. **Previews de imagem** precisam de containers com dimensões fixas
4. **CSS variables** só devem ser geradas quando o recurso existe
5. **@else blocks** são essenciais para limpeza de estados visuais

=================================================================================

Documentação criada em: 29/12/2025
Última atualização: 29/12/2025
