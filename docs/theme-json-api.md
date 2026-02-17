# Theme.json API - Guia para Criadores de Temas

Este documento define a estrutura completa do arquivo `theme.json` usado pelo ThemeManager do Krayin CRM.

## Estrutura do Tema

Cada tema deve ser uma pasta dentro de `storage/app/public/themes/` contendo:

```
themes/
└── meu-tema/
    ├── theme.json          # Obrigatório - Configurações do tema
    ├── logo_main.svg       # Logo principal
    ├── logo_light.svg      # Logo para fundos escuros (opcional)
    ├── logo_icon.svg       # Ícone/favicon (opcional)
    ├── favicon.svg         # Favicon do site
    ├── login_bg.jpg        # Background da página de login
    ├── login_card_bg.jpg   # Background do card de login (opcional)
    └── theme.css           # CSS adicional (opcional)
```

---

## Campos do theme.json

### Metadados (Obrigatórios)

| Campo | Tipo | Descrição | Exemplo |
|-------|------|-----------|---------|
| `name` | string | Nome do tema exibido na UI | `"Meu Tema Corporativo"` |
| `version` | string | Versão semântica | `"1.0.0"` |
| `description` | string | Descrição breve | `"Tema azul corporativo"` |

### Metadados (Opcionais)

| Campo | Tipo | Descrição | Exemplo |
|-------|------|-----------|---------|
| `author` | string | Nome do autor | `"Empresa XYZ"` |
| `preview` | string | Imagem de preview | `"preview.png"` |

---

### Cores

Todas as cores devem ser em formato hexadecimal `#RRGGBB`.

| Campo | Tipo | Default | Descrição |
|-------|------|---------|-----------|
| `color_primary` | string | `#1E40AF` | Cor principal (botões, links) |
| `color_primary_dark` | string | `#1E3A8A` | Variante escura (hover) |
| `color_primary_light` | string | `#3B82F6` | Variante clara (backgrounds) |
| `color_success` | string | `#10B981` | Cor de sucesso |
| `color_warning` | string | `#F59E0B` | Cor de aviso |
| `color_danger` | string | `#EF4444` | Cor de erro/perigo |

**Exemplo:**
```json
{
  "color_primary": "#7C3AED",
  "color_primary_dark": "#6D28D9",
  "color_primary_light": "#A78BFA",
  "color_success": "#10B981",
  "color_warning": "#FBBF24",
  "color_danger": "#F43F5E"
}
```

---

### Logos e Favicon

Paths relativos à pasta do tema. Formatos suportados: SVG, PNG, JPG, ICO.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `logo_main` | string | Logo principal (sidebar, header) |
| `logo_light` | string | Logo para fundos escuros |
| `logo_icon` | string | Ícone pequeno (32x32 ou 64x64) |
| `favicon` | string | Favicon do navegador |

**Exemplo:**
```json
{
  "logo_main": "logo_main.svg",
  "logo_light": "logo_light.svg",
  "logo_icon": "logo_icon.png",
  "favicon": "favicon.ico"
}
```

---

### Login - Background da Página

| Campo | Tipo | Default | Descrição |
|-------|------|---------|-----------|
| `login_bg_image` | string | null | Imagem de fundo da página |
| `login_bg_zoom` | int | 100 | Zoom da imagem (100 = 100%) |
| `login_bg_opacity` | int | 50 | Opacidade da imagem (0-100) |
| `login_show_powered_by` | bool | true | Exibir "Powered by Krayin" |

**Nota sobre opacidade:** O valor define a visibilidade da imagem. 100 = imagem totalmente visível, 0 = imagem oculta (overlay escuro total).

**Exemplo:**
```json
{
  "login_bg_image": "login_bg.jpg",
  "login_bg_zoom": 100,
  "login_bg_opacity": 45,
  "login_show_powered_by": false
}
```

---

### Login - Card Customizado

Quando `login_card_enabled` é `true`, o card de login exibe um header personalizado com imagem de fundo.

