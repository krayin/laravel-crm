## 0) Contexto do Projeto (30 segundos)
Sistema de customização visual para Krayin CRM (Laravel):
- **Preset Themes**: Temas base em `storage/app/public/themes/{slug}/theme.json`
- **ThemeManager**: Pacote Laravel que gerencia temas, cores, logos e login page
- **Database Config**: Configurações salvas em `theme_configs` (singleton)
- **Regra de ouro**: Sem editar `vendor/`, tudo via overrides upgrade-safe

**Estado atual (Dezembro 2025):**
- ✅ CRUD completo de temas (Controller + Repository + Helper)
- ✅ Bugs #1-5 resolvidos (delete, previews, zoom/opacity)
- ✅ Tooltips informativos implementados
- ✅ Toasts melhorados com instruções
- ⏳ Próximas melhorias planejadas

---

## 1) Stack & Estrutura do Projeto

### Stack
- **Backend**: Laravel (Krayin CRM)
- **Views**: Blade (componentes em `packages/Webkul/ThemeManager/Resources/views/`)
- **Assets**: Vite + Laravel Mix
- **DB**: MySQL/MariaDB (tabela `theme_configs`)
- **Cache**: Laravel Cache (invalidado por ThemeHelper)
- **Skills**: `/mnt/skills/` (docx, pptx, xlsx, pdf, etc.)

### Estrutura de Arquivos
```
packages/Webkul/ThemeManager/
├── src/
│   ├── Http/Controllers/
│   │   └── ThemeController.php
│   ├── Repositories/
│   │   └── ThemeConfigRepository.php
│   ├── Helpers/
│   │   └── ThemeHelper.php
│   ├── Models/
│   │   └── ThemeConfig.php
│   ├── Database/Migrations/
│   │   └── *_create_theme_configs_table.php
│   ├── Routes/
│   │   └── admin-routes.php
│   └── Providers/
│       └── ThemeManagerServiceProvider.php
├── Resources/
│   └── views/
│       ├── admin/
│       │   ├── sessions/login.blade.php
│       │   └── settings/theme/index.blade.php
│       └── components/
│           └── theme-styles.blade.php

storage/app/public/themes/
├── default/
│   └── theme.json
├── azul-oceano/
│   └── theme.json
├── stelium-sanctuary/
│   ├── theme.json
│   ├── logo_main.svg
│   └── login_bg.jpg
└── [outros temas]/

storage/app/public/theme-manager/
└── [uploads dos usuários - timestamps]
```

### Comandos Essenciais
```bash
# Limpar caches (SEMPRE após mudanças em temas)
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Rodar servidor (porta customizada para testes)
php artisan serve --port=8042

# Tinker (debug rápido)
php artisan tinker
>>> app('theme')->getConfig()
>>> app('theme')->getLoginConfig()
```

---

## 2) Arquitetura — Como Funciona

### Fluxo de Dados
```
1. User acessa /admin/settings/theme
   ↓
2. ThemeController::index() 
   ↓
3. ThemeConfigRepository::get() → Retorna singleton ThemeConfig
   ↓
4. View renderiza form com valores atuais
   ↓
5. User salva mudanças (POST)
   ↓
6. ThemeController::update()
   ↓
7. ThemeConfigRepository::update()
   ├─ Processa uploads (logo, favicon, backgrounds)
   ├─ Deleta arquivos antigos se marcado
   ├─ Salva no DB (theme_configs table)
   └─ Invalida cache (ThemeHelper::clearCache())
   ↓
8. User vê toast de sucesso
   ↓
9. Frontend usa ThemeHelper::getCssVariables() para aplicar tema
```

### Componentes Principais

#### **ThemeConfig (Model - Singleton)**
```php
// Tabela: theme_configs (sempre 1 única linha)
id, is_active, selected_theme, 
color_primary, color_success, color_warning, color_danger,
logo_main, logo_light, logo_icon, favicon,
login_bg_image, login_bg_zoom, login_bg_opacity,
login_card_bg_image, login_card_bg_opacity, login_card_overlay_color,
login_card_title, login_card_subtitle,
created_at, updated_at

// Singleton pattern
ThemeConfig::getInstance() // Retorna sempre a mesma instância
```

#### **ThemeConfigRepository**
- **Responsabilidades**: CRUD, upload de arquivos, delete de imagens
- **Métodos principais**:
  - `get()`: Retorna singleton
  - `update(array $data)`: Salva tudo + limpa cache
  - `loadThemeSettings($slug)`: Carrega theme.json e copia assets
  - `deleteFile($filename)`: Remove arquivo antigo
  - `sanitizeSvg($content)`: Sanitiza SVG contra XSS

#### **ThemeHelper**
- **Responsabilidades**: Cache, sanitização, geração de CSS
- **Métodos principais**:
  - `getConfig()`: Config com cache (1 hora)
  - `getCssVariables()`: Gera `:root { --primary-color: ...; }`
  - `getLoginConfig()`: Array com todas configs de login
  - `sanitizeHexColor()`: Valida cores (anti-injection)
  - `clearCache()`: Invalida cache

