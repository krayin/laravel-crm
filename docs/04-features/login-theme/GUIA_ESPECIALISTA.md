# Guia Técnico: Separação Background Page vs Login Card

> **Documento para Especialista Frontend**  
> Objetivo: Adaptar solução responsiva existente para trabalhar com dois componentes separados

---

## 1. VISÃO GERAL DA ARQUITETURA

O ThemeManager divide a página de login em **dois componentes independentes**:

```
┌─────────────────────────────────────────────────────────────┐
│                    LOGIN PAGE BACKGROUND                     │
│  (Imagem de fundo que cobre toda a tela)                    │
│                                                              │
│    ┌─────────────────────────────────────┐                  │
│    │         LOGIN CARD                   │                  │
│    │  (Caixa central com formulário)      │                  │
│    │                                       │                  │
│    │  ┌─────────────────────────────┐     │                  │
│    │  │  Título + Subtítulo         │     │                  │
│    │  │  Formulário de Login        │     │                  │
│    │  │  Link de Ajuda              │     │                  │
│    │  └─────────────────────────────┘     │                  │
│    │                                       │                  │
│    └─────────────────────────────────────┘                  │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. CAMPOS DO LOGIN BACKGROUND (Página inteira)

### 2.1 Campos no Banco de Dados

| Campo | Tipo | Default | Descrição |
|-------|------|---------|-----------|
| `login_bg_image` | VARCHAR(500) | NULL | Caminho da imagem de fundo |
| `login_bg_zoom` | INT | 100 | Zoom da imagem (50-200%) |
| `login_bg_opacity` | INT | 50 | Opacidade do overlay branco (0-100%) |
| `login_show_powered_by` | BOOLEAN | TRUE | Exibir "Powered by Krayin" |

### 2.2 Como os Campos São Usados

**Arquivo:** `theme-styles.blade.php` (linhas 447-475)

```css
/* CSS aplicado quando login_bg_image existe */
@if($themeConfig->login_bg_image)

/* Background aplicado em toda a página */
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

/* Overlay branco para controlar visibilidade */
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