| Campo | Tipo | Default | Descrição |
|-------|------|---------|-----------|
| `login_card_enabled` | bool | false | Ativar card customizado |
| `login_card_bg_image` | string | null | Imagem de fundo do card |
| `login_card_bg_opacity` | int | 62 | Opacidade da imagem (0-100) |
| `login_card_overlay_color` | string | `rgba(10,45,15,0.78)` | Cor do overlay sobre a imagem |
| `login_card_title` | string | `"Bem-vindo"` | Título no header do card |
| `login_card_subtitle` | string | `"Acesse sua conta"` | Subtítulo no header |
| `login_card_sparkles` | bool | false | Efeito de partículas animadas |
| `login_card_help_link` | bool | true | Exibir link de ajuda |
| `login_card_support_email` | string | `"suporte@empresa.com.br"` | Email de suporte |

**Exemplo:**
```json
{
  "login_card_enabled": true,
  "login_card_bg_image": "login_card_bg.jpg",
  "login_card_bg_opacity": 55,
  "login_card_overlay_color": "rgba(109, 40, 217, 0.92)",
  "login_card_title": "Portal Corporativo",
  "login_card_subtitle": "Acesse com suas credenciais",
  "login_card_sparkles": true,
  "login_card_help_link": true,
  "login_card_support_email": "ti@empresa.com"
}
```

---

## Exemplo Completo

```json
{
  "name": "Roxo Moderno",
  "version": "1.0.0",
  "author": "Minha Empresa",
  "description": "Tema roxo vibrante com visual moderno",

  "color_primary": "#7C3AED",
  "color_primary_dark": "#6D28D9",
  "color_primary_light": "#A78BFA",
  "color_success": "#10B981",
  "color_warning": "#FBBF24",
  "color_danger": "#F43F5E",

  "logo_main": "logo_main.svg",
  "logo_light": "logo_light.svg",
  "favicon": "favicon.svg",

  "login_bg_image": "login_bg.svg",
  "login_bg_opacity": 45,
  "login_show_powered_by": true,

  "login_card_enabled": true,
  "login_card_bg_image": "login_card_bg.svg",
  "login_card_bg_opacity": 55,
  "login_card_overlay_color": "rgba(109, 40, 217, 0.92)",
  "login_card_title": "Inovação",
  "login_card_subtitle": "O futuro começa aqui",
  "login_card_sparkles": true
}
```

---

## Preview de Tema

Para visualizar um tema sem aplicá-lo permanentemente, adicione o parâmetro `?theme_preview=SLUG` na URL:

```
https://seusite.com/admin/login?theme_preview=meu-tema
```

Para limpar o preview:
```
https://seusite.com/admin?clear_preview=1
```

**Requisitos:**
- Usuário deve estar autenticado
- Usuário deve ter permissão `settings`
- O tema deve existir em `storage/app/public/themes/`

---

## Validação de Assets

O sistema valida automaticamente:

1. **Formatos de imagem permitidos:** JPG, JPEG, PNG, GIF, SVG, WEBP, ICO
2. **Tamanho máximo:** 5MB por arquivo
3. **Dimensões recomendadas:**
   - Logo principal: 200x60px
   - Logo icon: 64x64px
   - Favicon: 32x32px ou 64x64px
   - Background login: 1920x1080px ou maior
   - Background card: 800x600px ou maior

---

## Dicas de Design

### Cores de Overlay

Para calcular a cor do overlay do card, use RGBA com alta opacidade:
- **Escuro elegante:** `rgba(0, 0, 0, 0.85)`
- **Colorido vibrante:** `rgba(COR_PRIMARY_RGB, 0.90)`
- **Verde natureza:** `rgba(20, 83, 45, 0.90)`
- **Roxo moderno:** `rgba(109, 40, 217, 0.92)`

### Contraste de Texto

O header do card usa texto branco. Garanta que seu overlay tenha opacidade suficiente (> 0.75) para legibilidade.

### Sparkles

O efeito sparkles funciona melhor com cores de overlay escuras ou saturadas. Evite overlays muito claros.

---

## Troubleshooting

| Problema | Solução |
|----------|---------|
| Imagem não aparece | Verifique se o arquivo existe e o path está correto no theme.json |
| Cores não aplicam | Limpe o cache: `php artisan optimize:clear` |
| Preview não funciona | Verifique permissões e se o usuário está logado |
| Card sem background | Verifique se `login_card_enabled` é `true` (boolean, não string) |

---

*Documentação gerada em Dezembro 2024 - ThemeManager v1.0*
