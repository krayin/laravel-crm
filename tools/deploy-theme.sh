#!/bin/bash
#
# deploy-theme.sh - Script de deploy do Theme System Refactoring
#
# Uso: bash tools/deploy-theme.sh [--no-backup] [--dry-run]
#
# Opcoes:
#   --no-backup   Pula o backup do banco de dados
#   --dry-run     Mostra o que seria feito sem executar
#
# Requisitos:
#   - Docker Swarm ativo
#   - Acesso ao container do app
#   - Git configurado
#

set -e  # Exit on error

# =============================================================================
# CONFIGURACAO
# =============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Docker
STACK_NAME="${STACK_NAME:-krayin}"
SERVICE_NAME="${SERVICE_NAME:-krayin_app}"
COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.yml}"

# Backup
BACKUP_DIR="${BACKUP_DIR:-$PROJECT_DIR/backups}"
DB_CONTAINER="${DB_CONTAINER:-krayin_db}"

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Flags
DO_BACKUP=true
DRY_RUN=false

# =============================================================================
# FUNCOES
# =============================================================================

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[OK]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

log_step() {
    echo ""
    echo -e "${GREEN}==>${NC} $1"
    echo "---------------------------------------------------"
}

run_cmd() {
    if [ "$DRY_RUN" = true ]; then
        echo -e "${YELLOW}[DRY-RUN]${NC} $1"
    else
        log_info "Executando: $1"
        eval "$1"
    fi
}

check_prerequisites() {
    log_step "Verificando pre-requisitos"

    # Verifica se esta no diretorio correto
    if [ ! -f "$PROJECT_DIR/artisan" ]; then
        log_error "Arquivo artisan nao encontrado. Execute do diretorio do projeto."
        exit 1
    fi
    log_success "Diretorio do projeto OK"

    # Verifica Docker
    if ! command -v docker &> /dev/null; then
        log_error "Docker nao encontrado"
        exit 1
    fi
    log_success "Docker instalado"

    # Verifica se Docker Swarm esta ativo
    if ! docker info 2>/dev/null | grep -q "Swarm: active"; then
        log_warn "Docker Swarm nao esta ativo. Deploy pode falhar."
    else
        log_success "Docker Swarm ativo"
    fi

    # Verifica Git
    if ! command -v git &> /dev/null; then
        log_error "Git nao encontrado"
        exit 1
    fi
    log_success "Git instalado"

    # Verifica se ha alteracoes nao commitadas
    cd "$PROJECT_DIR"
    if [ -n "$(git status --porcelain)" ]; then
        log_warn "Ha alteracoes nao commitadas no repositorio"
        git status --short
        echo ""
        read -p "Continuar mesmo assim? [y/N] " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            log_info "Deploy cancelado"
            exit 0
        fi
    else
        log_success "Repositorio limpo"
    fi
}

backup_database() {
    if [ "$DO_BACKUP" = false ]; then
        log_warn "Backup pulado (--no-backup)"
        return
    fi

    log_step "Backup do banco de dados"

    # Cria diretorio de backup
    mkdir -p "$BACKUP_DIR"

    BACKUP_FILE="$BACKUP_DIR/db_backup_$TIMESTAMP.sql"

    # Tenta backup via docker
    if docker ps --format '{{.Names}}' | grep -q "$DB_CONTAINER"; then
        log_info "Fazendo backup do container $DB_CONTAINER..."
        run_cmd "docker exec $DB_CONTAINER mysqldump -u root -p\$MYSQL_ROOT_PASSWORD krayin > $BACKUP_FILE 2>/dev/null || true"

        if [ -f "$BACKUP_FILE" ] && [ -s "$BACKUP_FILE" ]; then
            log_success "Backup salvo em: $BACKUP_FILE"
            log_info "Tamanho: $(du -h "$BACKUP_FILE" | cut -f1)"
        else
            log_warn "Backup pode ter falhado ou banco vazio"
        fi
    else
        log_warn "Container $DB_CONTAINER nao encontrado. Backup pulado."
        log_info "Voce pode fazer backup manualmente antes de continuar."
        read -p "Continuar sem backup? [y/N] " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            log_info "Deploy cancelado"
            exit 0
        fi
    fi
}

