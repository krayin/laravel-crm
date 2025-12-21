# ThemeManager para Krayin CRM

**Versão:** 1.0.0
**Autor:** Webkul
**Licença:** MIT

## Descrição

ThemeManager é um package completo de personalização visual para Krayin CRM. Permite customizar cores, logos, página de login e elementos visuais do sistema sem necessidade de editar código.

### Funcionalidades

- ✅ **Sistema de Ativação**: Ative/desative o tema personalizado a qualquer momento
- 🎨 **Customização de Cores**: Defina cores primárias, sucesso, aviso e perigo
- 🖼️ **Upload de Logos**: Logo principal, logo claro, ícone e favicon
- 🔐 **Login Personalizado**: Background, zoom, overlay e card customizável
- ✨ **Efeitos Especiais**: Sparkles animados no login
- 📦 **Empty States**: SVGs customizados para estados vazios
- 🌐 **Multi-idioma**: Suporte a PT-BR e EN
- ⚡ **Performance**: Sistema de cache integrado
- 💉 **Injeção Dinâmica**: CSS aplicado automaticamente via middleware

## Requisitos

- PHP 8.2 ou superior
- Laravel 10+
- Krayin CRM instalado
- MySQL 8.0+
- Extensões PHP: gd, mbstring, openssl, curl, fileinfo

## Instalação

### 1. O package já está no diretório correto

```
packages/Webkul/ThemeManager/
```

### 2. Registrar autoload no composer.json raiz

Já adicionado em:
```json
"autoload": {
    "psr-4": {
        "Webkul\\ThemeManager\\": "packages/Webkul/ThemeManager/src"
    }
}
```

### 3. Registrar módulo no config/concord.php

Já adicionado no final do array `modules`:
```php
\Webkul\ThemeManager\Providers\ModuleServiceProvider::class,
```

### 4. Executar comandos de instalação

```bash
# Atualizar autoload
composer dump-autoload

# Executar migrations
php artisan migrate

# Criar link simbólico para storage
php artisan storage:link

# Limpar caches
php artisan optimize:clear
```

### 5. Acessar as configurações

Navegue para: **Configurações → Tema**

## Configurações Disponíveis

### Ativação

| Campo | Tipo | Padrão | Descrição |
|-------|------|--------|-----------|
| `is_active` | Boolean | `false` | Ativa/desativa o tema personalizado |

### Cores do Tema

| Campo | Tipo | Padrão | Descrição |
|-------|------|--------|-----------|
| `color_primary` | String | `#1E40AF` | Cor primária da marca |
| `color_primary_dark` | String | `#1E3A8A` | Variação escura da cor primária |
| `color_primary_light` | String | `#3B82F6` | Variação clara da cor primária |
| `color_success` | String | `#10B981` | Cor para mensagens de sucesso |
| `color_warning` | String | `#F59E0B` | Cor para avisos |
| `color_danger` | String | `#EF4444` | Cor para erros e ações destrutivas |

### Logos e Favicon

| Campo | Tipo | Tamanho Máx | Descrição |
|-------|------|-------------|-----------|
| `logo_main` | Upload | 5MB | Logo principal (sidebar) - SVG/PNG transparente, altura 40-50px |
| `logo_light` | Upload | 5MB | Logo para fundos escuros - SVG/PNG transparente |
| `logo_icon` | Upload | 5MB | Ícone para sidebar recolhida - Quadrado 32x32 ou 64x64px |
| `favicon` | Upload | 5MB | Favicon do site - ICO ou PNG 32x32px |

### Página de Login - Background

| Campo | Tipo | Padrão | Descrição |
|-------|------|--------|-----------|
| `login_bg_image` | Upload | - | Imagem de fundo (JPG/PNG, mín. 1920x1080px) |
| `login_bg_zoom` | Integer | `100` | Zoom da imagem (50-200%) |
| `login_bg_opacity` | Integer | `50` | Opacidade do overlay escuro (0-100%) |
| `login_show_powered_by` | Boolean | `true` | Exibir "Powered by Krayin" |

### Página de Login - Card Customizado

