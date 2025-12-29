# Theme Manager - Mapeamento de Funcionalidades

> **Versao:** 1.0.0
> **Atualizado:** Dezembro 2025
> **Objetivo:** Documentar cada funcionalidade com arquivos envolvidos, interligacoes, dependencias, erros a evitar e melhorias possiveis.

---

## Indice

1. [Ativacao do Tema](#1-ativacao-do-tema)
2. [Selecao de Tema Predefinido](#2-selecao-de-tema-predefinido)
3. [Gerenciamento de Cores](#3-gerenciamento-de-cores)
4. [Upload e Gerenciamento de Logos](#4-upload-e-gerenciamento-de-logos)
5. [Login Page Background](#5-login-page-background)
6. [Login Card Customizado](#6-login-card-customizado)
7. [Empty States](#7-empty-states)
8. [Reset Field (Restaurar Campo)](#8-reset-field-restaurar-campo)
9. [Tooltips Informativos](#9-tooltips-informativos)
10. [Delete de Imagens](#10-delete-de-imagens)
11. [Sistema de Cache](#11-sistema-de-cache)
12. [Sanitizacao de Seguranca](#12-sanitizacao-de-seguranca)

---

## 1. ATIVACAO DO TEMA

### Descricao
Controla se o tema customizado esta ativo ou se o sistema usa o tema padrao do Krayin.

### Arquivos Envolvidos

| Arquivo | Linhas | Responsabilidade |
|---------|--------|------------------|
| [index.blade.php](../packages/Webkul/ThemeManager/Resources/views/admin/settings/theme/index.blade.php) | 155-192 | Formulario com select is_active |
| [ThemeController.php](../packages/Webkul/ThemeManager/src/Http/Controllers/ThemeController.php) | 144 | Validacao `nullable\|in:0,1` |
| [ThemeConfigRepository.php](../packages/Webkul/ThemeManager/src/Repositories/ThemeConfigRepository.php) | 239-253 | Conversao para boolean |
| [ThemeHelper.php](../packages/Webkul/ThemeManager/src/Helpers/ThemeHelper.php) | 73-76 | Metodo `isActive()` |
| [ThemeConfig.php](../packages/Webkul/ThemeManager/src/Models/ThemeConfig.php) | 67 | Cast `'is_active' => 'boolean'` |

### Interligacoes

```
[Form] is_active (0/1)
    |
    v
[Controller] validate() --> in:0,1
    |
    v
[Repository] update() --> converte para boolean
    |
    v
[Model] cast --> boolean
    |
    v
[Helper] isActive() --> condiciona CSS
    |
    v
[theme-styles.blade.php] --> aplica ou nao estilos
```

### Dependencias
- `ThemeConfig` model com cast boolean
- `ThemeHelper::isActive()` consultado em layouts

### Erros a NAO Cometer

| Erro | Consequencia | Solucao Correta |
|------|--------------|-----------------|
| Usar string "true"/"false" | Campo nao converte corretamente | Usar 0/1 no form, converter com `in_array()` no Repository |
| Nao invalidar cache apos mudanca | Usuario ve estado antigo | Sempre chamar `$this->themeHelper->clearCache()` |
| Verificar `is_active == 1` direto | Falha com cast boolean | Usar `(bool) $config->is_active` ou `isActive()` |

### Melhorias Possiveis
- [ ] Adicionar toggle switch no lugar do select para melhor UX
- [ ] Preview em tempo real do estado ativo/inativo
- [ ] Historico de ativacoes/desativacoes

---

## 2. SELECAO DE TEMA PREDEFINIDO

### Descricao
Permite selecionar um tema base que carrega cores, logos e configuracoes de login do arquivo `theme.json`.

### Arquivos Envolvidos

| Arquivo | Linhas | Responsabilidade |
|---------|--------|------------------|
| [index.blade.php](../packages/Webkul/ThemeManager/Resources/views/admin/settings/theme/index.blade.php) | 194-305, 1225-1350 | Grid de cards, preview de cores, JS selectTheme() |
| [ThemeController.php](../packages/Webkul/ThemeManager/src/Http/Controllers/ThemeController.php) | 36-62, 69-126 | `index()` prepara $themesForJs, `getAvailableThemes()` le diretorio |
| [ThemeConfigRepository.php](../packages/Webkul/ThemeManager/src/Repositories/ThemeConfigRepository.php) | 93-140, 372-524 | Detecta mudanca de tema, `loadThemeSettings()` |
| storage/app/public/themes/{slug}/theme.json | - | Configuracoes do tema |

### Interligacoes

```
[storage/themes/] --> File::directories()
    |
    v
[Controller] getAvailableThemes() --> le theme.json de cada pasta
    |
    v
[View] renderiza grid de cards com cores preview
    |
    v
[JS] selectTheme(slug) --> atualiza radio button + preview
    |
    v
[POST] selected_theme enviado
    |
    v
[Repository] detecta $newTheme !== $currentTheme
    |
    v
loadThemeSettings($slug) --> le theme.json
    |
    v
copyThemeAsset() --> copia logos/backgrounds para theme-manager/
    |
    v
Merge com $data --> salva no DB
```

### Dependencias
- Estrutura de diretorios em `storage/app/public/themes/`
- Arquivo `theme.json` valido em cada pasta de tema
- Metodo `copyThemeAsset()` para duplicar assets

### Erros a NAO Cometer

| Erro | Consequencia | Solucao Correta |
|------|--------------|-----------------|
| Nao validar slug com regex | Path traversal, injection | Usar `regex:/^[a-z0-9\\-_]+$/` |
| Manter valores do form antigo ao trocar tema | Cores antigas sobrescrevem tema novo | Filtrar apenas `selected_theme`, `is_active`, `_token` do form |
| Nao preservar campos `*_delete` | Usuario marca delete mas tema sobrescreve | Capturar delete flags ANTES de carregar theme.json |
| Assumir que theme.json existe | Erro fatal se arquivo ausente | Verificar `Storage::disk('public')->exists()` |
| Usar File:: em vez de Storage:: | Path absoluto vs relativo inconsistente | Padronizar em `Storage::disk('public')` |

### Melhorias Possiveis
- [ ] Validacao de theme.json com JSON Schema
- [ ] Preview completo do tema antes de aplicar
- [ ] Import/Export de temas (.zip)
- [ ] Marketplace de temas

---

## 3. GERENCIAMENTO DE CORES

### Descricao
Permite customizar 6 cores do sistema: primary, primary_dark, primary_light, success, warning, danger.

### Arquivos Envolvidos

| Arquivo | Linhas | Responsabilidade |
|---------|--------|------------------|
| [index.blade.php](../packages/Webkul/ThemeManager/Resources/views/admin/settings/theme/index.blade.php) | 307-510, 1323-1334 | Inputs color + text, sync JS |
| [ThemeController.php](../packages/Webkul/ThemeManager/src/Http/Controllers/ThemeController.php) | 136, 150-165 | Regex hex `^#([A-Fa-f0-9]{6}\|[A-Fa-f0-9]{3})$` |
| [ThemeHelper.php](../packages/Webkul/ThemeManager/src/Helpers/ThemeHelper.php) | 29, 81-98, 143-198 | Pattern, sanitizeHexColor(), getCssVariables() |
| [theme-styles.blade.php](../packages/Webkul/ThemeManager/Resources/views/components/theme-styles.blade.php) | 1-400 | Aplica vars CSS em elementos |

### Interligacoes

```
[Form] <input type="color"> + <input type="text">
    |
    v
[JS] Sincroniza valores entre os dois inputs
    |
    v
[Controller] Valida regex hex
    |
    v
[Repository] Salva string hex
    |
    v
[Helper] sanitizeHexColor() --> valida novamente
    |
    v
getCssVariables() --> gera :root { --primary-color: #xxx; }
    |
    v
[theme-styles] --> usa var(--primary-color) em seletores CSS
```

### Dependencias
- Regex identico no Controller e Helper
- Metodo `hexToRgb()` para gerar `--primary-rgb`

### Erros a NAO Cometer

| Erro | Consequencia | Solucao Correta |
|------|--------------|-----------------|
| Aceitar cor sem # | CSS invalido | Prepend # se ausente em sanitizeHexColor() |
| Nao sanitizar na saida | CSS injection | Sempre usar sanitizeHexColor() antes de outputar |
| Usar cores RGB ou HSL | Regex falha | Aceitar apenas HEX, converter se necessario |
| Sincronizar apenas color->text | Text editado nao atualiza picker | Sincronizar bidirecionalmente com regex check |

### Melhorias Possiveis
- [ ] Color picker avancado (paleta, eyedropper)
- [ ] Suporte a dark mode automatico (cores invertidas)
- [ ] Geracao automatica de primary_dark e primary_light a partir de primary
- [ ] Preview em tempo real das cores no admin

---

## 4. UPLOAD E GERENCIAMENTO DE LOGOS

### Descricao
Upload de 4 tipos de logo: main, light, icon, favicon. Inclui preview, delete e reset.

### Arquivos Envolvidos

| Arquivo | Linhas | Responsabilidade |
|---------|--------|------------------|
| [index.blade.php](../packages/Webkul/ThemeManager/Resources/views/admin/settings/theme/index.blade.php) | 512-727 | Inputs file, previews, checkboxes delete, botoes reset |
| [ThemeController.php](../packages/Webkul/ThemeManager/src/Http/Controllers/ThemeController.php) | 168-171 | Validacao mimes e max size |
| [ThemeConfigRepository.php](../packages/Webkul/ThemeManager/src/Repositories/ThemeConfigRepository.php) | 143-159, 161-230, 285-302, 295-302, 334-363 | Lista fileFields, processamento upload, delete, sanitizeSvg() |
| [ThemeHelper.php](../packages/Webkul/ThemeManager/src/Helpers/ThemeHelper.php) | 224-248 | getLogo(), getFavicon() |
| [theme-styles.blade.php](../packages/Webkul/ThemeManager/Resources/views/components/theme-styles.blade.php) | 405-440, 494-621 | CSS fallback + JS substituicao de logos |

### Interligacoes

```
[Form] <input type="file"> + preview + checkbox delete
    |
    v
[Controller] validate mimes:svg,png,jpg,jpeg,webp max:5120
    |
    v
[Repository] instanceof UploadedFile?
    |
    +--> Sim: deleteFile(antigo), sanitizeSvg() se SVG, storeAs()
    |
    +--> String? Veio do theme.json, manter
    |
    +--> *_delete? deleteFile(), set null
    |
    v
generateSafeFilename() --> {timestamp}_{field}_{random}.{ext}
    |
    v
Storage::disk('public')->put('theme-manager/'...)
    |
    v
[Helper] getLogo('main') --> asset('storage/theme-manager/...')
    |
    v
[theme-styles.blade.php] --> JS substitui img[src*="logo"]
```

### Dependencias
- Storage disk 'public' configurado
- Symlink `storage/app/public` -> `public/storage`
- ThemeAssetValidator (opcional, para validacao extra)

### Erros a NAO Cometer

| Erro | Consequencia | Solucao Correta |
|------|--------------|-----------------|
| Confiar em filename do usuario | Path traversal, XSS | Sempre usar generateSafeFilename() |
| Nao sanitizar SVG | XSS via script/onload | Usar sanitizeSvg() antes de salvar |
| Usar is_file() ou file_exists() | Path relativo vs absoluto | Usar `$data[$field] instanceof UploadedFile` |
| Deletar arquivo novo ao inves do antigo | Perda de arquivo | Deletar `$config->$field` (antigo), nao `$data[$field]` |
| Nao verificar Storage::disk | Arquivo salvo em lugar errado | Sempre especificar `disk('public')` |

### Melhorias Possiveis
- [ ] Crop/resize de imagens no upload
- [ ] Suporte a drag-and-drop
- [ ] Geracao automatica de favicon de diferentes tamanhos
- [ ] Lazy loading de previews

---

## 5. LOGIN PAGE BACKGROUND

### Descricao
Customiza o fundo da pagina de login com imagem, zoom e opacidade.

### Arquivos Envolvidos

| Arquivo | Linhas | Responsabilidade |
|---------|--------|------------------|
| [index.blade.php](../packages/Webkul/ThemeManager/Resources/views/admin/settings/theme/index.blade.php) | 729-887 | Upload, sliders zoom/opacity, toggle powered_by |
| [ThemeController.php](../packages/Webkul/ThemeManager/src/Http/Controllers/ThemeController.php) | 174-177 | Validacao mimes, integer min/max |
| [ThemeConfigRepository.php](../packages/Webkul/ThemeManager/src/Repositories/ThemeConfigRepository.php) | 256-266 | Clamp inteiros min/max |
| [ThemeHelper.php](../packages/Webkul/ThemeManager/src/Helpers/ThemeHelper.php) | 176-180, 255-274 | CSS vars login, getLoginConfig() |
| [theme-styles.blade.php](../packages/Webkul/ThemeManager/Resources/views/components/theme-styles.blade.php) | 445-490, 623-675 | CSS background + JS backup |
| [login.blade.php](../packages/Webkul/ThemeManager/Resources/views/admin/sessions/login.blade.php) | - | Consome configuracoes |

### Interligacoes

```
[Form] file + range(zoom) + range(opacity) + checkbox(powered_by)
    |
    v
[Repository] processa upload, clamp integers
    |
    v
[Helper] getCssVariables()
    |
    +--> Se login_bg_image existe:
    |    --theme-login-bg-url: url('...')
    |    --theme-login-bg-opacity: 0.5 (valor/100)
    |    --theme-login-bg-zoom: 1.0 (valor/100)
    |
    v
[theme-styles.blade.php]
    |
    +--> CSS: body { background-image: var(--theme-login-bg-url) }
    |         body::before { overlay com opacidade invertida }
    |
    +--> JS: Backup aplicando diretamente no body
    |
    v
[theme-styles.blade.php] linha 677-717
    |
    +--> JS: Se !login_show_powered_by, esconde elementos "Powered by"
```

### Dependencias
- Divisao correta: `zoom / 100`, `opacity / 100`
- Overlay usa opacidade invertida: `1 - opacity`
- Deteccao de pagina login via `window.location.pathname.includes('login')`

### Erros a NAO Cometer

| Erro | Consequencia | Solucao Correta |
|------|--------------|-----------------|
| Nao dividir por 100 | Zoom 10000%, opacity 5000% | `max(50, min(200, $zoom)) / 100` |
| Esquecer de remover overlay quando imagem e null | Overlay branco cobre tudo | CSS/JS condicionais: `@if($themeConfig->login_bg_image)` |
| Aplicar background em todas as paginas | Admin inteiro tem background | Verificar pathname inclui 'login' ou 'session' |
| Hardcodar gradient fallback | Background nao remove | Usar `background-image: none; background-color: #f3f4f6;` |

### Melhorias Possiveis
- [ ] Preview do background no admin
- [ ] Suporte a video background
- [ ] Gradients customizaveis
- [ ] Posicionamento do background (center, cover, contain)

---

## 6. LOGIN CARD CUSTOMIZADO

### Descricao
Customiza o card de login com background, overlay, titulo, subtitulo, sparkles e link de ajuda.

### Arquivos Envolvidos

| Arquivo | Linhas | Responsabilidade |
|---------|--------|------------------|
| [index.blade.php](../packages/Webkul/ThemeManager/Resources/views/admin/settings/theme/index.blade.php) | 889-1174, 1239-1243 | Toggle, campos, JS toggleLoginCardOptions() |
| [ThemeController.php](../packages/Webkul/ThemeManager/src/Http/Controllers/ThemeController.php) | 180-194 | Validacao rgba regex, email, strings |
| [ThemeConfigRepository.php](../packages/Webkul/ThemeManager/src/Repositories/ThemeConfigRepository.php) | 239-245 | Conversao booleans |
| [ThemeHelper.php](../packages/Webkul/ThemeManager/src/Helpers/ThemeHelper.php) | 36, 103-120, 183-187, 255-274 | Pattern rgba, sanitize, CSS vars, getLoginConfig() |
| [theme-styles.blade.php](../packages/Webkul/ThemeManager/Resources/views/components/theme-styles.blade.php) | 723-940 | JS aplica card customizado |

### Interligacoes

```
[Form] toggle login_card_enabled
    |
    v
[JS] toggleLoginCardOptions() --> mostra/esconde secao
    |
    v
[Repository] converte booleans, processa uploads
    |
    v
[Helper] getLoginConfig() --> array sanitizado
    |
    v
[theme-styles.blade.php] @if($themeConfig->login_card_enabled)
    |
    v
[JS] Encontra card .box-shadow.rounded-md.bg-white
    |
    +--> Aplica background + overlay
    |
    +--> Substitui titulo "Sign in" por titulo/subtitulo custom
    |
    +--> Adiciona sparkles animados (se habilitado)
    |
    +--> Adiciona link de ajuda com email (se habilitado)
    |
    +--> Injeta custom_code (HTML/CSS/JS)
```

### Dependencias
- Regex RGBA: `/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(,\s*(0|1|0?\.\d+))?\s*\)/`
- Seletor CSS do card login: `.box-shadow.rounded-md.bg-white`
- Titulo original: `p.text-xl.font-bold`

### Erros a NAO Cometer

| Erro | Consequencia | Solucao Correta |
|------|--------------|-----------------|
| Aplicar overlay sem imagem | Card fica com cor solida estranha | `if (config.bgImage)` antes de criar overlay |
| Nao escapar titulo/subtitulo | XSS | `sanitizeText()` remove caracteres perigosos |
| Executar custom_code sem sanitizar | XSS, code injection | CUIDADO: Atualmente executa `eval()`. Considerar remover ou sanitizar |
| Sparkles com z-index errado | Cobrem inputs | Usar `pointer-events: none` |
| Nao verificar email valido | Mailto quebrado | `filter_var($email, FILTER_VALIDATE_EMAIL)` |

### Melhorias Possiveis
- [ ] Remover ou sanitizar login_card_custom_code (risco de seguranca)
- [ ] WYSIWYG editor para titulo/subtitulo
- [ ] Mais opcoes de animacao alem de sparkles
- [ ] Posicionamento do link de ajuda customizavel

---

## 7. EMPTY STATES

### Descricao
SVGs customizados para estados vazios de diferentes modulos (leads, persons, etc).

### Arquivos Envolvidos

| Arquivo | Linhas | Responsabilidade |
|---------|--------|------------------|
| [index.blade.php](../packages/Webkul/ThemeManager/Resources/views/admin/settings/theme/index.blade.php) | 1176-1218 | Grid de uploads SVG com @foreach |
| [ThemeController.php](../packages/Webkul/ThemeManager/src/Http/Controllers/ThemeController.php) | 197-205 | Validacao `mimes:svg\|max:2048` |
| [ThemeConfigRepository.php](../packages/Webkul/ThemeManager/src/Repositories/ThemeConfigRepository.php) | 150-158, 334-363 | Lista campos, sanitizeSvg() |
| [ThemeHelper.php](../packages/Webkul/ThemeManager/src/Helpers/ThemeHelper.php) | 281-298 | getEmptyState($type) com whitelist |

### Interligacoes

```
[Form] @foreach(['activities', 'calls', ...]) input file
    |
    v
[Controller] Valida cada empty_state_* como SVG
    |
    v
[Repository] sanitizeSvg() --> remove scripts, eventos
    |
    v
[Helper] getEmptyState('leads')
    |
    +--> Valida tipo contra whitelist
    |
    +--> Retorna asset() URL ou null
    |
    v
[Componentes Krayin] usam URL para exibir SVG
```

### Dependencias
- Whitelist de tipos em ThemeHelper: `['activities', 'calls', 'emails', 'meetings', 'notes', 'organizations', 'persons', 'leads', 'products']`
- Apenas SVG permitido (seguranca)

### Erros a NAO Cometer

| Erro | Consequencia | Solucao Correta |
|------|--------------|-----------------|
| Aceitar formatos alem de SVG | Imagem raster em contexto vetorial | Validar `mimes:svg` apenas |
| Nao sanitizar SVG | XSS via `<script>`, `onload`, etc | Usar sanitizeSvg() obrigatoriamente |
| Aceitar tipo nao listado | Path traversal | Validar contra whitelist em getEmptyState() |
| Usar tipo dinamico no form | Injection de campo | @foreach com array fixo |

### Melhorias Possiveis
- [ ] Preview de SVGs no admin
- [ ] Biblioteca de SVGs pre-definidos para escolha
- [ ] Editor visual de cores do SVG
- [ ] Suporte a Lottie animations

---

## 8. RESET FIELD (RESTAURAR CAMPO)

### Descricao
Permite restaurar um campo de imagem individual para o valor definido no theme.json do tema selecionado.

### Arquivos Envolvidos

| Arquivo | Linhas | Responsabilidade |
|---------|--------|------------------|
| [index.blade.php](../packages/Webkul/ThemeManager/Resources/views/admin/settings/theme/index.blade.php) | 542-549, 593-600, 644-651, 695-702, 759-766, 953-960, 1291-1314 | Botoes reset, JS resetField() |
| [web.php](../packages/Webkul/ThemeManager/src/Routes/web.php) | 19 | Rota POST reset-field |
| [ThemeController.php](../packages/Webkul/ThemeManager/src/Http/Controllers/ThemeController.php) | 303-343 | Metodo resetField() com Rule::in() |
| [ThemeConfigRepository.php](../packages/Webkul/ThemeManager/src/Repositories/ThemeConfigRepository.php) | 657-777 | resetFieldToTheme() - le theme.json, copia asset |

### Interligacoes

```
[Botao Reset] onclick="resetField('logo_main')"
    |
    v
[JS] Cria form dinamico com CSRF + field_name
    |
    v
[POST] /admin/settings/theme/reset-field
    |
    v
[Controller] Valida field_name contra whitelist Rule::in()
    |
    v
[Repository] resetFieldToTheme($fieldName)
    |
    +--> Obtem tema selecionado
    |
    +--> Se 'default': deleta arquivo, seta null
    |
    +--> Se outro tema: le theme.json
    |        |
    |        +--> Mapeia campo para path no JSON
    |        |    (login_bg_image -> login.bg_image)
    |        |
    |        +--> Copia asset do tema para theme-manager/
    |        |
    |        +--> Atualiza campo no DB
    |
    +--> Invalida cache
    |
    v
[Flash] Mensagem de sucesso ou warning
    |
    v
[Redirect] back()
```

### Dependencias
- Whitelist de campos resetaveis: `['logo_main', 'logo_light', 'logo_icon', 'favicon', 'login_bg_image', 'login_card_bg_image']`
- Mapeamento de campos aninhados: `login_bg_image` -> `['login', 'bg_image']`
- Metodo `copyThemeAsset()` reutilizado

### Erros a NAO Cometer

| Erro | Consequencia | Solucao Correta |
|------|--------------|-----------------|
| Aceitar qualquer field_name | Injection, manipulacao de campos | Usar `Rule::in()` com whitelist explicita |
| Nao verificar se arquivo existe no tema | Erro fatal | Verificar com Storage::exists() antes de copiar |
| Retornar true quando tema e 'default' | Mensagem enganosa | Retornar false, mostrar warning "campo foi limpo" |
| Esquecer de deletar arquivo antigo | Acumulo de arquivos orfaos | Chamar deleteFile() antes de qualquer operacao |
| Nao invalidar cache | Usuario ve valor antigo | clearCache() em todos os caminhos |

### Melhorias Possiveis
- [ ] Reset em batch (varios campos de uma vez)
- [ ] Confirmacao visual do valor que sera restaurado
- [ ] Undo da operacao de reset
- [ ] Reset de cores tambem (nao apenas imagens)

---

## 9. TOOLTIPS INFORMATIVOS

### Descricao
Icones (i) azuis ao lado de labels que exibem popup com informacao contextual no hover.

### Arquivos Envolvidos

| Arquivo | Linhas | Responsabilidade |
|---------|--------|------------------|
| [index.blade.php](../packages/Webkul/ThemeManager/Resources/views/admin/settings/theme/index.blade.php) | 7-120, 168-171, 322-325, ... | CSS em @pushOnce('styles'), HTML spans |

### Interligacoes

```
[Blade] @pushOnce('styles')
    |
    v
[Layout] @stack('styles') no <head>
    |
    v
[CSS] .theme-tooltip { position: relative }
      .theme-tooltip-content { display: none }
      .theme-tooltip:hover .theme-tooltip-content { display: block }
    |
    v
[HTML] <span class="theme-tooltip">
          <span class="theme-tooltip-icon">i</span>
          <span class="theme-tooltip-content">Texto...</span>
       </span>
```

### Dependencias
- Blade directive `@pushOnce('styles')` para evitar CSS duplicado
- Layout base deve ter `@stack('styles')` no `<head>`

### Erros a NAO Cometer

| Erro | Consequencia | Solucao Correta |
|------|--------------|-----------------|
| Colocar `<style>` solto no template | CSS nao carrega se incluido depois do `</head>` | Usar `@pushOnce('styles')` |
| Usar opacity/visibility para show/hide | Problemas de transicao em alguns browsers | Usar `display: none` / `display: block !important` |
| z-index baixo | Tooltip aparece atras de outros elementos | Usar `z-index: 9999 !important` |
| position: absolute sem relative no pai | Tooltip posicionado em relacao ao body | Container deve ter `position: relative` |
| Nao tratar mobile | Tooltip cortado ou fora da tela | `@media (max-width: 640px)` com `position: fixed` |

### Melhorias Possiveis
- [ ] Tooltips com delay para evitar flickering
- [ ] Suporte a markdown no conteudo
- [ ] Tooltips clicaveis para mais informacoes
- [ ] Internacionalizacao dos textos

---

## 10. DELETE DE IMAGENS

### Descricao
Checkboxes que permitem remover imagens existentes ao salvar o formulario.

### Arquivos Envolvidos

| Arquivo | Linhas | Responsabilidade |
|---------|--------|------------------|
| [index.blade.php](../packages/Webkul/ThemeManager/Resources/views/admin/settings/theme/index.blade.php) | 552-557, 603-608, ... | Checkboxes `*_delete` |
| [ThemeConfigRepository.php](../packages/Webkul/ThemeManager/src/Repositories/ThemeConfigRepository.php) | 102-106, 128-131, 161-171, 233-236 | Captura e processa delete flags |

### Interligacoes

```
[Form] <input type="checkbox" name="logo_main_delete" value="1">
    |
    v
[Controller] Passa para Repository sem validacao especifica
    |
    v
[Repository] update()
    |
    +--> Captura $deleteFields ANTES de loadThemeSettings()
    |
    +--> Processa cada fileField:
    |    if (isset($data["{$field}_delete"]) && $data["{$field}_delete"]) {
    |        $this->deleteFile($config->$field);
    |        $data[$field] = null;
    |        unset($data["{$field}_delete"]);
    |    }
    |
    +--> Remove delete checkboxes do $data final
    |    array_filter(..., !str_ends_with($key, '_delete'))
    |
    v
[Model] update() com campo = null
    |
    v
[Storage] Arquivo fisico deletado
```

### Dependencias
- Ordem de processamento: delete ANTES de upload
- Campos `*_delete` NAO existem no Model/DB

### Erros a NAO Cometer

| Erro | Consequencia | Solucao Correta |
|------|--------------|-----------------|
| Processar delete DEPOIS de upload | Novo arquivo e imediatamente deletado | Verificar delete ANTES de checar instanceof UploadedFile |
| Esquecer de unset delete flag | Erro "Unknown column" no Model::update() | `unset($data["{$field}_delete"])` |
| Nao deletar arquivo fisico | Acumulo de arquivos orfaos | `$this->deleteFile($config->$field)` |
| Deletar $data[$field] em vez de $config->$field | Deleta o arquivo errado (se upload simultaneo) | Sempre usar valor do DB atual |
| Nao restaurar delete flags apos loadThemeSettings | Delete ignorado ao trocar de tema | Merge $deleteFields apos carregar tema |

### Melhorias Possiveis
- [ ] Confirmacao antes de deletar
- [ ] Lixeira com recuperacao (soft delete)
- [ ] Indicacao visual mais clara de que arquivo sera deletado

---

## 11. SISTEMA DE CACHE

### Descricao
Cache de configuracoes do tema para evitar queries repetidas ao DB.

### Arquivos Envolvidos

| Arquivo | Linhas | Responsabilidade |
|---------|--------|------------------|
| [ThemeHelper.php](../packages/Webkul/ThemeManager/src/Helpers/ThemeHelper.php) | 15-22, 44-66 | cacheKey, cacheTtl, getConfig(), clearCache() |
| [ThemeConfigRepository.php](../packages/Webkul/ThemeManager/src/Repositories/ThemeConfigRepository.php) | 271-277 | Invalida cache apos update |
| [ThemeController.php](../packages/Webkul/ThemeManager/src/Http/Controllers/ThemeController.php) | 250, 284 | Invalida cache em restore/rollback |

### Interligacoes

```
[Requisicao] Precisa de config do tema
    |
    v
[ThemeHelper] getConfig()
    |
    v
[Cache] Cache::remember('theme_config', 3600, ...)
    |
    +--> Cache hit? Retorna array cacheado
    |
    +--> Cache miss? ThemeConfig::getInstance()->toArray()
    |
    v
[ThemeHelper] Hydrata model de array
    |
    +--> $config = new ThemeConfig;
    |    $config->forceFill($configArray);
    |    $config->exists = true;
    |
    v
Retorna ThemeConfig hydratatado

---

[Update/Delete] Qualquer mudanca
    |
    v
[Repository/Controller] $this->themeHelper->clearCache()
    |
    v
[Cache] Cache::forget('theme_config')
```

### Dependencias
- Cache driver configurado (file, redis, memcached)
- TTL: 3600 segundos (1 hora)
- Chave: `theme_config`

### Erros a NAO Cometer

| Erro | Consequencia | Solucao Correta |
|------|--------------|-----------------|
| Cachear o Model diretamente | Erro de serializacao Eloquent | Cachear `->toArray()`, rehydratar depois |
| Esquecer clearCache() em algum path | Usuario ve dados antigos | Chamar em TODOS os metodos que modificam config |
| TTL muito longo | Mudancas demoram a refletir | 1 hora e equilibrio; invalidar explicitamente e melhor |
| TTL muito curto | Performance ruim | Nao usar TTL < 60s em producao |
| Usar cache key dinamica | Multiplas configs cacheadas | Usar key fixa, config e singleton |

### Melhorias Possiveis
- [ ] Cache tags para invalidacao seletiva
- [ ] Warming de cache apos deploy
- [ ] Monitoramento de hit/miss rate
- [ ] Cache por usuario (se temas por usuario no futuro)

---

## 12. SANITIZACAO DE SEGURANCA

### Descricao
Protecoes contra XSS, CSS injection e outras vulnerabilidades.

### Arquivos Envolvidos

| Arquivo | Linhas | Responsabilidade |
|---------|--------|------------------|
| [ThemeController.php](../packages/Webkul/ThemeManager/src/Http/Controllers/ThemeController.php) | 136-141 | Regex hex e rgba na validacao |
| [ThemeConfigRepository.php](../packages/Webkul/ThemeManager/src/Repositories/ThemeConfigRepository.php) | 334-363 | sanitizeSvg() |
| [ThemeHelper.php](../packages/Webkul/ThemeManager/src/Helpers/ThemeHelper.php) | 29-36, 81-135 | Patterns, sanitizeHexColor(), sanitizeRgbaColor(), sanitizeText() |

### Sanitizacoes Implementadas

| Tipo | Metodo | Protege Contra |
|------|--------|----------------|
| Cores HEX | `sanitizeHexColor()` | CSS injection via `color: url(javascript:...)` |
| Cores RGBA | `sanitizeRgbaColor()` | CSS injection via expressoes |
| Texto | `sanitizeText()` | XSS via `<script>`, caracteres CSS |
| SVG | `sanitizeSvg()` | XSS via `<script>`, `onload`, `javascript:`, `foreignObject` |
| Slug tema | Regex `^[a-z0-9\-_]+$` | Path traversal |
| Email | `filter_var(FILTER_VALIDATE_EMAIL)` | Mailto injection |
| Empty state type | Whitelist | Path traversal |
| Reset field name | `Rule::in()` | Field manipulation |

### Interligacoes

```
[Input] Dado do usuario
    |
    v
[Controller] Validacao de formato (regex, mimes, in)
    |
    v
[Repository] Sanitizacao de conteudo (sanitizeSvg)
    |
    v
[Helper] Sanitizacao na saida (sanitizeHexColor, sanitizeText)
    |
    v
[View] Output seguro
```

### Erros a NAO Cometer

| Erro | Consequencia | Solucao Correta |
|------|--------------|-----------------|
| Sanitizar apenas na entrada | Dados legados nao sanitizados | Sanitizar na SAIDA tambem |
| Confiar em validacao para seguranca | Validacao pode ser bypassed | Sanitizar independente de validacao |
| Usar htmlspecialchars para CSS | Nao protege contra CSS injection | Usar regex especifico |
| Permitir CSS custom global | Atacante estiliza qualquer elemento | Escopar com seletor especifico |
| Executar JS custom com eval() | Code execution | Remover ou sanitizar severamente |

### Melhorias Possiveis
- [ ] CSP (Content Security Policy) headers
- [ ] Remover login_card_custom_code ou usar sandbox
- [ ] Rate limiting em uploads
- [ ] Verificacao de assinatura de arquivos (magic bytes)
- [ ] Logging de tentativas de injection

---

## Apendice A: Fluxo Completo de Salvamento

```
[User] Clica "Salvar"
    |
    v
[Form] POST /admin/settings/theme
    |
    v
[Middleware] web, admin_locale, user (auth)
    |
    v
[Controller::update()]
    |
    +--> validate() - todos os campos
    |
    +--> Event::dispatch('theme.update.before')
    |
    +--> array_merge($request->all(), $request->allFiles())
    |
    v
[Repository::update()]
    |
    +--> get() config atual
    |
    +--> Tema mudou? loadThemeSettings()
    |
    +--> Para cada fileField:
    |    - *_delete? deleteFile, set null
    |    - instanceof UploadedFile? validate, delete old, sanitize, store
    |    - string? manter (veio do tema)
    |    - null? manter null (delete)
    |    - else? unset (nao mudar)
    |
    +--> Converter booleans
    |
    +--> Clamp integers
    |
    +--> $config->update($data)
    |
    +--> clearCache()
    |
    v
[Controller]
    |
    +--> Event::dispatch('theme.update.after')
    |
    +--> session()->flash('success')
    |
    +--> redirect()->back()
```

---

## Apendice B: Checklist de Desenvolvimento

### Ao Adicionar Novo Campo

- [ ] Adicionar coluna na migration
- [ ] Adicionar em $fillable do Model
- [ ] Adicionar cast se necessario (boolean, integer)
- [ ] Adicionar validacao no Controller
- [ ] Adicionar processamento no Repository (se arquivo)
- [ ] Adicionar getter no Helper (se necessario)
- [ ] Adicionar input no index.blade.php
- [ ] Adicionar CSS/JS no theme-styles.blade.php (se aplicavel)
- [ ] Adicionar tooltip explicativo
- [ ] Testar com cache limpo
- [ ] Documentar em THEME_FIELDS_MAP.md

### Ao Modificar Funcionalidade Existente

- [ ] Verificar todos os arquivos envolvidos
- [ ] Manter compatibilidade com dados existentes
- [ ] Invalidar cache em todos os paths
- [ ] Testar edge cases (null, vazio, invalido)
- [ ] Atualizar documentacao

---

**Documento gerado em:** Dezembro 2025
**Autor:** Claude Code
**Ultima revisao:** Analise minuciosa de codigo fonte
