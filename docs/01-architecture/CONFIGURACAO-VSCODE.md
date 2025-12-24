# 🎨 Guia de Configuração: Krayin ThemeManager

**Objetivo:** Configurar o ambiente completo para desenvolver o ThemeManager
**Tempo estimado:** 30 minutos
**Resultado:** VSCode + Claude Code prontos para criar o sistema de temas

---

## 📋 ÍNDICE

| Parte | Conteúdo | Tempo |
|-------|----------|-------|
| 1 | O Que Vamos Fazer | 2 min |
| 2 | Configurar o VSCode | 15 min |
| 3 | Configurar o Agente (CLAUDE.md) | 5 min |
| 4 | Testar o Ambiente | 5 min |
| 5 | Referência Rápida | - |

---

## PARTE 1: O QUE VAMOS FAZER

### 🎯 Visão Geral

Você vai configurar duas coisas:

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│   1. VSCODE                    2. CLAUDE CODE                   │
│   ════════════                 ═══════════════                  │
│                                                                 │
│   Extensões PHP/Laravel        Arquivo CLAUDE.md                │
│   Settings otimizados          na raiz do projeto               │
│   Snippets de código                                            │
│   Atalhos de teclado           Define quem o agente é           │
│                                e como deve se comportar         │
│                                                                 │
│   ↓                            ↓                                │
│   IDE profissional             Assistente especialista          │
│   para PHP/Krayin              em Krayin ThemeManager           │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 📁 Estrutura Final

Após configurar, seu projeto ficará assim:

```
seu-projeto-krayin/
│
├── CLAUDE.md                    ← Instruções para o agente
├── CONFIGURACAO-VSCODE.md       ← Este guia
│
├── .vscode/
│   ├── settings.json            ← Configs do workspace
│   └── tasks.json               ← Comandos rápidos
│
├── install-extensions.ps1       ← Script Windows
├── install-extensions.sh        ← Script Linux/Mac
│
├── packages/
│   └── Webkul/
│       └── ThemeManager/        ← Package que vamos criar
│
└── ... (resto do Krayin)
```

---

## PARTE 2: CONFIGURAR O VSCODE

### Passo 2.1: Instalar Extensões

#### Opção A: Script Automático (Recomendado)

**Windows (PowerShell):**
```powershell
# Execute no PowerShell (no diretório do projeto)
.\install-extensions.ps1
```

**Linux/Mac:**
```bash
# Execute no terminal (no diretório do projeto)
chmod +x install-extensions.sh
./install-extensions.sh
```

#### Opção B: Instalar Manualmente

Abra o VSCode e instale estas extensões (`Ctrl+Shift+X`):

| Extensão | Para quê serve |
|----------|----------------|
| **PHP Intelephense** | Autocomplete PHP inteligente |
| **Laravel Blade Snippets** | Syntax e snippets Blade |
| **Laravel Extra Intellisense** | Autocomplete Laravel |
| **PHP Namespace Resolver** | Import automático de classes |
| **Docker** | Gerenciar containers |
| **YAML** | Syntax docker-compose |
| **Error Lens** | Ver erros inline no código |
| **GitLens** | Info de Git no código |
| **DotENV** | Syntax para .env |
| **Todo Tree** | Encontrar TODOs no projeto |
| **Material Icon Theme** | Ícones bonitos |
| **Better Comments** | Comentários coloridos |

---

### Passo 2.2: Configurar Settings Globais do VSCode

1. Abra o VSCode
2. Pressione `Ctrl+Shift+P`
3. Digite: **Preferences: Open User Settings (JSON)**
4. Adicione/mescle estas configurações:

```json
{
  "editor.fontSize": 14,
  "editor.fontFamily": "'Fira Code', 'JetBrains Mono', Consolas, monospace",
  "editor.tabSize": 4,
  "editor.formatOnSave": true,
  "editor.minimap.enabled": false,
  "editor.bracketPairColorization.enabled": true,
  "editor.stickyScroll.enabled": true,
  "editor.wordWrap": "on",

  "files.autoSave": "onFocusChange",
  "files.trimTrailingWhitespace": true,
  "files.associations": {
    "*.blade.php": "blade"
  },

  "php.suggest.basic": false,
  "intelephense.environment.phpVersion": "8.2.0",

  "emmet.includeLanguages": {
    "blade": "html"
  },

  "workbench.iconTheme": "material-icon-theme",
  "workbench.colorTheme": "Default Dark+",
  "workbench.editor.enablePreview": false,

  "explorer.compactFolders": false,
  "explorer.confirmDelete": false,

  "terminal.integrated.fontSize": 13,
  "terminal.integrated.scrollback": 10000,

  "git.autofetch": true,
  "git.enableSmartCommit": true,

  "errorLens.enabledDiagnosticLevels": ["error", "warning"],

  "todo-tree.general.tags": ["TODO", "FIXME", "BUG", "KRAYIN", "THEME"],

  "[php]": {
    "editor.defaultFormatter": "bmewburn.vscode-intelephense-client"
  },
  "[blade]": {
    "editor.defaultFormatter": "onecentlin.laravel-blade"
  }
}
```

