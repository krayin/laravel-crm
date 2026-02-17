#!/bin/bash
#
# check-upgrade-safe.sh
# Verifica se o projeto permanece upgrade-safe (sem alteracoes em vendor/packages).
#
# Este script falha (exit 1) se houver mudancas staged ou unstaged em:
#   - vendor/
#   - packages/Webkul/
#
# Uso:
#   ./scripts/check-upgrade-safe.sh
#   ./scripts/check-upgrade-safe.sh --fix    # Reverte automaticamente
#   ./scripts/check-upgrade-safe.sh --verbose
#

set -e

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
GRAY='\033[0;90m'
NC='\033[0m' # No Color

# Flags
FIX=false
VERBOSE=false

# Parse args
for arg in "$@"; do
    case $arg in
        --fix)
            FIX=true
            ;;
        --verbose|-v)
            VERBOSE=true
            ;;
    esac
done

echo ""
echo -e "${CYAN}============================================${NC}"
echo -e "${CYAN}  Upgrade-Safe Check - Krayin CRM          ${NC}"
echo -e "${CYAN}============================================${NC}"
echo ""

# Diretorios proibidos
FORBIDDEN_PATHS=(
    "vendor/"
    "packages/Webkul/"
)

# Verificar se estamos em um repositorio git
if ! git rev-parse --is-inside-work-tree > /dev/null 2>&1; then
    echo -e "${RED}[ERROR] Nao e um repositorio git!${NC}"
    exit 1
fi

echo -e "${YELLOW}[INFO] Verificando alteracoes em diretorios protegidos...${NC}"
echo ""

# Coletar arquivos modificados (staged + unstaged)
MODIFIED_FILES=$(git diff --name-only 2>/dev/null; git diff --cached --name-only 2>/dev/null)
MODIFIED_FILES=$(echo "$MODIFIED_FILES" | sort -u | grep -v '^$' || true)

if [ "$VERBOSE" = true ]; then
    COUNT=$(echo "$MODIFIED_FILES" | grep -c . || echo "0")
    echo -e "${GRAY}[DEBUG] Arquivos modificados encontrados: $COUNT${NC}"
fi

# Filtrar arquivos em diretorios proibidos
VIOLATIONS=""

for file in $MODIFIED_FILES; do
    for forbidden in "${FORBIDDEN_PATHS[@]}"; do
        if [[ "$file" == "$forbidden"* ]]; then
            VIOLATIONS="$VIOLATIONS$file"$'\n'
            break
        fi
    done
done

# Remover linha vazia final
VIOLATIONS=$(echo "$VIOLATIONS" | grep -v '^$' || true)

# Resultado
if [ -n "$VIOLATIONS" ]; then
    echo -e "${RED}[FAIL] Alteracoes detectadas em diretorios protegidos!${NC}"
    echo ""
    echo -e "${RED}Arquivos violando upgrade-safe:${NC}"
    echo -e "${RED}--------------------------------${NC}"

    while IFS= read -r v; do
        [ -n "$v" ] && echo -e "  ${YELLOW}- $v${NC}"
    done <<< "$VIOLATIONS"

    echo ""
    echo -e "${CYAN}SOLUCAO:${NC}"
    echo -e "  Para reverter essas alteracoes, execute:"
    echo ""

    while IFS= read -r v; do
        [ -n "$v" ] && echo -e "  ${GREEN}git restore -- $v${NC}"
    done <<< "$VIOLATIONS"

    echo ""
    echo -e "  Ou para reverter todos de uma vez:"
    echo -e "  ${GREEN}git restore -- vendor/ packages/Webkul/${NC}"
    echo ""

    if [ "$FIX" = true ]; then
        echo -e "${YELLOW}[FIX] Revertendo alteracoes automaticamente...${NC}"
        while IFS= read -r v; do
            if [ -n "$v" ]; then
                if git restore -- "$v" 2>/dev/null; then
                    echo -e "  ${GREEN}[OK] Revertido: $v${NC}"
                else
                    echo -e "  ${RED}[FAIL] Nao foi possivel reverter: $v${NC}"
                fi
            fi
        done <<< "$VIOLATIONS"
        echo ""
        echo -e "${CYAN}[INFO] Verifique com: git status${NC}"
        exit 0
    fi

    exit 1
else
    echo -e "${GREEN}[PASS] Nenhuma alteracao em diretorios protegidos!${NC}"
    echo ""
    echo -e "${GRAY}Diretorios verificados:${NC}"
    for p in "${FORBIDDEN_PATHS[@]}"; do
        echo -e "  ${GRAY}- $p${NC}"
    done
    echo ""
    echo -e "${GREEN}O projeto permanece upgrade-safe.${NC}"
    exit 0
fi
