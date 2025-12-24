# Operations

Documentacao operacional: deploy, runbooks e troubleshooting.

## Subpastas

| Pasta | Descricao |
|-------|-----------|
| [runbooks/](./runbooks/) | Procedimentos operacionais padrao (SOPs) |
| [troubleshooting/](./troubleshooting/) | Diagnosticos, debug, resolucao de problemas |

## Runbooks Disponiveis

| Runbook | Quando usar |
|---------|-------------|
| [RUNBOOK_THEME_SMOKE.md](./runbooks/RUNBOOK_THEME_SMOKE.md) | Apos deploy de alteracoes no tema |

## Scripts de Deploy

Veja [../../tools/](../../tools/) para scripts automatizados:

- `deploy-theme.sh` - Deploy completo com backup, git pull, cache clear

## Checklist Rapido Pos-Deploy

```
[ ] 1. Login carrega sem erro 500
[ ] 2. Tema ativo aplica cores/logos
[ ] 3. Preview funciona
[ ] 4. Rollback funciona
[ ] 5. Logs sem erros criticos
```

## Comandos Uteis

```bash
# Limpar cache
php artisan cache:clear

# Ver logs de tema
grep -i '\[Theme\]' storage/logs/laravel.log | tail -20

# Verificar tema atual
php artisan tinker --execute="DB::table('theme_configs')->first()"
```
