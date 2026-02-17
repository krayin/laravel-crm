#!/bin/bash
# =============================================================================
# BrandKit Test Suite Runner (Unix/Linux/Mac)
# =============================================================================
# Roda todos os testes do sistema de temas/BrandKit em isolamento total.
#
# Uso:
#   ./scripts/test-brandkit.sh              # Roda suite completa
#   ./scripts/test-brandkit.sh --coverage   # Com cobertura de codigo
#   ./scripts/test-brandkit.sh --filter "Preview"  # Filtra por nome
#
# Caracteristicas:
#   - SQLite :memory: (nao depende das migrations do CRM)
#   - Array cache (sem file cache)
#   - ~10-15 segundos para 77 testes
# =============================================================================

set -e

COVERAGE=""
FILTER=""

# Parse arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --coverage)
            COVERAGE="yes"
            shift
            ;;
        --filter)
            FILTER="$2"
            shift 2
            ;;
        *)
            echo "Uso: $0 [--coverage] [--filter PATTERN]"
            exit 1
            ;;
    esac
done

echo ""
echo "========================================"
echo " BrandKit Test Suite"
echo "========================================"
echo ""

# Test files
TEST_FILES="tests/Unit/BrandKitResolverTest.php \
tests/Unit/BrandKitSnapshotServiceTest.php \
tests/Unit/BrandKitCacheInvalidatorTest.php \
tests/Unit/PreviewSessionTest.php \
tests/Unit/ThemeContextFactoryTest.php"

# Build command
CMD="php vendor/bin/phpunit $TEST_FILES"

if [ -z "$COVERAGE" ]; then
    CMD="$CMD --no-coverage"
fi

if [ -n "$FILTER" ]; then
    CMD="$CMD --filter \"$FILTER\""
fi

echo "Executando: $CMD"
echo ""

# Execute
eval $CMD
EXIT_CODE=$?

echo ""
echo "========================================"
if [ $EXIT_CODE -eq 0 ]; then
    echo " SUCESSO - Todos os testes passaram"
else
    echo " FALHA - Alguns testes falharam"
fi
echo "========================================"
echo ""

exit $EXIT_CODE