git_pull() {
    log_step "Atualizando codigo (git pull)"

    cd "$PROJECT_DIR"

    CURRENT_BRANCH=$(git branch --show-current)
    log_info "Branch atual: $CURRENT_BRANCH"

    # Fetch primeiro
    run_cmd "git fetch origin"

    # Mostra o que vai mudar
    BEHIND=$(git rev-list --count HEAD..origin/$CURRENT_BRANCH 2>/dev/null || echo "0")
    if [ "$BEHIND" -gt 0 ]; then
        log_info "Commits a baixar: $BEHIND"
        git log --oneline HEAD..origin/$CURRENT_BRANCH 2>/dev/null || true
        echo ""
    else
        log_info "Repositorio ja esta atualizado"
    fi

    # Pull
    run_cmd "git pull origin $CURRENT_BRANCH"

    log_success "Codigo atualizado"
}

docker_deploy() {
    log_step "Deploy Docker Stack"

    cd "$PROJECT_DIR"

    if [ -f "$COMPOSE_FILE" ]; then
        log_info "Usando compose file: $COMPOSE_FILE"
        run_cmd "docker stack deploy -c $COMPOSE_FILE $STACK_NAME"
        log_success "Stack deployed: $STACK_NAME"

        # Aguarda um pouco para os containers subirem
        if [ "$DRY_RUN" = false ]; then
            log_info "Aguardando containers iniciarem (10s)..."
            sleep 10
        fi
    else
        log_warn "Arquivo $COMPOSE_FILE nao encontrado"
        log_info "Pulando docker stack deploy"
    fi
}

clear_caches() {
    log_step "Limpando caches"

    # Encontra o container do app
    APP_CONTAINER=$(docker ps --filter "name=${SERVICE_NAME}" --format "{{.Names}}" | head -1)

    if [ -n "$APP_CONTAINER" ]; then
        log_info "Container encontrado: $APP_CONTAINER"

        # Cache clear
        run_cmd "docker exec $APP_CONTAINER php artisan cache:clear"
        log_success "Cache limpo"

        # Config clear
        run_cmd "docker exec $APP_CONTAINER php artisan config:clear"
        log_success "Config cache limpo"

        # View clear
        run_cmd "docker exec $APP_CONTAINER php artisan view:clear"
        log_success "View cache limpo"

        # Route clear (opcional)
        run_cmd "docker exec $APP_CONTAINER php artisan route:clear"
        log_success "Route cache limpo"

    else
        log_warn "Container do app nao encontrado"
        log_info "Tentando executar localmente..."

        cd "$PROJECT_DIR"
        run_cmd "php artisan cache:clear"
        run_cmd "php artisan config:clear"
        run_cmd "php artisan view:clear"
        run_cmd "php artisan route:clear"
    fi
}

run_migrations() {
    log_step "Executando migrations"

    APP_CONTAINER=$(docker ps --filter "name=${SERVICE_NAME}" --format "{{.Names}}" | head -1)

    if [ -n "$APP_CONTAINER" ]; then
        run_cmd "docker exec $APP_CONTAINER php artisan migrate --force"
    else
        cd "$PROJECT_DIR"
        run_cmd "php artisan migrate --force"
    fi

    log_success "Migrations executadas"
}