/* Conteúdo acima do overlay */
body > * {
    position: relative;
    z-index: 1;
}
@endif
```

### 2.3 JavaScript Backup (fallback)

```javascript
// Aplicado quando URL contém 'login' ou 'session'
if (window.location.pathname.includes('login') || window.location.pathname.includes('session')) {
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
    overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;' +
                            'background:rgba(255,255,255,' + (1 - bgOpacity) + ');' +
                            'pointer-events:none;z-index:0;';
    document.body.insertBefore(overlay, document.body.firstChild);
}
```

### 2.4 Variáveis PHP Disponíveis

```php
$themeConfig->login_bg_image      // string: "1703123456_login_bg_abc123.jpg"
$themeConfig->login_bg_zoom       // int: 100 (representa %)
$themeConfig->login_bg_opacity    // int: 50 (representa %)
$themeConfig->login_show_powered_by // bool: true/false
```

---

## 3. CAMPOS DO LOGIN CARD (Caixa central)

### 3.1 Campos no Banco de Dados

| Campo | Tipo | Default | Descrição |
|-------|------|---------|-----------|
| `login_card_enabled` | BOOLEAN | FALSE | Ativar card customizado |
| `login_card_bg_image` | VARCHAR(500) | NULL | Imagem de fundo DO CARD |
| `login_card_bg_opacity` | INT | 62 | Opacidade do overlay do card |
| `login_card_overlay_color` | VARCHAR(50) | `rgba(10, 45, 15, 0.78)` | Cor do overlay |
| `login_card_title` | VARCHAR(100) | "Bem-vindo" | Título de boas-vindas |
| `login_card_subtitle` | VARCHAR(200) | "Acesse sua conta..." | Subtítulo |
| `login_card_sparkles` | BOOLEAN | FALSE | Efeito de brilhos animados |
| `login_card_help_link` | BOOLEAN | TRUE | Mostrar link de ajuda |
| `login_card_support_email` | VARCHAR(100) | "suporte@empresa.com.br" | Email do link |
| `login_card_custom_code` | TEXT | NULL | HTML/CSS/JS customizado |

### 3.2 Como os Campos São Usados

**Arquivo:** `theme-styles.blade.php` (linhas 680-850)

O código do Login Card só executa se `login_card_enabled = true`:

```javascript
@if($themeConfig->login_card_enabled)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Só aplicar na página de login
    if (!window.location.pathname.includes('login') && 
        !window.location.pathname.includes('session')) {
        return;
    }

    // Objeto de configuração
    var config = {
        bgImage: '{{ $themeConfig->login_card_bg_image ? asset("storage/theme-manager/" . $themeConfig->login_card_bg_image) : "" }}',
        bgOpacity: {{ $themeConfig->login_card_bg_opacity ?? 62 }},
        overlayColor: '{{ $themeConfig->login_card_overlay_color ?? "rgba(10, 45, 15, 0.78)" }}',
        title: '{{ $themeConfig->login_card_title ?? "Bem-vindo" }}',
        subtitle: '{{ $themeConfig->login_card_subtitle ?? "Acesse sua conta" }}',
        sparkles: {{ $themeConfig->login_card_sparkles ? 'true' : 'false' }},
        helpLink: {{ $themeConfig->login_card_help_link ? 'true' : 'false' }},
        supportEmail: '{{ $themeConfig->login_card_support_email ?? "suporte@empresa.com.br" }}'
    };

    // Encontrar o card de login
    var loginCard = document.querySelector('.box-shadow.rounded-md.bg-white');
    
    // ... aplicar customizações
});
</script>
@endif
```

### 3.3 Seletor CSS do Card

O card de login é identificado por:

```javascript
var loginCard = document.querySelector(
    '.box-shadow.rounded-md.bg-white, ' +
    '.box-shadow.rounded-md.dark\\:bg-gray-900'
);
```

### 3.4 Aplicação do Background no Card

```javascript
if (config.bgImage) {
    // Background image no card
    loginCard.style.backgroundImage = 'url(' + config.bgImage + ')';
    loginCard.style.backgroundSize = 'cover';
    loginCard.style.backgroundPosition = 'center';
    loginCard.style.backgroundRepeat = 'no-repeat';
    loginCard.style.position = 'relative';

    // Overlay colorido (usa login_card_overlay_color)
    var overlay = document.createElement('div');
    overlay.style.cssText = 
        'position:absolute;' +
        'top:0;left:0;width:100%;height:100%;' +
        'background:' + config.overlayColor + ';' +  // <-- Cor RGBA configurável
        'border-radius:inherit;' +
        'pointer-events:none;' +
        'z-index:0;';
    loginCard.insertBefore(overlay, loginCard.firstChild);

    // Garantir que conteúdo fique acima do overlay
    Array.from(loginCard.children).forEach(function(child, index) {
        if (index > 0) {
            child.style.position = 'relative';
            child.style.zIndex = '1';
        }
    });
}
```

### 3.5 Título e Subtítulo Customizados

```javascript
var titleElement = loginCard.querySelector('p.text-xl.font-bold');

if (titleElement) {
    var headerContainer = document.createElement('div');
    headerContainer.style.cssText = 'text-align:center;margin-bottom:1rem;';

    var customTitle = document.createElement('h2');
    customTitle.textContent = config.title;
    customTitle.style.cssText = 'font-size:1.5rem;font-weight:700;color:inherit;margin-bottom:0.5rem;';

    var customSubtitle = document.createElement('p');
    customSubtitle.textContent = config.subtitle;
    customSubtitle.style.cssText = 'font-size:0.875rem;color:rgba(107, 114, 128, 1);';

    headerContainer.appendChild(customTitle);
    headerContainer.appendChild(customSubtitle);

    // Substitui o título original
    titleElement.parentNode.replaceChild(headerContainer, titleElement);
}
```

### 3.6 Efeito Sparkles

```javascript
if (config.sparkles) {
    var sparklesContainer = document.createElement('div');
    sparklesContainer.style.cssText = 
        'position:absolute;top:0;left:0;width:100%;height:100%;' +
        'overflow:hidden;pointer-events:none;z-index:10;border-radius:inherit;';

    // Criar 15 sparkles aleatórios
    for (var i = 0; i < 15; i++) {
        var sparkle = document.createElement('div');
        var size = Math.random() * 4 + 2;  // 2-6px
        var left = Math.random() * 100;
        var top = Math.random() * 100;
        var delay = Math.random() * 3;
        var duration = Math.random() * 2 + 2;  // 2-4s

        sparkle.style.cssText = 
            'position:absolute;' +
            'width:' + size + 'px;height:' + size + 'px;' +
            'background:rgba(255,255,255,0.8);border-radius:50%;' +
            'left:' + left + '%;top:' + top + '%;' +
            'animation:sparkle ' + duration + 's ease-in-out ' + delay + 's infinite;';

        sparklesContainer.appendChild(sparkle);
    }

    // CSS da animação
    var style = document.createElement('style');
    style.textContent = '@keyframes sparkle { ' +
        '0%, 100% { opacity: 0; transform: scale(0); } ' +
        '50% { opacity: 1; transform: scale(1); } ' +
    '}';
    document.head.appendChild(style);

    loginCard.appendChild(sparklesContainer);
}
```

---

## 4. INJEÇÃO DE CUSTOM CODE

### 4.1 O que é

O campo `login_card_custom_code` permite injetar código HTML, CSS e JavaScript diretamente na página de login. É um campo `TEXT` no banco que aceita qualquer código.

### 4.2 Como Funciona

```javascript
@if($themeConfig->login_card_custom_code)
console.log('📝 Injetando código customizado...');