| Campo | Tipo | Padrão | Descrição |
|-------|------|--------|-----------|
| `login_card_enabled` | Boolean | `false` | Habilitar card customizado |
| `login_card_bg_image` | Upload | - | Imagem de fundo do card |
| `login_card_bg_opacity` | Integer | `62` | Opacidade da imagem do card (0-100%) |
| `login_card_overlay_color` | String | `rgba(10,45,15,0.78)` | Cor do overlay (formato rgba) |
| `login_card_title` | String | `Bem-vindo` | Título de boas-vindas |
| `login_card_subtitle` | String | `Acesse sua conta...` | Subtítulo |
| `login_card_sparkles` | Boolean | `false` | Efeito de brilhos animados |
| `login_card_help_link` | Boolean | `true` | Mostrar link "Precisa de ajuda?" |
| `login_card_support_email` | String | `suporte@empresa.com.br` | Email de suporte |

### Empty States (SVGs)

| Campo | Descrição |
|-------|-----------|
| `empty_state_activities` | SVG para estado vazio de Atividades |
| `empty_state_calls` | SVG para estado vazio de Chamadas |
| `empty_state_emails` | SVG para estado vazio de E-mails |
| `empty_state_meetings` | SVG para estado vazio de Reuniões |
| `empty_state_notes` | SVG para estado vazio de Notas |
| `empty_state_organizations` | SVG para estado vazio de Organizações |
| `empty_state_persons` | SVG para estado vazio de Pessoas |
| `empty_state_leads` | SVG para estado vazio de Leads |
| `empty_state_products` | SVG para estado vazio de Produtos |

**Recomendação:** SVGs otimizados entre 200x200px e 400x400px

## Como Usar

### 1. Ativando o Tema

1. Acesse **Configurações → Tema**
2. Na seção "Ativar Tema Personalizado", selecione **Sim**
3. Clique em **Salvar Configurações**

### 2. Configurando Cores

1. Na seção "Cores do Tema", escolha suas cores usando os color pickers
2. As cores são aplicadas automaticamente em:
   - Botões primários
   - Links
   - Navegação ativa
   - Badges e pills
   - Alerts e mensagens
   - Tabelas e paginação
   - E muito mais...

### 3. Upload de Logos

1. Na seção "Logos e Favicon":
   - Faça upload dos seus logos (SVG ou PNG transparente recomendado)
   - Para deletar uma imagem atual, marque o checkbox "Deletar imagem atual"
2. Os logos são aplicados automaticamente em:
   - Sidebar (logo principal)
   - Login (logo claro se disponível)
   - Favicon na aba do navegador

### 4. Customizando a Página de Login

**Background:**
1. Faça upload de uma imagem de fundo (mínimo 1920x1080px)
2. Ajuste o zoom (100% = tamanho original)
3. Ajuste a opacidade do overlay escuro (0% = sem overlay, 100% = preto total)

**Card Customizado:**
1. Habilite a opção "Habilitar Caixa Customizada"
2. Configure:
   - Imagem de fundo do card
   - Opacidade da imagem
   - Cor do overlay (formato rgba, ex: `rgba(10, 45, 15, 0.78)`)
   - Título e subtítulo personalizados
   - Habilite sparkles para efeito de brilho animado
   - Configure email de suporte para o link de ajuda

### 5. Empty States

1. Na seção "Estados Vazios", faça upload de SVGs customizados
2. Esses SVGs são exibidos quando não há dados em cada módulo
3. Formatos aceitos: apenas SVG

## Estrutura de Arquivos

```
packages/Webkul/ThemeManager/
├── composer.json                          # Definição do package
├── module.json                            # Configuração do módulo Concord
├── .gitignore                             # Arquivos ignorados pelo Git
├── README.md                              # Esta documentação
├── CHANGELOG.md                           # Histórico de versões
├── INSTALL.md                             # Guia de instalação
│
├── Database/
│   └── Migrations/
│       └── 2024_12_20_000001_create_theme_configs_table.php
│
├── src/
│   ├── Contracts/
│   │   └── ThemeConfig.php                # Interface do modelo
│   │
│   ├── Models/
│   │   ├── ThemeConfig.php                # Modelo principal
│   │   └── ThemeConfigProxy.php           # Proxy para Concord
│   │
│   ├── Repositories/
│   │   └── ThemeConfigRepository.php      # Lógica de negócio e uploads
│   │
│   ├── Helpers/
│   │   └── ThemeHelper.php                # Helper com cache
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── ThemeController.php        # Controller de configurações
│   │   └── Middleware/
│   │       └── ThemeMiddleware.php        # Injeção de CSS dinâmico
│   │
│   ├── Providers/
│   │   ├── ModuleServiceProvider.php      # Provider do Concord
│   │   └── ThemeManagerServiceProvider.php # Provider principal
│   │
│   ├── Config/
│   │   ├── menu.php                       # Item de menu
│   │   └── system.php                     # Configurações de sistema
│   │
│   └── Routes/
│       └── web.php                        # Rotas HTTP
│
└── Resources/
    ├── views/
    │   ├── admin/
    │   │   ├── sessions/
    │   │   │   └── login.blade.php        # Login customizado
    │   │   └── settings/
    │   │       └── theme/
    │   │           └── index.blade.php    # Página de configurações
    │   └── components/
    │       └── theme-styles.blade.php     # CSS dinâmico
    │
    └── lang/
        ├── pt_BR/
        │   └── app.php                    # Traduções em Português
        └── en/
            └── app.php                    # Traduções em Inglês
```