5. Salve o arquivo (`Ctrl+S`)

---

### Passo 2.3: Configurar Atalhos de Teclado

1. Pressione `Ctrl+Shift+P`
2. Digite: **Preferences: Open Keyboard Shortcuts (JSON)**
3. Adicione o conteúdo abaixo:

```json
[
  {
    "key": "ctrl+`",
    "command": "workbench.action.terminal.toggleTerminal"
  },
  {
    "key": "ctrl+shift+`",
    "command": "workbench.action.terminal.new"
  },
  {
    "key": "alt+up",
    "command": "editor.action.moveLinesUpAction"
  },
  {
    "key": "alt+down",
    "command": "editor.action.moveLinesDownAction"
  },
  {
    "key": "ctrl+shift+d",
    "command": "editor.action.copyLinesDownAction"
  },
  {
    "key": "ctrl+shift+i",
    "command": "namespaceResolver.import",
    "when": "editorLangId == 'php'"
  }
]
```

**Atalhos que você ganhou:**

| Atalho | O que faz |
|--------|-----------|
| `Ctrl+\`` | Abre/fecha terminal |
| `Ctrl+Shift+\`` | Novo terminal |
| `Alt+↑` / `Alt+↓` | Move linha para cima/baixo |
| `Ctrl+Shift+D` | Duplica linha |
| `Ctrl+Shift+I` | Importa classe PHP |

---

### Passo 2.4: Adicionar Snippets PHP

1. Pressione `Ctrl+Shift+P`
2. Digite: **Snippets: Configure User Snippets**
3. Selecione: **php.json**
4. Adicione dentro das chaves `{}`:

```json
{
  "Krayin ServiceProvider": {
    "prefix": "ksp",
    "body": [
      "<?php",
      "",
      "namespace Webkul\\\\ThemeManager\\\\Providers;",
      "",
      "use Illuminate\\\\Support\\\\ServiceProvider;",
      "",
      "class ThemeManagerServiceProvider extends ServiceProvider",
      "{",
      "    public function register()",
      "    {",
      "        $0",
      "    }",
      "",
      "    public function boot()",
      "    {",
      "        \\$this->loadMigrationsFrom(__DIR__ . '/../../Database/Migrations');",
      "        \\$this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');",
      "        \\$this->loadViewsFrom(__DIR__ . '/../../Resources/views', 'theme-manager');",
      "    }",
      "}"
    ],
    "description": "Krayin Service Provider"
  },

  "Krayin Model": {
    "prefix": "kmodel",
    "body": [
      "<?php",
      "",
      "namespace Webkul\\\\ThemeManager\\\\Models;",
      "",
      "use Illuminate\\\\Database\\\\Eloquent\\\\Model;",
      "",
      "class ${1:ThemeConfig} extends Model",
      "{",
      "    protected \\$table = '${2:theme_configs}';",
      "",
      "    protected \\$fillable = [",
      "        $0",
      "    ];",
      "",
      "    public static function getInstance(): self",
      "    {",
      "        return static::firstOrCreate(['id' => 1]);",
      "    }",
      "}"
    ],
    "description": "Krayin Model"
  },

  "Krayin Controller": {
    "prefix": "kcontroller",
    "body": [
      "<?php",
      "",
      "namespace Webkul\\\\ThemeManager\\\\Http\\\\Controllers;",
      "",
      "use Illuminate\\\\Http\\\\Request;",
      "use Illuminate\\\\Routing\\\\Controller;",
      "",
      "class ${1:ThemeController} extends Controller",
      "{",
      "    public function index()",
      "    {",
      "        return view('theme-manager::admin.settings.theme.index');",
      "    }",
      "",
      "    public function update(Request \\$request)",
      "    {",
      "        $0",
      "        return redirect()->back()->with('success', 'Salvo!');",
      "    }",
      "}"
    ],
    "description": "Krayin Controller"
  }
}
```

**Snippets que você ganhou:**

| Digite | Tab | Gera |
|--------|-----|------|
| `ksp` | ⇥ | ServiceProvider completo |
| `kmodel` | ⇥ | Model com singleton |
| `kcontroller` | ⇥ | Controller básico |

---

### Passo 2.5: Verificar Configurações do Workspace

Os arquivos já foram criados em `.vscode/`:

✅ `.vscode/settings.json` - Configurações específicas do projeto
✅ `.vscode/tasks.json` - Tarefas rápidas

**Para usar os Tasks:**
1. Pressione `Ctrl+Shift+P`
2. Digite: **Tasks: Run Task**
3. Escolha o comando desejado