#### **ThemeController**
- **Responsabilidades**: Validação, flash messages, eventos
- **Regras**:
  - ❌ NÃO salvar direto no Model
  - ✅ SEMPRE chamar Repository
  - ✅ SEMPRE validar input (regex para cores, tamanhos de arquivo)
  - ✅ SEMPRE disparar eventos (`theme.update.before`, `theme.update.after`)

---

## 3) Regras de Segurança (NÃO NEGOCIAR)

### Upload de Arquivos
- ✅ Validar extensão: `svg,png,jpg,jpeg,webp,ico`
- ✅ Validar tamanho: logos max 5MB, backgrounds max 10MB
- ✅ Sanitizar SVG: remover `<script>`, `onload`, etc.
- ✅ Nome único: `timestamp_field_random.ext`
- ❌ NUNCA confiar em filename do usuário

### Cores
- ✅ Validar regex hex: `/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/`
- ✅ Validar regex rgba: `/^rgba?\(...\)$/`
- ❌ NUNCA inserir direto no CSS sem validar

### CSS (se adicionar no futuro)
- ✅ Escopar: `#theme-scope { ... }`
- ✅ Sanitizar: remover `@import`, `url()` externos
- ❌ NUNCA permitir CSS global sem escopo

### Cache
- ✅ SEMPRE invalidar após salvar: `ThemeHelper::clearCache()`
- ✅ SEMPRE invalidar após deletar imagem
- ✅ Cache TTL: 1 hora (3600s)

---

## 4) Bugs Resolvidos (Histórico)

### ✅ Bug #1: Delete de Logos/Favicon
- **Problema**: Checkbox "Delete" não funcionava
- **Causa**: Repository não processava campos `*_delete`
- **Solução**: Lógica de delete no Repository (linhas 162-171)
- **Status**: RESOLVIDO

### ✅ Bug #2: Login Background Delete
- **Problema**: Background voltava para gradient roxo após delete
- **Causa**: Gradient hardcoded no template login.blade.php
- **Solução**: Remover gradient, aplicar `#f3f4f6` quando NULL
- **Status**: RESOLVIDO

### ✅ Bug #3: Login Card Background Delete
- **Problema**: Overlay verde escuro cobria tudo mesmo sem imagem
- **Causa**: CSS gerado incondicionalmente em theme-styles.blade.php
- **Solução**: Tornar variáveis CSS e classe `theme-login-card-custom` condicionais
- **Status**: RESOLVIDO

### ✅ Bug #4: Preview de Imagens Gigantes
- **Problema**: Logos/backgrounds ocupavam 90% da tela no admin
- **Causa**: Falta de `max-height` e containers fixos
- **Solução**: Thumbnails compactos (128x64px logos, 128x80px backgrounds)
- **Status**: RESOLVIDO

### ✅ Bug #5: Zoom/Opacity Não Funcionavam
- **Problema**: Sliders salvavam mas não aplicavam na tela de login
- **Causa**: `getCssVariables()` não gerava variáveis de zoom/opacity
- **Solução**: Adicionar variáveis no ThemeHelper (linhas ~160-175)
- **Status**: RESOLVIDO

---

## 5) Melhorias Implementadas

### ✅ Melhoria #1: Tooltips Informativos
- **25 tooltips** implementados em todos os campos do formulário
- Ícone (i) azul ao lado de cada label
- Popup com explicação no hover
- Design moderno (sombra, z-index alto)
- Responsivo (mobile centralizado)
- **Bug corrigido (Dez/2025)**: CSS não estava sendo carregado
  - **Causa**: `<style>` solto no template, não injetado no `<head>`
  - **Solução**: Mover para `@pushOnce('styles')` + usar `display: none/block !important`
  - **Arquivo**: [index.blade.php](packages/Webkul/ThemeManager/Resources/views/admin/settings/theme/index.blade.php) linhas 11-120

### ✅ Melhoria #2: Toasts Melhorados
- Mensagens específicas por tipo de update (logos, cores, zoom)
- Instruções de cache: "Pressione Ctrl+F5"
- Emojis visuais: ✅ 🔄 ↩️ 💡
- Contexto claro para o usuário

---

## 6) Sistema de Temas (theme.json)

### Estrutura de um Tema
```json
{
  "name": "Azul Oceano",
  "slug": "azul-oceano",
  "version": "1.0.0",
  "description": "Tema azul oceano - inspirado no oceano",
  
  "color_primary": "#1E40AF",
  "color_primary_dark": "#1E3A8A",
  "color_primary_light": "#3B82F6",
  "color_success": "#10B981",
  "color_warning": "#F59E0B",
  "color_danger": "#EF4444",
  
  "logo_main": "logo_main.svg",
  "logo_light": "logo_light.svg",
  "favicon": "favicon.ico",
  
  "login": {
    "bg_image": "login_bg.jpg",
    "bg_zoom": 100,
    "bg_opacity": 50,
    "card_bg_image": "login_card_bg.jpg",
    "card_bg_opacity": 62,
    "card_overlay_color": "rgba(10, 45, 15, 0.78)",
    "card_title": "Bem-vindo",
    "card_subtitle": "Acesse sua conta"
  }
}
```