// Criar container temporário para parsear o HTML
var customCodeContainer = document.createElement('div');
customCodeContainer.innerHTML = {!! json_encode($themeConfig->login_card_custom_code) !!};

// 1. EXTRAIR E INJETAR CSS NO <head>
var styles = customCodeContainer.querySelectorAll('style');
styles.forEach(function(styleEl) {
    var newStyle = document.createElement('style');
    newStyle.textContent = styleEl.textContent;
    document.head.appendChild(newStyle);
    console.log('✓ CSS customizado injetado no <head>');
});

// 2. EXTRAIR E ADICIONAR HTML AO BODY
var htmlElements = Array.from(customCodeContainer.children).filter(function(el) {
    return el.tagName !== 'STYLE' && el.tagName !== 'SCRIPT';
});
htmlElements.forEach(function(el) {
    document.body.appendChild(el);
});

// 3. EXECUTAR SCRIPTS
var scripts = customCodeContainer.querySelectorAll('script');
scripts.forEach(function(oldScript) {
    var scriptContent = oldScript.textContent.trim();

    // Remove wrapper DOMContentLoaded se existir (já estamos no DOMContentLoaded)
    if (scriptContent.indexOf('DOMContentLoaded') !== -1) {
        var match = scriptContent.match(
            /addEventListener\s*\(\s*['"]DOMContentLoaded['"]\s*,\s*function\s*\(\s*\)\s*\{([\s\S]*)\}\s*\)\s*;?\s*$/
        );
        if (match && match[1]) {
            scriptContent = match[1].trim();
        }
    }

    // Executar imediatamente
    try {
        eval(scriptContent);
        console.log('✓ JavaScript customizado executado');
    } catch (e) {
        console.error('❌ Erro ao executar JavaScript customizado:', e);
    }
});
@endif
```

### 4.3 Exemplo de Custom Code

O especialista pode colocar no campo `login_card_custom_code`:

```html
<!-- CSS Customizado -->
<style>
    .minha-classe-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
        border-radius: 10px;
    }
    
    .login-card-responsivo {
        /* Estilos responsivos */
    }
    
    @media (max-width: 768px) {
        .box-shadow.rounded-md {
            width: 95% !important;
            margin: 10px auto !important;
        }
    }
</style>

<!-- HTML Customizado (será adicionado ao body) -->
<div class="minha-classe-custom">
    <p>Mensagem customizada</p>
</div>

<!-- JavaScript Customizado -->
<script>
    // Este código será executado automaticamente
    console.log('Código customizado carregado!');
    
    // Pode manipular o DOM
    var loginCard = document.querySelector('.box-shadow.rounded-md');
    if (loginCard) {
        loginCard.classList.add('minha-classe-custom');
    }
    
    // Pode adicionar event listeners
    document.querySelectorAll('input').forEach(function(input) {
        input.addEventListener('focus', function() {
            this.style.borderColor = '#667eea';
        });
    });
</script>
```

### 4.4 Ordem de Execução

1. **CSS** → Injetado no `<head>` primeiro
2. **HTML** → Adicionado ao `<body>`
3. **JavaScript** → Executado por último (via `eval()`)

### 4.5 Considerações de Segurança

- O campo aceita qualquer código (HTML/CSS/JS)
- Scripts são executados via `eval()` - apenas admins têm acesso
- CSS pode usar `!important` para sobrescrever estilos
- HTML é inserido no body, não dentro do card

---

## 5. DIFERENÇAS CHAVE PARA O ESPECIALISTA

### Background Page vs Card

| Aspecto | Login Background | Login Card |
|---------|------------------|------------|
| **Escopo** | Página inteira (`body`) | Apenas a caixa central |
| **Overlay** | Branco com opacity | Cor RGBA customizável |
| **Ativação** | Sempre ativo se imagem existe | Precisa `login_card_enabled = true` |
| **Implementação** | CSS + JS fallback | Apenas JavaScript |
| **Z-index** | `body::before` z-index: 0 | Overlay interno z-index: 0 |

### Seletores CSS Importantes

```css
/* Background da página */
body,
.min-h-screen,
.flex.min-h-screen { }

/* Card de login */
.box-shadow.rounded-md.bg-white,
.box-shadow.rounded-md.dark\:bg-gray-900 { }

/* Título dentro do card */
.box-shadow p.text-xl.font-bold { }

/* Container de botões */
.flex.items-center.justify-between.p-4 { }
```

---

## 6. COMO ADAPTAR SOLUÇÃO RESPONSIVA

### 6.1 Para Background

Usar o campo `login_bg_zoom` para ajustar em diferentes telas, ou adicionar media queries via `login_card_custom_code`:

```html
<style>
    @media (max-width: 768px) {
        body {
            background-size: cover !important;
            background-attachment: scroll !important;
        }
        body::before {
            /* Ajustar overlay para mobile */
            background: rgba(255, 255, 255, 0.8) !important;
        }
    }
</style>
```

### 6.2 Para Card

Usar `login_card_custom_code` para estilos responsivos:

```html
<style>
    /* Desktop */
    .box-shadow.rounded-md {
        width: 400px;
        max-width: 90vw;
    }
    
    /* Tablet */
    @media (max-width: 1024px) {
        .box-shadow.rounded-md {
            width: 350px;
        }
    }
    
    /* Mobile */
    @media (max-width: 640px) {
        .box-shadow.rounded-md {
            width: 95vw !important;
            margin: 20px auto !important;
            border-radius: 12px !important;
        }
    }
</style>
```

---

## 7. VARIÁVEIS DISPONÍVEIS NO JAVASCRIPT

Dentro do código customizado, você tem acesso a:

```javascript
// Configuração do card (se login_card_enabled = true)
config.bgImage        // URL da imagem do card
config.bgOpacity      // Opacidade (0-100)
config.overlayColor   // Cor RGBA do overlay
config.title          // Título
config.subtitle       // Subtítulo
config.sparkles       // Boolean
config.helpLink       // Boolean
config.supportEmail   // Email

// Elemento do card
var loginCard = document.querySelector('.box-shadow.rounded-md.bg-white');

// Verificar se está na página de login
var isLoginPage = window.location.pathname.includes('login');
```

---

## 8. ARQUIVOS RELEVANTES

| Arquivo | Descrição |
|---------|-----------|
| `theme-styles.blade.php` | Toda a injeção de CSS/JS |
| `index.blade.php` | Formulário de configuração |
| `ThemeConfigRepository.php` | Salva os dados |
| `ThemeController.php` | Validações |
| `ThemeHelper.php` | Acesso aos dados via `app('theme')` |

---

## 9. RESUMO PARA IMPLEMENTAÇÃO

### O especialista precisa:

1. **Para Background:** 
   - Usar CSS via `login_card_custom_code` com media queries
   - Ou ajustar `login_bg_zoom` para diferentes necessidades

2. **Para Card:**
   - Ativar `login_card_enabled = true`
   - Usar `login_card_custom_code` para CSS/JS responsivo
   - Manipular `.box-shadow.rounded-md` com estilos customizados

3. **Para solução completa responsiva:**
   - Colocar TODO o CSS responsivo no campo `login_card_custom_code`
   - CSS será injetado no `<head>` automaticamente
   - JS será executado após DOM ready

---

*Documento criado em: 22/12/2024*
*ThemeManager v1.0.0*
