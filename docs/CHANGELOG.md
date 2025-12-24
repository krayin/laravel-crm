# Changelog de Customizações - Krayin CRM

> Documento único de registro de todas as alterações realizadas no projeto.
> Atualizado automaticamente a cada tarefa executada.

---

## Índice de Alterações

| Data | Alteração | Arquivos Afetados |
|------|-----------|-------------------|
| 2025-12-23 | Adição de eventos before/after no ThemeController | ThemeController.php |
| 2025-12-23 | Remoção das seções Login Page da interface Theme | index.blade.php |

---

## Detalhes das Alterações

### [2025-12-23] Adição de Eventos Before/After no ThemeController

**Objetivo:** Permitir que outros pacotes reajam a alterações de tema através de eventos.

**Arquivo Modificado:**
- `packages/Webkul/ThemeManager/Http/Controllers/ThemeController.php`

**Alterações:**
```php
// Adicionado import do Event facade
use Illuminate\Support\Facades\Event;

// No método update(), adicionados eventos:
Event::dispatch("theme.update.before", $request->all());
// ... lógica de update ...
Event::dispatch("theme.update.after", $config);
```

**Como usar os eventos:**
```php
// Em qualquer ServiceProvider
Event::listen('theme.update.before', function($data) {
    // Executado ANTES da atualização do tema
    // $data contém os dados do request
});

Event::listen('theme.update.after', function($config) {
    // Executado APÓS a atualização do tema
    // $config contém o objeto ThemeConfig atualizado
});
```

**Conformidade:** Segue o padrão Krayin de eventos `entity.action.before/after`.

---

### [2025-12-23] Remoção das Seções Login Page da Interface Theme

**Objetivo:** Remover temporariamente as opções de customização de Login Page da interface administrativa, mantendo o backend intacto para futura reativação.

**Arquivo Modificado:**
- `packages/Webkul/ThemeManager/Resources/views/admin/settings/theme/index.blade.php`

**Seções Comentadas:**
1. **SEÇÃO 4 - Página de Login (Background)**
   - Imagem de fundo
   - Zoom da imagem
   - Opacidade da sobreposição
   - Mostrar "Powered By"

2. **SEÇÃO 5 - Caixa de Login Customizada**
   - Toggle de habilitação
   - Imagem de fundo do card
   - Opacidade da imagem
   - Cor do overlay
   - Título de boas-vindas
   - Subtítulo
   - Efeito de brilhos
   - Link de ajuda
   - E-mail de suporte
   - Custom HTML/CSS/JS Code

3. **JavaScript Associado**
   - Função `toggleLoginCardOptions()` comentada

**Tipo de Alteração:** Comentários Blade `{{-- --}}`

**Como Reativar:**
1. Abrir o arquivo `index.blade.php`
2. Localizar o bloco com o comentário:
   ```
   SEÇÕES DE LOGIN PAGE TEMPORARIAMENTE DESABILITADAS
   ```
3. Remover os `{{--` e `--}}` das seções 4 e 5
4. Remover os comentários do bloco JavaScript no final do arquivo

**Backend Preservado:**
- ThemeController.php - Campos de login ainda são processados
- ThemeConfig.php - Model mantém todos os campos
- ThemeConfigRepository.php - Repository processa uploads de login
- Migrations - Colunas de login permanecem no banco

---

## Arquivos do Projeto Modificados (Resumo)

| Arquivo | Tipo de Modificação | Status |
|---------|---------------------|--------|
| `packages/Webkul/ThemeManager/Http/Controllers/ThemeController.php` | Adição de eventos | Ativo |
| `packages/Webkul/ThemeManager/Resources/views/admin/settings/theme/index.blade.php` | Seções comentadas | Parcialmente desabilitado |

---

## Notas Técnicas

### Melhor Forma de Usar HTML Próprio na Tela de Login

O ThemeManager já está configurado para sobrescrever a view de login do Admin através do método `overrideLoginView()` no ServiceProvider.

**Arquivo para customização:**
```
packages/Webkul/ThemeManager/Resources/views/admin/sessions/login.blade.php
```

**Mecanismo utilizado:**
```php
// ThemeManagerServiceProvider.php
protected function overrideLoginView()
{
    $this->app['view']->prependNamespace(
        'admin',
        __DIR__ . '/../Resources/views'
    );
}
```

Para usar HTML completamente customizado, basta editar o arquivo `login.blade.php` acima.

---

*Última atualização: 2025-12-23*