print_checklist() {
    log_step "CHECKLIST POS-DEPLOY"

    echo ""
    echo -e "${GREEN}Deploy concluido!${NC} Siga o checklist abaixo:"
    echo ""
    echo "┌─────────────────────────────────────────────────────────────┐"
    echo "│                    VALIDACAO MANUAL                         │"
    echo "├─────────────────────────────────────────────────────────────┤"
    echo "│ [ ] 1. Acessar /admin/login em aba anonima                  │"
    echo "│     → Pagina deve carregar sem erro 500                     │"
    echo "│     → CSS deve estar aplicado (nao quebrado)                │"
    echo "│                                                             │"
    echo "│ [ ] 2. Fazer login como admin                               │"
    echo "│     → Dashboard deve carregar normalmente                   │"
    echo "│                                                             │"
    echo "│ [ ] 3. Acessar Settings > Theme                             │"
    echo "│     → Pagina deve carregar                                  │"
    echo "│     → Temas disponiveis devem aparecer                      │"
    echo "│                                                             │"
    echo "│ [ ] 4. Testar Preview de tema                               │"
    echo "│     → Clicar em Preview de um tema                          │"
    echo "│     → URL deve ter ?theme_preview={slug}                    │"
    echo "│     → Em outra aba, tema original deve aparecer             │"
    echo "│                                                             │"
    echo "│ [ ] 5. Testar Aplicar tema                                  │"
    echo "│     → Selecionar e aplicar um tema                          │"
    echo "│     → Verificar em /admin/login (aba anonima)               │"
    echo "│                                                             │"
    echo "│ [ ] 6. Testar Rollback                                      │"
    echo "│     → Clicar em 'Restaurar Padrao'                          │"
    echo "│     → Tema deve voltar ao default                           │"
    echo "│                                                             │"
    echo "│ [ ] 7. Verificar logs                                       │"
    echo "│     → grep -i '[Theme]' storage/logs/laravel.log            │"
    echo "│     → Nao deve ter erros criticos                           │"
    echo "└─────────────────────────────────────────────────────────────┘"
    echo ""
    echo -e "${BLUE}Comandos uteis:${NC}"
    echo ""
    echo "  # Ver logs do tema"
    echo "  grep -i '\[Theme\]' storage/logs/laravel.log | tail -20"
    echo ""
    echo "  # Ver tema atual no banco"
    echo "  docker exec $DB_CONTAINER mysql -u root -p\$MYSQL_ROOT_PASSWORD -e \"SELECT selected_theme, is_active FROM krayin.theme_configs WHERE id=1;\""
    echo ""
    echo "  # Rodar testes"
    echo "  docker exec \$(docker ps --filter 'name=${SERVICE_NAME}' -q | head -1) php vendor/bin/pest --filter=ThemeContextFactoryTest"
    echo ""
    echo -e "${BLUE}Documentacao:${NC}"
    echo "  - Runbook completo: docs/RUNBOOK_THEME_SMOKE.md"
    echo "  - Changelog tecnico: docs/CHANGELOG_THEME_REFACTORING.md"
    echo ""
    echo -e "${GREEN}Tag do release:${NC} theme-refactor-v1"
    echo -e "${GREEN}Data do deploy:${NC} $(date '+%Y-%m-%d %H:%M:%S')"
    echo ""
}

show_help() {
    echo "Uso: $0 [opcoes]"
    echo ""
    echo "Opcoes:"
    echo "  --no-backup   Pula o backup do banco de dados"
    echo "  --dry-run     Mostra o que seria feito sem executar"
    echo "  --help        Mostra esta ajuda"
    echo ""
    echo "Variaveis de ambiente:"
    echo "  STACK_NAME    Nome do stack Docker (default: krayin)"
    echo "  SERVICE_NAME  Nome do servico do app (default: krayin_app)"
    echo "  COMPOSE_FILE  Arquivo docker-compose (default: docker-compose.yml)"
    echo "  BACKUP_DIR    Diretorio para backups (default: ./backups)"
    echo "  DB_CONTAINER  Nome do container do banco (default: krayin_db)"
    echo ""
}

# =============================================================================
# MAIN
# =============================================================================

# Parse argumentos
while [[ $# -gt 0 ]]; do
    case $1 in
        --no-backup)
            DO_BACKUP=false
            shift
            ;;
        --dry-run)
            DRY_RUN=true
            shift
            ;;
        --help|-h)
            show_help
            exit 0
            ;;
        *)
            log_error "Opcao desconhecida: $1"
            show_help
            exit 1
            ;;
    esac
done

# Header
echo ""
echo "======================================================="
echo "     DEPLOY - Theme System Refactoring"
echo "======================================================="
echo ""
echo "Timestamp: $TIMESTAMP"
echo "Projeto:   $PROJECT_DIR"
if [ "$DRY_RUN" = true ]; then
    echo -e "Modo:      ${YELLOW}DRY-RUN (simulacao)${NC}"
fi
echo ""

# Confirmacao
if [ "$DRY_RUN" = false ]; then
    read -p "Iniciar deploy? [y/N] " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        log_info "Deploy cancelado"
        exit 0
    fi
fi

# Executa steps
check_prerequisites
backup_database
git_pull
docker_deploy
run_migrations
clear_caches
print_checklist

log_success "Deploy finalizado com sucesso!"