### Quando User Seleciona Tema:
1. Repository lê `storage/app/public/themes/{slug}/theme.json`
2. Copia assets para `storage/app/public/theme-manager/`
3. Salva valores no DB (`theme_configs`)
4. Invalida cache
5. Frontend usa valores do DB (não do JSON)

---

## 7) Padrões de Código

### Controllers
```php
// ❌ ERRADO
$config->update(['color_primary' => '#FF0000']);

// ✅ CERTO
$this->themeConfigRepository->update($request->all());
```

### Validação
```php
// ✅ SEMPRE validar cores com regex
'color_primary' => ['nullable', 'string', 'max:7', 
    'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],

// ✅ SEMPRE validar tamanhos
'logo_main' => 'nullable|file|mimes:svg,png,jpg|max:5120',

// ✅ SEMPRE validar ranges
'login_bg_zoom' => 'nullable|integer|min:50|max:200',
```

### Cache
```php
// ✅ SEMPRE invalidar após mudanças
$config = $this->themeConfigRepository->update($data);
$this->themeHelper->clearCache();
Event::dispatch('theme.update.after', $config);
```

---

## 8) Workflow Recomendado (Como Pedir Tarefas)

### Fase 1: Explorar
```
"Leia ThemeController.php e ThemeConfigRepository.php.
Não escreva código ainda. Liste fluxo e possíveis problemas."
```

### Fase 2: Planejar
```
"Crie plano em passos para [tarefa]. Inclua validações,
cache, e tratamento de erros. Não escreva código ainda."
```

### Fase 3: Implementar
```
"Implemente seguindo o plano. Mantenha controllers finos.
Use Repository para salvar. Valide tudo."
```

### Fase 4: Validar
```
"Teste com: php artisan tinker
>>> app('theme')->getConfig()
Mostre comandos e outputs esperados."
```

### Fase 5: Documentar
```
"Adicione comentários e atualize CLAUDE.md se necessário."
```

---

## 9) Skills (Sistema de Habilidades)

Claude tem acesso a skills especializadas em `/mnt/skills/`:

- **docx**: Criar/editar documentos Word
- **pptx**: Criar/editar apresentações PowerPoint
- **xlsx**: Criar/editar planilhas Excel
- **pdf**: Criar/manipular PDFs
- **frontend-design**: Design de interfaces web

**Quando usar**: Antes de criar documentos, SEMPRE ler o SKILL.md correspondente com `view /mnt/skills/public/{tipo}/SKILL.md`

---

## 10) Constraints (Guardrails)

### ❌ NUNCA
- Editar `vendor/`
- Commitar `.env`, senhas, tokens
- Aplicar CSS sem validar
- Salvar direto no Model (usar Repository)
- Upload sem validar extensão/tamanho
- Cores sem validar regex

### ✅ SEMPRE
- Invalidar cache após mudanças
- Validar input (FormRequest ou validate())
- Usar Repository para CRUD
- Sanitizar SVG antes de salvar
- Disparar eventos (before/after)
- Manter fallback funcionando

---

## 11) Known Issues (Checklist Rápido)

### Mudança não reflete?
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
# Ctrl+F5 no navegador
```

### Preview gigante?
- Confirmar thumbnails: `w-32 h-16` (logos), `w-32 h-20` (backgrounds)
- Confirmar `max-height` com `!important`

### Zoom/Opacity não funciona?
- Confirmar `getCssVariables()` gera variáveis
- Confirmar divisão por 100: `zoom / 100`, `opacity / 100`

### CSS quebra layout?
- Confirmar escopo: `#theme-scope { ... }`
- Confirmar sanitização no validator

---

## 12) Roadmap (Próximas Melhorias)

### Planejadas (não implementadas):
- [ ] Botão "Reset" por campo individual
- [ ] Export/Import de tema (JSON)
- [ ] Validação com avisos (não bloqueio)
- [ ] Preview antes/depois lado a lado
- [ ] Lazy load de previews (performance)
- [ ] Copiar cores entre temas

### Não fazer agora (muito estrutural):
- ❌ Sistema de snapshots/versionamento
- ❌ Multi-idioma para temas
- ❌ Editor visual drag-and-drop
- ❌ Herança/composição de temas

---

**Última atualização**: Dezembro 2025
**Bugs resolvidos**: 5/5
**Melhorias implementadas**: 2/2
**Próxima prioridade**: Melhorias UX não-estruturais
```