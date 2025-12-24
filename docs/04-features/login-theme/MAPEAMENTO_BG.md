# 🗺️ MAPEAMENTO COMPLETO: Login Background

**Data**: 22/12/2024
**Objetivo**: Mapear todos os arquivos que envolvem login_bg e o que cada um faz

---

## 📁 ARQUIVOS ENVOLVIDOS (8 arquivos)

### 1. Migration - Estrutura do Banco
**Arquivo**: `packages/Webkul/ThemeManager/Database/Migrations/2024_12_20_000001_create_theme_configs_table.php`

**Linhas 36-38**: Criação das colunas
```php
$table->string('login_bg_image', 500)->nullable();
$table->integer('login_bg_zoom')->default(100);
$table->integer('login_bg_opacity')->default(50);
```

**Linhas 76-77**: Valores padrão na seed
```php
'login_bg_zoom' => 100,
'login_bg_opacity' => 50,
```

**O que faz**: Define estrutura do banco de dados e valores padrão.

---

### 2. Model - ThemeConfig
**Arquivo**: `packages/Webkul/ThemeManager/src/Models/ThemeConfig.php`

**Linhas 34-36**: Campos fillable
```php
'login_bg_image',
'login_bg_zoom',
'login_bg_opacity',
```

**Linhas 65-66**: Casts de tipo
```php
'login_bg_zoom' => 'integer',
'login_bg_opacity' => 'integer',
```

**Linhas 90-91**: Atributos padrão
```php
'login_bg_zoom' => 100,
'login_bg_opacity' => 50,
```

**O que faz**: Define campos do model, tipos de dados e valores padrão.

---

### 3. Controller - Validação
**Arquivo**: `packages/Webkul/ThemeManager/src/Http/Controllers/ThemeController.php`

**Linhas 74-76**: Regras de validação
```php
'login_bg_image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
'login_bg_zoom' => 'nullable|integer|min:50|max:200',
'login_bg_opacity' => 'nullable|integer|min:0|max:100',
```

**O que faz**:
- `login_bg_image`: Aceita JPG, PNG, WebP até 10MB
- `login_bg_zoom`: 50% a 200%
- `login_bg_opacity`: 0% a 100%

**Linha 103**: Envia dados para Repository
```php
$this->themeConfigRepository->update(array_merge($request->all(), $request->allFiles()));
```

---

### 4. Repository - Upload e Salvamento
**Arquivo**: `packages/Webkul/ThemeManager/src/Repositories/ThemeConfigRepository.php`

**Linha 78**: Campo na lista de uploads
```php
$fileFields = [
    'logo_main',
    'logo_light',
    'logo_icon',
    'favicon',
    'login_bg_image',  ← AQUI
    'login_card_bg_image',
    // ...
];
```

**Linhas 91-132**: Loop de processamento de uploads
```php
foreach ($fileFields as $field) {
    // Se for login_bg_image, processa upload
    if (isset($data[$field]) && $data[$field] instanceof UploadedFile) {
        // Valida extensão
        // Gera filename seguro
        // Salva no storage
        // Atualiza $data[$field] com filename
    }
}
```

**O que faz**: Processa upload de `login_bg_image` e salva no storage.

---

### 5. Helper - Acesso aos Dados
**Arquivo**: `packages/Webkul/ThemeManager/src/Helpers/ThemeHelper.php`

**Método getConfig()**: Retorna configuração com cache
```php
public function getConfig()
{
    return Cache::remember('theme_config', 3600, function() {
        return ThemeConfig::getInstance();
    });
}
```

**O que faz**: Fornece acesso aos valores de `login_bg_image`, `login_bg_zoom`, `login_bg_opacity` via cache.

---

### 6. View (Form) - Interface de Upload
**Arquivo**: `packages/Webkul/ThemeManager/Resources/views/admin/settings/theme/index.blade.php`