## API de Programação

### Helper `app('theme')`

```php
// Verificar se tema está ativo
if (app('theme')->isActive()) {
    // Tema ativo
}

// Obter configuração completa (com cache)
$config = app('theme')->getConfig();

// Obter logo específico
$mainLogo = app('theme')->getLogo('main');      // Logo principal
$lightLogo = app('theme')->getLogo('light');    // Logo claro
$iconLogo = app('theme')->getLogo('icon');      // Ícone
$favicon = app('theme')->getLogo('favicon');    // Favicon

// Obter configurações de login
$loginConfig = app('theme')->getLoginConfig();
// Retorna array com: bg_image, bg_zoom, bg_opacity, show_powered_by,
// card_enabled, card_bg_image, card_bg_opacity, card_overlay_color,
// card_title, card_subtitle, card_sparkles, card_help_link, card_support_email

// Obter empty state SVG
$activitiesSvg = app('theme')->getEmptyState('activities');

// Obter valor específico
$primaryColor = app('theme')->get('color_primary', '#1E40AF');

// Limpar cache
app('theme')->clearCache();

// Gerar CSS variables
$cssVariables = app('theme')->getCssVariables();
```

### Repository

```php
use Webkul\ThemeManager\Repositories\ThemeConfigRepository;

$repository = app(ThemeConfigRepository::class);

// Obter configuração (singleton)
$config = $repository->get();

// Atualizar (com upload de arquivos)
$data = $request->all();
$config = $repository->update($data);
// Automaticamente: processa uploads, deleta arquivos antigos, limpa cache
```

## Cache

O ThemeManager utiliza cache do Laravel para melhor performance:

- **Chave:** `theme_config`
- **TTL:** 3600 segundos (1 hora)
- **Limpeza:** Automática ao atualizar configurações

Para limpar manualmente:
```bash
php artisan cache:clear
# ou
app('theme')->clearCache();
```

## Troubleshooting

### CSS não está aplicando

1. Verifique se o tema está ativo: `app('theme')->isActive()`
2. Limpe o cache: `php artisan optimize:clear`
3. Verifique se o middleware está registrado
4. Inspecione o HTML para ver se o `<style>` foi injetado antes do `</head>`

### Upload de imagens falha

1. Verifique permissões: `chmod -R 775 storage/`
2. Verifique se o link simbólico existe: `php artisan storage:link`
3. Verifique tamanho máximo no php.ini: `upload_max_filesize` e `post_max_size`

### Login customizado não aparece

1. Limpe o cache de views: `php artisan view:clear`
2. Verifique se o tema está ativo
3. Verifique logs: `tail -f storage/logs/laravel.log`

### Menu "Tema" não aparece

1. Execute: `composer dump-autoload`
2. Execute: `php artisan optimize:clear`
3. Verifique se o package está em `config/concord.php`

## Screenshots

> **Nota:** Adicione screenshots aqui após implementação

- [ ] Página de configurações do tema
- [ ] Login padrão vs Login customizado
- [ ] Exemplo de cores aplicadas
- [ ] Menu de configurações

## Changelog

Veja [CHANGELOG.md](CHANGELOG.md) para histórico de versões.

## Roadmap (Futuro)

- [ ] Marketplace de temas pré-configurados
- [ ] Exportar/importar configurações de tema
- [ ] Preview ao vivo das alterações
- [ ] Mais pontos de customização (sidebar, dashboard, etc)
- [ ] Dark mode customizável
- [ ] Fontes customizadas

## Suporte

Para reportar bugs ou solicitar funcionalidades:
- Crie uma issue no repositório
- Entre em contato com a equipe Webkul

## Licença

MIT License - Veja arquivo LICENSE para detalhes.

---

**Desenvolvido por Webkul para Krayin CRM**
