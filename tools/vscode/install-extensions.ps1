# Script para instalar extensões do VSCode para Krayin ThemeManager
# Execute no PowerShell: .\install-extensions.ps1

Write-Host "🚀 Instalando extensões para Krayin ThemeManager..." -ForegroundColor Green

# PHP e Laravel
Write-Host "`n📦 Instalando extensões PHP/Laravel..." -ForegroundColor Yellow
code --install-extension bmewburn.vscode-intelephense-client
code --install-extension onecentlin.laravel-blade
code --install-extension amiralizadeh9480.laravel-extra-intellisense
code --install-extension MehediDrawormo.php-namespace-resolver

# Docker e DevOps
Write-Host "`n🐳 Instalando extensões Docker..." -ForegroundColor Yellow
code --install-extension ms-azuretools.vscode-docker
code --install-extension redhat.vscode-yaml

# Produtividade
Write-Host "`n⚡ Instalando extensões de produtividade..." -ForegroundColor Yellow
code --install-extension usernamehw.errorlens
code --install-extension eamodio.gitlens
code --install-extension mikestead.dotenv
code --install-extension gruntfuggly.todo-tree
code --install-extension PKief.material-icon-theme
code --install-extension aaron-bond.better-comments

Write-Host "`n✅ Instalação concluída!" -ForegroundColor Green
Write-Host "`n📝 Próximos passos:" -ForegroundColor Cyan
Write-Host "   1. Reinicie o VSCode" -ForegroundColor White
Write-Host "   2. Abra a pasta do projeto Krayin" -ForegroundColor White
Write-Host "   3. As configurações em .vscode/ serão carregadas automaticamente" -ForegroundColor White
Write-Host "`n💡 Dica: Configure os snippets manualmente seguindo o guia no CLAUDE.md" -ForegroundColor Magenta
