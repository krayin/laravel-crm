# Runbook: Smoke Test de Tema (Pos-Deploy)

Checklist manual para validar que o sistema de temas esta funcionando apos deploy.

**Tempo estimado:** 5-10 minutos  
**Quando executar:** Apos cada deploy que altere arquivos em `app/Support/Theme*` ou middlewares de tema.

---

## Pre-requisitos

- [ ] Acesso ao ambiente de staging/producao
- [ ] Usuario admin com permissao `settings.theme.manage`
- [ ] Pelo menos 1 tema customizado no storage (`storage/app/public/themes/{slug}/theme.json`)

---

## 1. Teste de Login (Critico)

**Objetivo:** Garantir que a pagina de login carrega sem erros.

| Passo | Acao | Resultado Esperado |
|-------|------|-------------------|
| 1.1 | Acesse `/admin/login` (deslogado) | Pagina carrega sem erro 500 |
| 1.2 | Verifique o console do browser (F12) | Sem erros JS criticos |
| 1.3 | Verifique se CSS carrega | Pagina estilizada (nao quebrada) |

**Se falhar:** Verifique logs em `storage/logs/laravel.log` para erros de ThemeContext.

---

## 2. Teste de Tema Padrao (is_active=0)

**Objetivo:** Validar comportamento com tema desativado.

| Passo | Acao | Resultado Esperado |
|-------|------|-------------------|
| 2.1 | No banco, set `is_active=0` na tabela `theme_configs` | - |
| 2.2 | Limpe cache: `php artisan cache:clear` | - |
| 2.3 | Acesse `/admin/login` | Login com aparencia padrao Krayin |
| 2.4 | Faca login como admin | Dashboard carrega normalmente |

---

## 3. Teste de Tema Ativo (is_active=1)

**Objetivo:** Validar que configuracoes do DB sao aplicadas.

| Passo | Acao | Resultado Esperado |
|-------|------|-------------------|
| 3.1 | Acesse Settings > Theme no admin | Pagina carrega |
| 3.2 | Selecione um tema customizado | - |
| 3.3 | Clique em "Aplicar" | Redirect com mensagem de sucesso |
| 3.4 | Acesse `/admin/login` em aba anonima | Tema aplicado (cores, logo, bg) |
| 3.5 | Verifique cor primaria | Deve ser a cor do DB (se preenchida) |

---

## 4. Teste de Preview (Sessao)

**Objetivo:** Validar que preview nao afeta outros usuarios.

| Passo | Acao | Resultado Esperado |
|-------|------|-------------------|
| 4.1 | Acesse Settings > Theme | - |
| 4.2 | Clique em "Preview" de um tema diferente do ativo | - |
| 4.3 | URL deve conter `?theme_preview={slug}` | Tema de preview aparece |
| 4.4 | Em outra aba/browser (mesmo user), acesse `/admin` | Tema original (sem preview) |
| 4.5 | Em browser anonimo, acesse `/admin/login` | Tema ativo (nao o preview) |
| 4.6 | Clique em "Limpar Preview" | Volta ao tema ativo |

---

## 5. Teste de Rollback

**Objetivo:** Validar restauracao de tema anterior.

| Passo | Acao | Resultado Esperado |
|-------|------|-------------------|
| 5.1 | Anote o tema atual | - |
| 5.2 | Troque para outro tema | Sucesso |
| 5.3 | Clique em "Voltar ao anterior" | Tema anterior restaurado |
| 5.4 | Clique em "Restaurar Padrao" | Tema volta para `default`, `is_active=0` |

---

## 6. Teste de Fallback (Tema Inexistente)

**Objetivo:** Validar comportamento quando tema no DB nao existe no storage.

| Passo | Acao | Resultado Esperado |
|-------|------|-------------------|
| 6.1 | No banco, set `selected_theme='tema_fake'` | - |
| 6.2 | Limpe cache: `php artisan cache:clear` | - |
| 6.3 | Acesse `/admin/login` | **NAO deve dar erro 500** |
| 6.4 | Tema deve ser `default` | Aparencia padrao |
| 6.5 | Verifique log | Deve ter warning `[Theme] Selected theme not found` |

---

## 7. Teste de Precedencia (DB > theme.json > defaults)

**Objetivo:** Validar ordem de prioridade das configuracoes.

| Passo | Acao | Resultado Esperado |
|-------|------|-------------------|
| 7.1 | Crie tema com `color_primary: #FF0000` no theme.json | - |
| 7.2 | No DB, deixe `color_primary=NULL`, `is_active=1` | - |
| 7.3 | Acesse login | Cor primaria = `#FF0000` (theme.json) |
| 7.4 | No DB, set `color_primary='#0000FF'` | - |
| 7.5 | Limpe cache e acesse login | Cor primaria = `#0000FF` (DB vence) |

---

## 8. Teste de Permissoes (ACL)

**Objetivo:** Validar que usuarios sem permissao nao acessam config de tema.

| Passo | Acao | Resultado Esperado |
|-------|------|-------------------|
| 8.1 | Crie usuario sem permissao `settings.theme.*` | - |
| 8.2 | Logue como esse usuario | - |
| 8.3 | Acesse `/admin/settings/theme` | Erro 403 (Forbidden) |
| 8.4 | Tente POST em `/admin/settings/theme/restore` | Erro 403 |

---

## Comandos Uteis

```bash
# Limpar todo o cache
php artisan cache:clear

# Verificar tema atual no banco
php artisan tinker --execute="echo DB::table('theme_configs')->first()->selected_theme;"

# Verificar se tema existe no storage
ls storage/app/public/themes/

# Verificar logs de tema
grep -i "\[Theme\]" storage/logs/laravel.log | tail -20

# Rodar testes unitarios de tema
php artisan test --filter=ThemeContextFactoryTest
```

---

## Checklist Resumido (Pos-Deploy)

- [ ] Login carrega sem erro 500
- [ ] Tema ativo aplica cores/logos
- [ ] Preview funciona e nao vaza para outros usuarios
- [ ] Rollback funciona
- [ ] Fallback para default quando tema nao existe
- [ ] Usuarios sem permissao recebem 403

---

## Troubleshooting

### Erro 500 no login
1. Verifique `storage/logs/laravel.log`
2. Procure por `ThemeContext` ou `ThemeConfigResolver`
3. Limpe cache: `php artisan cache:clear`
4. Verifique se tabela `theme_configs` existe e tem registro id=1

### Tema nao aplica apos trocar
1. Limpe cache: `php artisan cache:clear`
2. Verifique se `is_active=1` no banco
3. Verifique se `selected_theme` aponta para tema existente

### Preview afetando outros usuarios
1. Verifique se `CACHE_DRIVER=file` (nao pode ser `array` em producao)
2. Preview usa session, nao cache - verifique `SESSION_DRIVER`

### Permissoes nao funcionam
1. Verifique se ACL foi registrada: `php artisan bouncer:clean`
2. Verifique role do usuario tem `permission_type='custom'`
3. Verifique se permissao `settings.theme.*` foi atribuida

---

**Ultima atualizacao:** 2024-12-24  
**Autor:** DevLead  
**Versao:** 1.0
