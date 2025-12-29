# Theme Preview - Guia de Uso

O sistema de preview permite visualizar um tema sem aplicá-lo permanentemente ao sistema.

## Como Funciona

1. O preview é **por sessão** - só afeta o usuário que ativou
2. **Não persiste no banco** - não afeta outros usuários
3. Requer **autenticação** e permissão `settings`
4. O tema de preview tem **prioridade** sobre o tema selecionado no banco

## Ativar Preview

Adicione o parâmetro `?theme_preview=SLUG` a qualquer URL do admin:

```
# Ver login com tema stelium-sanctuary
https://seusite.com/admin/login?theme_preview=stelium-sanctuary

# Ver dashboard com tema starter
https://seusite.com/admin?theme_preview=starter

# Ver configurações com tema meu-tema
https://seusite.com/admin/settings?theme_preview=meu-tema
```

## Limpar Preview

Para voltar ao tema original, use `?clear_preview=1`:

```
https://seusite.com/admin?clear_preview=1
```

Ou simplesmente encerre a sessão (logout).

## Temas Disponíveis

Os temas disponíveis são lidos automaticamente de `storage/app/public/themes/`:

| Slug | Nome |
|------|------|
| `default` | Padrão Krayin |
| `starter` | Roxo Moderno |
| `meu-tema` | Azul Oceano |
| `theme-complete` | Verde Natureza |
| `stelium-sanctuary` | Stelium Sanctuary |
| `theme-minimal` | (Tema mínimo) |
| `theme-partial` | (Tema parcial) |

## Indicador de Preview

Quando em modo preview, o `ThemeContext` terá:

```php
$themeContext->isPreview === true
$themeContext->inPreviewMode() === true
```

A body class `theme-preview` é adicionada automaticamente.

## Arquitetura

```
Request com ?theme_preview=slug
       ↓
HandleThemePreview (middleware)
       ↓
Valida: usuário logado + permissão settings
       ↓
Salva em: session('theme_preview')
       ↓
ThemeContextFactory
       ↓
Prioriza session('theme_preview') sobre DB
       ↓
ThemeContext criado com tema do preview
```

## Segurança

- Apenas usuários autenticados com permissão `settings` podem ativar preview
- O slug é sanitizado (só permite a-z, 0-9, -, _)
- Temas inexistentes são ignorados silenciosamente
- Logs são gerados para auditoria

## Logs

O sistema gera logs em `storage/logs/laravel.log`:

```
[Theme] Preview activated {"slug":"starter","user_id":1}
[Theme] Preview cleared {"previous_slug":"starter","user_id":1}
[Theme] Preview requested for non-existent theme {"slug":"invalid-theme"}
```

## Uso Programático

```php
use App\Http\Middleware\HandleThemePreview;

// Verificar se há preview ativo
if (HandleThemePreview::hasActivePreview()) {
    $previewSlug = HandleThemePreview::getActivePreview();
}

// No ThemeContext
if ($themeContext->isPreview) {
    // Mostrar indicador visual
}
```

## Casos de Uso

1. **Testar tema antes de aplicar**: Visualize como ficará sem afetar outros usuários
2. **Desenvolvimento de temas**: Itere rapidamente sem salvar no banco
3. **Demonstração para stakeholders**: Mostre opções de tema sem commit
4. **Debug**: Compare comportamento entre temas

---

*Documentação gerada em Dezembro 2024 - ThemeManager v1.0*
