#!/bin/bash
# Script para instalar extensões do VSCode para Krayin ThemeManager
# Execute: chmod +x install-extensions.sh && ./install-extensions.sh

echo "🚀 Instalando extensões para Krayin ThemeManager..."
echo ""

# PHP e Laravel
echo "📦 Instalando extensões PHP/Laravel..."
code --install-extension bmewburn.vscode-intelephense-client
code --install-extension onecentlin.laravel-blade
code --install-extension amiralizadeh9480.laravel-extra-intellisense
code --install-extension MehediDrawormo.php-namespace-resolver

# Docker e DevOps
echo ""
echo "🐳 Instalando extensões Docker..."
code --install-extension ms-azuretools.vscode-docker
code --install-extension redhat.vscode-yaml

# Produtividade
echo ""
echo "⚡ Instalando extensões de produtividade..."
code --install-extension usernamehw.errorlens
code --install-extension eamodio.gitlens
code --install-extension mikestead.dotenv
code --install-extension gruntfuggly.todo-tree
code --install-extension PKief.material-icon-theme
code --install-extension aaron-bond.better-comments

echo ""
echo "✅ Instalação concluída!"
echo ""
echo "📝 Próximos passos:"
echo "   1. Reinicie o VSCode"
echo "   2. Abra a pasta do projeto Krayin"
echo "   3. As configurações em .vscode/ serão carregadas automaticamente"
echo ""
echo "💡 Dica: Configure os snippets manualmente seguindo o guia no CLAUDE.md"
