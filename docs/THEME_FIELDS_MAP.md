# Theme Manager - Mapeamento Completo de Campos

> **Versao:** 1.0.0
> **Atualizado:** Dezembro 2025
> **Total de Campos:** 37 (dados) + 15 (auxiliares delete)

---

## Indice

1. [Controle Geral](#1-controle-geral)
2. [Cores](#2-cores)
3. [Logos e Favicon](#3-logos-e-favicon)
4. [Login Page - Background](#4-login-page---background)
5. [Login Card Customizado](#5-login-card-customizado)
6. [Empty States](#6-empty-states)
7. [Campos Internos/Sistema](#7-campos-internossistema)
8. [Campos Auxiliares (Delete)](#8-campos-auxiliares-delete)

---

## 1. CONTROLE GERAL

### 1.1 `is_active`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `is_active` |
| **Label no Form** | "Ativar Customizacao Visual" |
| **Tipo de Dado** | `boolean` |
| **Tipo de Input** | `<select>` (0/1) |
| **Validacao Controller** | `nullable\|in:0,1` |
| **Valor Padrao (DB)** | `false` |
| **Valor Padrao (Form)** | `0` |
| **Coluna DB** | `boolean('is_active')->default(false)` |
| **Cast no Model** | `'is_active' => 'boolean'` |
| **Usado em CSS/JS** | `ThemeHelper::isActive()` - Condiciona aplicacao de todo o tema |
| **Tooltip** | "Quando ativado, todas as customizacoes visuais serao aplicadas ao sistema. Desative para voltar ao tema padrao do Krayin." |
| **Arquivos Relacionados** | `ThemeHelper.php:73-76`, `theme-styles.blade.php` (condicional) |

---

### 1.2 `selected_theme`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `selected_theme` |
| **Label no Form** | "Tema Predefinido" |
| **Tipo de Dado** | `string` |
| **Tipo de Input** | Radio buttons em cards visuais |
| **Validacao Controller** | `nullable\|string\|max:50\|regex:/^[a-z0-9\\-_]+$/` |
| **Valor Padrao (DB)** | `null` (assume 'default') |
| **Valor Padrao (Form)** | `'default'` |
| **Coluna DB** | `string('selected_theme', 50)->nullable()` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | Carrega `theme.json` do tema selecionado, copia assets para `theme-manager/` |
| **Tooltip** | N/A (secao tem descricao propria) |
| **Arquivos Relacionados** | `ThemeController.php:69-126`, `ThemeConfigRepository.php:94-139`, `index.blade.php:205-256` |

---

## 2. CORES

### 2.1 `color_primary`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `color_primary` |
| **Label no Form** | "Cor Primaria" |
| **Tipo de Dado** | `string` (hex color) |
| **Tipo de Input** | `<input type="color">` + `<input type="text">` |
| **Validacao Controller** | `nullable\|string\|max:7\|regex:/^#([A-Fa-f0-9]{6}\|[A-Fa-f0-9]{3})$/` |
| **Valor Padrao (DB)** | `'#1E40AF'` |
| **Valor Padrao (Form)** | `'#1E40AF'` |
| **Coluna DB** | `string('color_primary', 20)->default('#1E40AF')` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | `--primary-color`, `--primary-rgb` em `:root`. Aplicado em: botoes, links, badges, focus states |
| **Tooltip** | "Cor principal da marca. Usada em botoes, links e elementos de destaque em todo o sistema." |
| **Arquivos Relacionados** | `ThemeHelper.php:148,157`, `theme-styles.blade.php:9-44,50-57` |

---

### 2.2 `color_primary_dark`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `color_primary_dark` |
| **Label no Form** | "Cor Primaria Escura" |
| **Tipo de Dado** | `string` (hex color) |
| **Tipo de Input** | `<input type="color">` + `<input type="text">` |
| **Validacao Controller** | `nullable\|string\|max:7\|regex:/^#([A-Fa-f0-9]{6}\|[A-Fa-f0-9]{3})$/` |
| **Valor Padrao (DB)** | `'#1E3A8A'` |
| **Valor Padrao (Form)** | `'#1E3A8A'` |
| **Coluna DB** | `string('color_primary_dark', 20)->default('#1E3A8A')` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | `--primary-dark-color`. Aplicado em: hover de botoes, tooltips, elementos interativos |
| **Tooltip** | "Variacao escura da cor primaria. Usada em estados hover de botoes e elementos interativos." |
| **Arquivos Relacionados** | `ThemeHelper.php:149,158`, `theme-styles.blade.php:17-23,264-270` |

---

### 2.3 `color_primary_light`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `color_primary_light` |
| **Label no Form** | "Cor Primaria Clara" |
| **Tipo de Dado** | `string` (hex color) |
| **Tipo de Input** | `<input type="color">` + `<input type="text">` |
| **Validacao Controller** | `nullable\|string\|max:7\|regex:/^#([A-Fa-f0-9]{6}\|[A-Fa-f0-9]{3})$/` |
| **Valor Padrao (DB)** | `'#3B82F6'` |
| **Valor Padrao (Form)** | `'#3B82F6'` |
| **Coluna DB** | `string('color_primary_light', 20)->default('#3B82F6')` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | `--primary-light-color`. Aplicado em: backgrounds sutis, menus ativos, focus rings |
| **Tooltip** | "Variacao clara da cor primaria. Usada em backgrounds sutis, bordas e elementos secundarios." |
| **Arquivos Relacionados** | `ThemeHelper.php:150,159`, `theme-styles.blade.php:72-74,93-110` |

---

### 2.4 `color_success`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `color_success` |
| **Label no Form** | "Cor de Sucesso" |
| **Tipo de Dado** | `string` (hex color) |
| **Tipo de Input** | `<input type="color">` + `<input type="text">` |
| **Validacao Controller** | `nullable\|string\|max:7\|regex:/^#([A-Fa-f0-9]{6}\|[A-Fa-f0-9]{3})$/` |
| **Valor Padrao (DB)** | `'#10B981'` |
| **Valor Padrao (Form)** | `'#10B981'` |
| **Coluna DB** | `string('color_success', 20)->default('#10B981')` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | `--success-color`, `--success-rgb`. Aplicado em: alertas sucesso, badges, status indicators |
| **Tooltip** | "Cor para indicar sucesso. Usada em mensagens de confirmacao, badges de status positivo e icones de check." |
| **Arquivos Relacionados** | `ThemeHelper.php:151,163-164`, `theme-styles.blade.php:130-143,324-327` |

---

### 2.5 `color_warning`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `color_warning` |
| **Label no Form** | "Cor de Alerta" |
| **Tipo de Dado** | `string` (hex color) |
| **Tipo de Input** | `<input type="color">` + `<input type="text">` |
| **Validacao Controller** | `nullable\|string\|max:7\|regex:/^#([A-Fa-f0-9]{6}\|[A-Fa-f0-9]{3})$/` |
| **Valor Padrao (DB)** | `'#F59E0B'` |
| **Valor Padrao (Form)** | `'#F59E0B'` |
| **Coluna DB** | `string('color_warning', 20)->default('#F59E0B')` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | `--warning-color`, `--warning-rgb`. Aplicado em: alertas warning, badges pendencia |
| **Tooltip** | "Cor para alertas e avisos. Usada em mensagens de atencao, badges de pendencia e notificacoes." |
| **Arquivos Relacionados** | `ThemeHelper.php:152,166-167`, `theme-styles.blade.php:145-158,329-332` |

---

### 2.6 `color_danger`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `color_danger` |
| **Label no Form** | "Cor de Perigo" |
| **Tipo de Dado** | `string` (hex color) |
| **Tipo de Input** | `<input type="color">` + `<input type="text">` |
| **Validacao Controller** | `nullable\|string\|max:7\|regex:/^#([A-Fa-f0-9]{6}\|[A-Fa-f0-9]{3})$/` |
| **Valor Padrao (DB)** | `'#EF4444'` |
| **Valor Padrao (Form)** | `'#EF4444'` |
| **Coluna DB** | `string('color_danger', 20)->default('#EF4444')` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | `--danger-color`, `--danger-rgb`. Aplicado em: alertas erro, botoes delete, validacoes |
| **Tooltip** | "Cor para erros e acoes destrutivas. Usada em mensagens de erro, botoes de exclusao e validacoes." |
| **Arquivos Relacionados** | `ThemeHelper.php:153,169-172`, `theme-styles.blade.php:160-173,334-337` |

---

## 3. LOGOS E FAVICON

### 3.1 `logo_main`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `logo_main` |
| **Label no Form** | "Logo Principal" |
| **Tipo de Dado** | `string` (filename) |
| **Tipo de Input** | `<input type="file">` + preview + checkbox delete + botao reset |
| **Validacao Controller** | `nullable\|file\|mimes:svg,png,jpg,jpeg,webp\|max:5120` |
| **Valor Padrao (DB)** | `null` |
| **Valor Padrao (Form)** | N/A (exibe preview se existir) |
| **Coluna DB** | `string('logo_main', 500)->nullable()` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | Via JS substitui `img[alt="Krayin CRM"]`, `img.h-10[src*="logo"]` |
| **Tooltip** | "Logo principal exibido no header. Recomendado: PNG/SVG com fundo transparente, altura max. 40px." |
| **Arquivos Relacionados** | `ThemeHelper.php:224-234`, `theme-styles.blade.php:409-422,499-538` |
| **Storage Path** | `storage/app/public/theme-manager/{timestamp}_{field}_{random}.{ext}` |
| **Reset Button** | Sim - `resetField('logo_main')` |

---

### 3.2 `logo_light`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `logo_light` |
| **Label no Form** | "Logo Claro (Dark Mode)" |
| **Tipo de Dado** | `string` (filename) |
| **Tipo de Input** | `<input type="file">` + preview + checkbox delete + botao reset |
| **Validacao Controller** | `nullable\|file\|mimes:svg,png,jpg,jpeg,webp\|max:5120` |
| **Valor Padrao (DB)** | `null` |
| **Valor Padrao (Form)** | N/A (exibe preview se existir) |
| **Coluna DB** | `string('logo_light', 500)->nullable()` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | Via JS substitui `img[src*="dark-logo"]`, `img[src*="light-logo"]` |
| **Tooltip** | "Logo para modo escuro. Deve ter cores claras para contraste com fundos escuros." |
| **Arquivos Relacionados** | `ThemeHelper.php:224-234`, `theme-styles.blade.php:433-438,576-589` |
| **Storage Path** | `storage/app/public/theme-manager/{timestamp}_{field}_{random}.{ext}` |
| **Reset Button** | Sim - `resetField('logo_light')` |

---

### 3.3 `logo_icon`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `logo_icon` |
| **Label no Form** | "Icone do Logo" |
| **Tipo de Dado** | `string` (filename) |
| **Tipo de Input** | `<input type="file">` + preview + checkbox delete + botao reset |
| **Validacao Controller** | `nullable\|file\|mimes:svg,png,jpg,jpeg,ico\|max:5120` |
| **Valor Padrao (DB)** | `null` |
| **Valor Padrao (Form)** | N/A (exibe preview se existir) |
| **Coluna DB** | `string('logo_icon', 500)->nullable()` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | Via JS substitui `img[src*="/cache/logo"]`, logos mobile |
| **Tooltip** | "Icone quadrado para menu recolhido e mobile. Recomendado: 64x64px, PNG/SVG." |
| **Arquivos Relacionados** | `ThemeHelper.php:224-234`, `theme-styles.blade.php:425-431,553-574` |
| **Storage Path** | `storage/app/public/theme-manager/{timestamp}_{field}_{random}.{ext}` |
| **Reset Button** | Sim - `resetField('logo_icon')` |

---

### 3.4 `favicon`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `favicon` |
| **Label no Form** | "Favicon" |
| **Tipo de Dado** | `string` (filename) |
| **Tipo de Input** | `<input type="file">` + preview + checkbox delete + botao reset |
| **Validacao Controller** | `nullable\|file\|mimes:ico,png,svg\|max:1024` |
| **Valor Padrao (DB)** | `null` |
| **Valor Padrao (Form)** | N/A (exibe preview se existir) |
| **Coluna DB** | `string('favicon', 500)->nullable()` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | Via JS atualiza `link[rel="icon"]` no `<head>` |
| **Tooltip** | "Icone da aba do navegador. Recomendado: 32x32px ou 16x16px, formato .ico ou .png." |
| **Arquivos Relacionados** | `ThemeHelper.php:239-248`, `theme-styles.blade.php:591-621` |
| **Storage Path** | `storage/app/public/theme-manager/{timestamp}_{field}_{random}.{ext}` |
| **Reset Button** | Sim - `resetField('favicon')` |

---

## 4. LOGIN PAGE - BACKGROUND

### 4.1 `login_bg_image`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `login_bg_image` |
| **Label no Form** | "Imagem de Fundo" |
| **Tipo de Dado** | `string` (filename) |
| **Tipo de Input** | `<input type="file">` + preview + checkbox delete + botao reset |
| **Validacao Controller** | `nullable\|file\|mimes:jpg,jpeg,png,webp\|max:10240` |
| **Valor Padrao (DB)** | `null` |
| **Valor Padrao (Form)** | N/A (exibe preview se existir) |
| **Coluna DB** | `string('login_bg_image', 500)->nullable()` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | `--theme-login-bg-url`, aplica `background-image` no body da pagina login |
| **Tooltip** | "Imagem de fundo da pagina de login. Recomendado: alta resolucao (1920x1080+), JPG/PNG." |
| **Arquivos Relacionados** | `ThemeHelper.php:176-180,260`, `theme-styles.blade.php:445-490,623-675` |
| **Storage Path** | `storage/app/public/theme-manager/{timestamp}_{field}_{random}.{ext}` |
| **Reset Button** | Sim - `resetField('login_bg_image')` |

---

### 4.2 `login_bg_zoom`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `login_bg_zoom` |
| **Label no Form** | "Zoom do Background" |
| **Tipo de Dado** | `integer` |
| **Tipo de Input** | `<input type="range">` (slider) |
| **Validacao Controller** | `nullable\|integer\|min:50\|max:200` |
| **Valor Padrao (DB)** | `100` |
| **Valor Padrao (Form)** | `100` |
| **Coluna DB** | `integer('login_bg_zoom')->default(100)` |
| **Cast no Model** | `'login_bg_zoom' => 'integer'` |
| **Usado em CSS/JS** | `--theme-login-bg-zoom` (valor / 100), aplica `background-size: {zoom}%` |
| **Tooltip** | "Zoom da imagem de fundo. 100% = tamanho original. Valores maiores ampliam a imagem." |
| **Arquivos Relacionados** | `ThemeHelper.php:179,261`, `ThemeConfigRepository.php:257`, `theme-styles.blade.php:453,631` |
| **Range** | 50% - 150% (step: 5) |

---

### 4.3 `login_bg_opacity`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `login_bg_opacity` |
| **Label no Form** | "Opacidade do Background" |
| **Tipo de Dado** | `integer` |
| **Tipo de Input** | `<input type="range">` (slider) |
| **Validacao Controller** | `nullable\|integer\|min:0\|max:100` |
| **Valor Padrao (DB)** | `50` |
| **Valor Padrao (Form)** | `50` |
| **Coluna DB** | `integer('login_bg_opacity')->default(50)` |
| **Cast no Model** | `'login_bg_opacity' => 'integer'` |
| **Usado em CSS/JS** | `--theme-login-bg-opacity` (valor / 100), aplica overlay branco invertido |
| **Tooltip** | "Opacidade da imagem. 0% = invisivel, 100% = totalmente visivel. Use valores menores para destacar o card de login." |
| **Arquivos Relacionados** | `ThemeHelper.php:178,262`, `ThemeConfigRepository.php:258`, `theme-styles.blade.php:467,632,643` |
| **Range** | 0% - 100% (step: 5) |

---

### 4.4 `login_show_powered_by`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `login_show_powered_by` |
| **Label no Form** | "Exibir 'Powered by Krayin'" |
| **Tipo de Dado** | `boolean` |
| **Tipo de Input** | Toggle switch (checkbox estilizado) |
| **Validacao Controller** | `nullable\|in:0,1` |
| **Valor Padrao (DB)** | `true` |
| **Valor Padrao (Form)** | `true` (checked) |
| **Coluna DB** | `boolean('login_show_powered_by')->default(true)` |
| **Cast no Model** | `'login_show_powered_by' => 'boolean'` |
| **Usado em CSS/JS** | Via JS esconde elementos contendo "Powered by" quando `false` |
| **Tooltip** | "Exibe o texto 'Powered by Krayin' no rodape da pagina de login. Desative para remover." |
| **Arquivos Relacionados** | `ThemeHelper.php:263`, `theme-styles.blade.php:677-717` |

---

## 5. LOGIN CARD CUSTOMIZADO

### 5.1 `login_card_enabled`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `login_card_enabled` |
| **Label no Form** | "Ativar Card Customizado" |
| **Tipo de Dado** | `boolean` |
| **Tipo de Input** | Toggle switch (checkbox estilizado) |
| **Validacao Controller** | `nullable\|in:0,1` |
| **Valor Padrao (DB)** | `false` |
| **Valor Padrao (Form)** | `false` (unchecked) |
| **Coluna DB** | `boolean('login_card_enabled')->default(false)` |
| **Cast no Model** | `'login_card_enabled' => 'boolean'` |
| **Usado em CSS/JS** | Condiciona exibicao de secao de opcoes do card; habilita script de customizacao |
| **Tooltip** | "Ativa customizacao avancada do card de login com imagem de fundo, titulo e cores personalizadas." |
| **Arquivos Relacionados** | `ThemeHelper.php:264`, `index.blade.php:933`, `theme-styles.blade.php:723-940` |
| **JS Toggle Function** | `toggleLoginCardOptions()` |

---

### 5.2 `login_card_bg_image`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `login_card_bg_image` |
| **Label no Form** | "Imagem de Fundo do Card" |
| **Tipo de Dado** | `string` (filename) |
| **Tipo de Input** | `<input type="file">` + preview + checkbox delete + botao reset |
| **Validacao Controller** | `nullable\|file\|mimes:jpg,jpeg,png,webp\|max:10240` |
| **Valor Padrao (DB)** | `null` |
| **Valor Padrao (Form)** | N/A (exibe preview se existir) |
| **Coluna DB** | `string('login_card_bg_image', 500)->nullable()` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | `--theme-login-card-bg-url`, aplica `background-image` no card de login |
| **Tooltip** | "Imagem de fundo aplicada diretamente ao card de login. Combinada com overlay para melhor legibilidade." |
| **Arquivos Relacionados** | `ThemeHelper.php:183-187,265`, `theme-styles.blade.php:738,761-787` |
| **Storage Path** | `storage/app/public/theme-manager/{timestamp}_{field}_{random}.{ext}` |
| **Reset Button** | Sim - `resetField('login_card_bg_image')` |

---

### 5.3 `login_card_bg_opacity`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `login_card_bg_opacity` |
| **Label no Form** | "Opacidade do Fundo do Card" |
| **Tipo de Dado** | `integer` |
| **Tipo de Input** | `<input type="range">` (slider) |
| **Validacao Controller** | `nullable\|integer\|min:0\|max:100` |
| **Valor Padrao (DB)** | `62` |
| **Valor Padrao (Form)** | `62` |
| **Coluna DB** | `integer('login_card_bg_opacity')->default(62)` |
| **Cast no Model** | `'login_card_bg_opacity' => 'integer'` |
| **Usado em CSS/JS** | `--theme-login-card-bg-opacity` (valor / 100), controla visibilidade da imagem do card |
| **Tooltip** | "Controla a visibilidade da imagem de fundo do card. Valores menores deixam o overlay mais evidente." |
| **Arquivos Relacionados** | `ThemeHelper.php:185,266`, `ThemeConfigRepository.php:259`, `theme-styles.blade.php:739` |
| **Range** | 0% - 100% (step: 5) |

---

### 5.4 `login_card_overlay_color`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `login_card_overlay_color` |
| **Label no Form** | "Cor do Overlay" |
| **Tipo de Dado** | `string` (rgba color) |
| **Tipo de Input** | `<input type="text">` + preview div |
| **Validacao Controller** | `nullable\|string\|max:50\|regex:/^rgba?\\(\\s*\\d{1,3}\\s*,\\s*\\d{1,3}\\s*,\\s*\\d{1,3}\\s*(,\\s*(0\|1\|0?\\.\\d+))?\\s*\\)$/` |
| **Valor Padrao (DB)** | `'rgba(10, 45, 15, 0.78)'` |
| **Valor Padrao (Form)** | `'rgba(10, 45, 15, 0.78)'` |
| **Coluna DB** | `string('login_card_overlay_color', 50)->default('rgba(10, 45, 15, 0.78)')` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | `--theme-login-card-overlay`, aplica como `background` do overlay do card |
| **Tooltip** | "Cor de sobreposicao sobre a imagem. Use formato rgba() para controlar transparencia. Ex: rgba(10, 45, 15, 0.78)" |
| **Arquivos Relacionados** | `ThemeHelper.php:103-120,186,267`, `theme-styles.blade.php:740,770` |
| **Preview Function** | `updateOverlayColorPreview()` |

---

### 5.5 `login_card_title`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `login_card_title` |
| **Label no Form** | "Titulo de Boas-vindas" |
| **Tipo de Dado** | `string` |
| **Tipo de Input** | `<input type="text">` |
| **Validacao Controller** | `nullable\|string\|max:100` |
| **Valor Padrao (DB)** | `'Bem-vindo'` |
| **Valor Padrao (Form)** | `'Bem-vindo'` |
| **Coluna DB** | `string('login_card_title', 100)->default('Bem-vindo')` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | Injetado via JS como `<h2>` acima do formulario de login |
| **Tooltip** | "Titulo principal exibido acima do formulario de login." |
| **Arquivos Relacionados** | `ThemeHelper.php:125-135,268`, `theme-styles.blade.php:741,799-802` |
| **Sanitizacao** | `ThemeHelper::sanitizeText()` remove caracteres perigosos |

---

### 5.6 `login_card_subtitle`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `login_card_subtitle` |
| **Label no Form** | "Subtitulo" |
| **Tipo de Dado** | `string` |
| **Tipo de Input** | `<input type="text">` |
| **Validacao Controller** | `nullable\|string\|max:200` |
| **Valor Padrao (DB)** | `'Acesse sua conta para continuar'` |
| **Valor Padrao (Form)** | `'Acesse sua conta para continuar'` |
| **Coluna DB** | `string('login_card_subtitle', 200)->default('Acesse sua conta para continuar')` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | Injetado via JS como `<p>` abaixo do titulo |
| **Tooltip** | "Texto secundario abaixo do titulo, para contextualizar o usuario." |
| **Arquivos Relacionados** | `ThemeHelper.php:269`, `theme-styles.blade.php:742,804-807` |
| **Sanitizacao** | `ThemeHelper::sanitizeText()` remove caracteres perigosos |

---

### 5.7 `login_card_sparkles`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `login_card_sparkles` |
| **Label no Form** | "Efeito de Particulas" |
| **Tipo de Dado** | `boolean` |
| **Tipo de Input** | Toggle switch (checkbox estilizado) |
| **Validacao Controller** | `nullable\|in:0,1` |
| **Valor Padrao (DB)** | `false` |
| **Valor Padrao (Form)** | `false` (unchecked) |
| **Coluna DB** | `boolean('login_card_sparkles')->default(false)` |
| **Cast no Model** | `'login_card_sparkles' => 'boolean'` |
| **Usado em CSS/JS** | Via JS cria 15 divs com animacao `@keyframes sparkle` |
| **Tooltip** | "Adiciona particulas brilhantes animadas sobre o card. Efeito decorativo sutil." |
| **Arquivos Relacionados** | `ThemeHelper.php:270`, `theme-styles.blade.php:743,818-844` |

---

### 5.8 `login_card_help_link`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `login_card_help_link` |
| **Label no Form** | "Link de Ajuda" |
| **Tipo de Dado** | `boolean` |
| **Tipo de Input** | Toggle switch (checkbox estilizado) |
| **Validacao Controller** | `nullable\|in:0,1` |
| **Valor Padrao (DB)** | `true` |
| **Valor Padrao (Form)** | `true` (checked) |
| **Coluna DB** | `boolean('login_card_help_link')->default(true)` |
| **Cast no Model** | `'login_card_help_link' => 'boolean'` |
| **Usado em CSS/JS** | Via JS adiciona secao "Precisa de ajuda?" com mailto link |
| **Tooltip** | "Exibe link 'Precisa de ajuda?' com email de suporte configurado abaixo." |
| **Arquivos Relacionados** | `ThemeHelper.php:271`, `theme-styles.blade.php:744,847-878` |

---

### 5.9 `login_card_support_email`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `login_card_support_email` |
| **Label no Form** | "Email de Suporte" |
| **Tipo de Dado** | `string` (email) |
| **Tipo de Input** | `<input type="email">` |
| **Validacao Controller** | `nullable\|email\|max:100` |
| **Valor Padrao (DB)** | `'suporte@empresa.com.br'` |
| **Valor Padrao (Form)** | `'suporte@empresa.com.br'` |
| **Coluna DB** | `string('login_card_support_email', 100)->default('suporte@empresa.com.br')` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | Usado como `href="mailto:{email}"` no link de ajuda |
| **Tooltip** | "Email de suporte exibido quando o link de ajuda estiver ativado. Clicavel como mailto." |
| **Arquivos Relacionados** | `ThemeHelper.php:272`, `theme-styles.blade.php:745,861-863` |
| **Sanitizacao** | `filter_var($email, FILTER_VALIDATE_EMAIL)` |

---

### 5.10 `login_card_custom_code`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `login_card_custom_code` |
| **Label no Form** | N/A (nao exibido no form atual) |
| **Tipo de Dado** | `text` (HTML/CSS/JS) |
| **Tipo de Input** | `<textarea>` (se implementado) |
| **Validacao Controller** | `nullable\|string` |
| **Valor Padrao (DB)** | `null` |
| **Valor Padrao (Form)** | N/A |
| **Coluna DB** | `text('login_card_custom_code')->nullable()` |
| **Cast no Model** | Nenhum (string nativo) |
| **Usado em CSS/JS** | Injetado via JS: `<style>` no head, HTML no body, scripts executados |
| **Tooltip** | N/A |
| **Arquivos Relacionados** | `theme-styles.blade.php:881-935` |
| **Seguranca** | CUIDADO: Executa codigo arbitrario. Implementar sanitizacao se exposto ao usuario. |

---

## 6. EMPTY STATES

> Todos os campos de Empty States seguem o mesmo padrao.

### Template Geral Empty States

| Item | Valor Comum |
|------|-------------|
| **Tipo de Dado** | `string` (filename) |
| **Tipo de Input** | `<input type="file">` + preview + checkbox delete |
| **Validacao Controller** | `nullable\|file\|mimes:svg\|max:2048` |
| **Valor Padrao (DB)** | `null` |
| **Coluna DB** | `string('empty_state_{type}', 500)->nullable()` |
| **Cast no Model** | Nenhum (string nativo) |
| **Storage Path** | `storage/app/public/theme-manager/{timestamp}_{field}_{random}.svg` |
| **Metodo Helper** | `ThemeHelper::getEmptyState('{type}')` |

---

### 6.1 `empty_state_activities`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `empty_state_activities` |
| **Label no Form** | "Atividades" |
| **Usado em** | Pagina de atividades quando lista vazia |

---

### 6.2 `empty_state_calls`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `empty_state_calls` |
| **Label no Form** | "Ligacoes" |
| **Usado em** | Pagina de ligacoes quando lista vazia |

---

### 6.3 `empty_state_emails`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `empty_state_emails` |
| **Label no Form** | "E-mails" |
| **Usado em** | Pagina de e-mails quando lista vazia |

---

### 6.4 `empty_state_meetings`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `empty_state_meetings` |
| **Label no Form** | "Reunioes" |
| **Usado em** | Pagina de reunioes quando lista vazia |

---

### 6.5 `empty_state_notes`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `empty_state_notes` |
| **Label no Form** | "Notas" |
| **Usado em** | Pagina de notas quando lista vazia |

---

### 6.6 `empty_state_organizations`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `empty_state_organizations` |
| **Label no Form** | "Organizacoes" |
| **Usado em** | Pagina de organizacoes quando lista vazia |

---

### 6.7 `empty_state_persons`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `empty_state_persons` |
| **Label no Form** | "Pessoas" |
| **Usado em** | Pagina de pessoas/contatos quando lista vazia |

---

### 6.8 `empty_state_leads`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `empty_state_leads` |
| **Label no Form** | "Leads" |
| **Usado em** | Pagina de leads quando lista vazia |

---

### 6.9 `empty_state_products`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `empty_state_products` |
| **Label no Form** | "Produtos" |
| **Usado em** | Pagina de produtos quando lista vazia |

---

## 7. CAMPOS INTERNOS/SISTEMA

### 7.1 `id`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `id` |
| **Tipo de Dado** | `integer` (auto-increment) |
| **Coluna DB** | `$table->id()` |
| **Valor** | Sempre `1` (singleton) |
| **Usado em** | `ThemeConfig::getInstance()` - `firstOrCreate(['id' => 1], ...)` |

---

### 7.2 `previous_theme`

| Item | Descricao |
|------|-----------|
| **Nome do Campo** | `previous_theme` |
| **Tipo de Dado** | `string` |
| **Coluna DB** | `string('previous_theme', 50)->nullable()` |
| **Valor Padrao** | `null` |
| **Usado em** | `ThemeController::rollback()` - permite reverter para tema anterior |
| **Automatico** | Preenchido automaticamente quando `selected_theme` muda |

---

## 8. CAMPOS AUXILIARES (DELETE)

> Estes campos NAO sao salvos no banco. Sao checkboxes que disparam logica de delete no Repository.

### Padrao de Funcionamento

```php
// No ThemeConfigRepository::update()
if (isset($data["{$field}_delete"]) && $data["{$field}_delete"]) {
    $this->deleteFile($config->$field);
    $data[$field] = null;
    unset($data["{$field}_delete"]);
}
```

### Lista de Campos Delete

| Campo | Campo Associado | Arquivo Deletado |
|-------|-----------------|------------------|
| `logo_main_delete` | `logo_main` | `storage/app/public/theme-manager/{filename}` |
| `logo_light_delete` | `logo_light` | `storage/app/public/theme-manager/{filename}` |
| `logo_icon_delete` | `logo_icon` | `storage/app/public/theme-manager/{filename}` |
| `favicon_delete` | `favicon` | `storage/app/public/theme-manager/{filename}` |
| `login_bg_image_delete` | `login_bg_image` | `storage/app/public/theme-manager/{filename}` |
| `login_card_bg_image_delete` | `login_card_bg_image` | `storage/app/public/theme-manager/{filename}` |
| `empty_state_activities_delete` | `empty_state_activities` | `storage/app/public/theme-manager/{filename}` |
| `empty_state_calls_delete` | `empty_state_calls` | `storage/app/public/theme-manager/{filename}` |
| `empty_state_emails_delete` | `empty_state_emails` | `storage/app/public/theme-manager/{filename}` |
| `empty_state_meetings_delete` | `empty_state_meetings` | `storage/app/public/theme-manager/{filename}` |
| `empty_state_notes_delete` | `empty_state_notes` | `storage/app/public/theme-manager/{filename}` |
| `empty_state_organizations_delete` | `empty_state_organizations` | `storage/app/public/theme-manager/{filename}` |
| `empty_state_persons_delete` | `empty_state_persons` | `storage/app/public/theme-manager/{filename}` |
| `empty_state_leads_delete` | `empty_state_leads` | `storage/app/public/theme-manager/{filename}` |
| `empty_state_products_delete` | `empty_state_products` | `storage/app/public/theme-manager/{filename}` |

---

## Apendice A: Variaveis CSS Geradas

```css
:root {
    /* Cores Primarias */
    --primary-color: #1E40AF;
    --primary-dark-color: #1E3A8A;
    --primary-light-color: #3B82F6;
    --primary-rgb: 30, 64, 175;

    /* Status */
    --success-color: #10B981;
    --success-rgb: 16, 185, 129;
    --warning-color: #F59E0B;
    --warning-rgb: 245, 158, 11;
    --danger-color: #EF4444;
    --danger-rgb: 239, 68, 68;

    /* Login Background (se login_bg_image existir) */
    --theme-login-bg-url: url('...');
    --theme-login-bg-opacity: 0.5;
    --theme-login-bg-zoom: 1;

    /* Login Card (se login_card_bg_image existir) */
    --theme-login-card-bg-url: url('...');
    --theme-login-card-bg-opacity: 0.62;
    --theme-login-card-overlay: rgba(10, 45, 15, 0.78);
}
```

---

## Apendice B: Arquivos Relacionados

| Arquivo | Responsabilidade |
|---------|------------------|
| `ThemeConfig.php` | Model (singleton, casts, fillable) |
| `ThemeController.php` | Validacao, flash messages, eventos |
| `ThemeConfigRepository.php` | CRUD, uploads, deletes, theme.json loading |
| `ThemeHelper.php` | Cache, sanitizacao, geracao CSS, getters |
| `theme-styles.blade.php` | Aplicacao CSS/JS no frontend |
| `index.blade.php` | Formulario de configuracao |
| `login.blade.php` | Pagina de login (consome configs) |

---

## Apendice C: Eventos Dispatchados

| Evento | Quando | Payload |
|--------|--------|---------|
| `theme.update.before` | Antes de salvar | `$request->all()` |
| `theme.update.after` | Apos salvar | `$config` (ThemeConfig) |
| `theme.field.reset` | Apos reset de campo | `$fieldName` (string) |

---

## Apendice D: Rotas

| Metodo | URI | Nome | Controller Method |
|--------|-----|------|-------------------|
| GET | `/admin/settings/theme` | `admin.settings.theme.index` | `index()` |
| POST | `/admin/settings/theme` | `admin.settings.theme.update` | `update()` |
| POST | `/admin/settings/theme/restore` | `admin.settings.theme.restore` | `restore()` |
| POST | `/admin/settings/theme/rollback` | `admin.settings.theme.rollback` | `rollback()` |
| POST | `/admin/settings/theme/reset-field` | `admin.settings.theme.reset-field` | `resetField()` |

---

**Documento gerado em:** Dezembro 2025
**Versao do ThemeManager:** 1.0.0