#### Upload da Imagem (Linhas 420-448)
```blade
<x-admin::form.control-group.label>
    @lang('theme-manager::app.settings.login.bg-image')
</x-admin::form.control-group.label>

@if($config->login_bg_image)
    <!-- Preview da imagem atual -->
    <img src="{{ asset('storage/theme-manager/' . $config->login_bg_image) }}"
         class="h-32 w-full rounded border object-cover">

    <!-- Checkbox para deletar -->
    <input type="checkbox" name="login_bg_image_delete" value="1">
@endif

<!-- Input de upload -->
<x-admin::form.control-group.control
    type="file"
    name="login_bg_image"
    accept="image/*"
/>
```

#### Select de Zoom (Linhas 456-470)
```blade
<x-admin::form.control-group.control
    type="select"
    name="login_bg_zoom"
    :value="old('login_bg_zoom', $config->login_bg_zoom)"
>
    <option value="50">50%</option>
    <option value="75">75%</option>
    <option value="100" selected>100%</option>
    <option value="125">125%</option>
    <option value="150">150%</option>
    <option value="200">200%</option>
</x-admin::form.control-group.control>
```

#### Select de Opacidade (Linhas 478-489)
```blade
<x-admin::form.control-group.control
    type="select"
    name="login_bg_opacity"
    :value="old('login_bg_opacity', $config->login_bg_opacity)"
>
    @for($i = 0; $i <= 100; $i += 10)
        <option value="{{ $i }}">{{ $i }}%</option>
    @endfor
</x-admin::form.control-group.control>
```

**O que faz**: Interface de upload e configuração de zoom/opacidade.

---

### 7. View (CSS) - Renderização do Background
**Arquivo**: `packages/Webkul/ThemeManager/Resources/views/components/theme-styles.blade.php`

#### CSS (Linhas 445-477)
```blade
@if($themeConfig->login_bg_image)
/* Background para página de login */
body,
.min-h-screen,
.flex.min-h-screen,
[class*="login"],
[class*="auth"] {
    background-image: url('{{ asset("storage/theme-manager/" . $themeConfig->login_bg_image) }}') !important;
    background-size: {{ $themeConfig->login_bg_zoom ?? 100 }}% !important;
    background-position: center center !important;
    background-repeat: no-repeat !important;
    background-attachment: fixed !important;
}

/* Overlay para controlar opacidade */
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, {{ (100 - ($themeConfig->login_bg_opacity ?? 50)) / 100 }});
    pointer-events: none;
    z-index: 0;
}

/* Garantir que conteúdo fique acima do overlay */
body > * {
    position: relative;
    z-index: 1;
}
@endif
```

**⚠️ PROBLEMA**: CSS aplica em `body` GLOBALMENTE (todas as páginas), não só no login!

---

### 8. View (JavaScript) - Renderização Alternativa
**Arquivo**: `packages/Webkul/ThemeManager/Resources/views/components/theme-styles.blade.php`

#### JavaScript (Linhas 549-581)
```javascript
@if($themeConfig->login_bg_image)
// LOGIN PAGE BACKGROUND (JavaScript backup)
if (window.location.pathname.includes('login') ||
    window.location.pathname.includes('session')) {

    console.log('🖼️ ThemeManager: Aplicando background de login...');

    var bgUrl = '{{ asset("storage/theme-manager/" . $themeConfig->login_bg_image) }}';
    var bgZoom = {{ $themeConfig->login_bg_zoom ?? 100 }};
    var bgOpacity = {{ ($themeConfig->login_bg_opacity ?? 50) / 100 }};

    // Aplicar no body
    document.body.style.backgroundImage = 'url(' + bgUrl + ')';
    document.body.style.backgroundSize = bgZoom + '%';
    document.body.style.backgroundPosition = 'center';
    document.body.style.backgroundRepeat = 'no-repeat';
    document.body.style.backgroundAttachment = 'fixed';

    // Criar overlay de opacidade
    var overlay = document.createElement('div');
    overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,' + (1 - bgOpacity) + ');pointer-events:none;z-index:0;';
    document.body.insertBefore(overlay, document.body.firstChild);

    // Ajustar z-index do conteúdo
    var mainContent = document.body.children[1];
    if (mainContent) {
        mainContent.style.position = 'relative';
        mainContent.style.zIndex = '1';
    }

    console.log('✅ ThemeManager: Background de login aplicado!', {url: bgUrl, zoom: bgZoom, opacity: bgOpacity});
}
@endif
```