**Tasks disponíveis:**
- 🧹 Limpar Cache
- 🗄️ Migrate
- 📦 Publicar Assets
- 🔄 Dump Autoload
- 🐳 Docker Up
- 📋 Docker Logs
- 🧪 Tinker
- 📍 Ver Rotas Theme

---

## PARTE 3: O ARQUIVO CLAUDE.md

O arquivo `CLAUDE.md` já está configurado na raiz do projeto! Ele define:

✅ Identidade do agente (Especialista Krayin)
✅ Regras de trabalho
✅ Estrutura do ThemeManager
✅ Campos do banco de dados
✅ Padrões de código
✅ Comandos úteis

**Nada mais precisa ser feito!** O Claude Code vai ler este arquivo automaticamente.

---

## PARTE 4: TESTAR O AMBIENTE

### Teste 1: Verificar Extensões

1. Abra o VSCode
2. Vá em Extensions (`Ctrl+Shift+X`)
3. Verifique se estas estão instaladas:
   - ✅ PHP Intelephense
   - ✅ Laravel Blade Snippets
   - ✅ Laravel Extra Intellisense

### Teste 2: Testar Snippets

1. Crie um arquivo de teste: `test.php`
2. Digite `ksp` e pressione `Tab`
3. Deve gerar o ServiceProvider completo
4. Delete o arquivo de teste

### Teste 3: Testar Tasks

1. Pressione `Ctrl+Shift+P`
2. Digite: `Tasks: Run Task`
3. Escolha: `🧹 Limpar Cache`
4. Deve executar `php artisan optimize:clear`

### Teste 4: Testar com Claude Code

Pergunte ao Claude:

```
"Explique a estrutura do ThemeManager que vamos criar"
```

O Claude deve responder baseado no CLAUDE.md mostrando que entendeu o contexto.

---

## PARTE 5: REFERÊNCIA RÁPIDA

### 📁 Arquivos Criados

```
✅ .vscode/settings.json          - Configurações do workspace
✅ .vscode/tasks.json             - Tarefas rápidas
✅ install-extensions.ps1         - Script Windows
✅ install-extensions.sh          - Script Linux/Mac
✅ CLAUDE.md                      - Instruções para o agente
✅ CONFIGURACAO-VSCODE.md         - Este guia
```

### 🔧 Comandos Essenciais

```bash
# Limpar cache após alterações
composer dump-autoload && php artisan optimize:clear

# Rodar migrations
php artisan migrate

# Testar helper
php artisan tinker
>>> app('theme')->isActive();

# Ver logs
tail -f storage/logs/laravel.log

# Ver rotas do theme
php artisan route:list | grep theme
```

### ⌨️ Atalhos Úteis

| Atalho | Ação |
|--------|------|
| `Ctrl+\`` | Abrir/fechar terminal |
| `Ctrl+Shift+P` | Command Palette |
| `Ctrl+P` | Buscar arquivo |
| `Ctrl+Shift+F` | Buscar em todo projeto |
| `Alt+↑/↓` | Mover linha |
| `Ctrl+Shift+D` | Duplicar linha |
| `Ctrl+Shift+I` | Importar classe PHP |

### 📦 Próximos Passos

Agora que o ambiente está configurado, você pode:

1. **Criar a estrutura do ThemeManager**
   ```bash
   # Pergunte ao Claude:
   "Crie a estrutura completa do package ThemeManager"
   ```

2. **Implementar funcionalidades**
   - Sistema de cores
   - Upload de logos
   - Customização de login
   - Empty states

3. **Testar e validar**
   - Usar os Tasks do VSCode
   - Verificar logs
   - Testar no navegador

---

## 🆘 PROBLEMAS COMUNS

| Problema | Solução |
|----------|---------|
| Extensões não instalam | Execute o script como administrador |
| Snippets não funcionam | Reinicie o VSCode |
| Tasks não aparecem | Verifique se está na pasta do projeto |
| CLAUDE.md não funciona | Certifique-se que está na raiz |
| Syntax Blade quebrado | Instale "Laravel Blade Snippets" |

---

## ✅ CHECKLIST FINAL

```
VSCODE
☑ Extensões instaladas
☑ Settings.json configurado
☑ Keybindings configurados
☑ Snippets PHP adicionados
☑ Tasks.json criado

PROJETO
☑ .vscode/ criado
☑ CLAUDE.md na raiz
☑ Scripts de instalação criados
☑ Ambiente testado

PRÓXIMO PASSO
☐ Criar package ThemeManager
```

---

**🎉 Configuração Concluída!**

Seu ambiente está pronto para desenvolver o ThemeManager. Bom código! 🚀

---

*Guia criado para: Krayin ThemeManager v1.0.0 | Dezembro 2024*