**✅ CORRETO**: JavaScript verifica `pathname.includes('login')` - SÓ aplica na página de login!

---

## 🔄 FLUXO COMPLETO

### Upload e Salvamento:
```
1. Usuário faz upload no FORM
   ↓ (index.blade.php linha 440)

2. Controller VALIDA
   ↓ (ThemeController.php linha 74-76)

3. Controller envia para Repository
   ↓ (ThemeController.php linha 103)

4. Repository PROCESSA upload
   ↓ (ThemeConfigRepository.php linha 78, 91-132)

5. Arquivo SALVO em storage/app/public/theme-manager/
   ↓

6. Filename SALVO no banco (theme_configs.login_bg_image)
```

### Renderização:
```
1. Middleware injeta theme-styles.blade.php
   ↓ (ThemeMiddleware.php)

2. Helper busca config do cache
   ↓ (ThemeHelper.php)

3. CSS é renderizado (❌ PROBLEMA: GLOBAL)
   ↓ (theme-styles.blade.php linhas 445-477)

4. JavaScript é renderizado (✅ CORRETO: SÓ LOGIN)
   ↓ (theme-styles.blade.php linhas 549-581)

5. Se página é /login → JavaScript aplica background
```

---

## 🐛 PROBLEMA IDENTIFICADO

### CSS Aplica em TODAS as Páginas:
```css
body,
.min-h-screen {
    background-image: url(...);  ← GLOBAL!
}
```

**Efeito**: Background aparece no admin (dashboard, leads, etc.)

### JavaScript Aplica SÓ no Login:
```javascript
if (window.location.pathname.includes('login')) {
    // Aplica background  ← CORRETO!
}
```

**Efeito**: Background SÓ aparece na página de login.

---

## ✅ SOLUÇÃO

### Opção 1: Remover CSS, Deixar SÓ JavaScript
```blade
@if($themeConfig->login_bg_image)
{{-- REMOVER TODO O CSS (linhas 445-477) --}}
@endif

{{-- MANTER JavaScript (linhas 549-581) --}}
```

**Vantagem**: Simples, 100% funcional.

---

### Opção 2: Adicionar Classe no Body da Página de Login

**Se o Krayin adiciona classe no body da página de login** (ex: `class="login-page"`):

```css
/* APENAS páginas com classe .login-page */
body.login-page {
    background-image: url(...);
}
```

**Problema**: Precisaria verificar qual classe o Krayin usa.

---

### Opção 3: CSS com :has() (Moderno)
```css
/* Aplicar APENAS se houver form de login */
body:has(form[action*="login"]) {
    background-image: url(...);
}
```

**Problema**: `:has()` não funciona em navegadores antigos.

---

## 📊 RESUMO

| Arquivo | Função | Status |
|---------|--------|--------|
| Migration | Define estrutura do banco | ✅ OK |
| Model | Define campos e tipos | ✅ OK |
| Controller | Valida upload | ✅ OK |
| Repository | Processa e salva arquivo | ✅ OK |
| Helper | Fornece config via cache | ✅ OK |
| View (Form) | Interface de upload | ✅ OK |
| **View (CSS)** | Renderiza background | ❌ GLOBAL (problema) |
| **View (JavaScript)** | Renderiza background | ✅ SÓ LOGIN (correto) |

---

## 🎯 RECOMENDAÇÃO

**REMOVER CSS (linhas 445-477)** e **MANTER APENAS JavaScript (linhas 549-581)**.

JavaScript já funciona perfeitamente e aplica background SÓ na página de login.

---

**Última atualização**: 22/12/2024
**Arquivos mapeados**: 8
**Problema identificado**: CSS global aplicando em todas as páginas
**Solução**: Remover CSS, manter JavaScript
