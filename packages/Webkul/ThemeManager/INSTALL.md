# Guia de Instalação - ThemeManager

Este guia apresenta os comandos necessários para instalar e configurar o ThemeManager no Krayin CRM.

## Pré-requisitos

Antes de começar, certifique-se de que:

- ✅ Krayin CRM está instalado e funcionando
- ✅ PHP 8.2+ está instalado
- ✅ MySQL 8.0+ está rodando
- ✅ Composer está instalado
- ✅ Extensões PHP necessárias estão habilitadas:
  - gd
  - mbstring
  - openssl
  - curl
  - fileinfo
  - pdo_mysql

## Verificação Prévia

### 1. Verificar versão do PHP

```bash
php -v
# Deve retornar: PHP 8.2.x ou superior
```

### 2. Verificar extensões PHP

```bash
php -m | grep -E 'gd|mbstring|openssl|curl|fileinfo|pdo_mysql'
```

Todas as extensões listadas acima devem aparecer.

### 3. Verificar MySQL

```bash
# Windows (PowerShell)
powershell -Command "Get-Service -Name MySQL* | Select-Object Status,Name"

# Linux/Mac
mysql --version
```

## Passo a Passo da Instalação

### Passo 1: Verificar Estrutura do Package

O package já deve estar em:
```
packages/Webkul/ThemeManager/
```

Verifique se todos os arquivos existem:

```bash
# Windows (PowerShell)
powershell -Command "Test-Path packages/Webkul/ThemeManager/composer.json"

# Linux/Mac
ls -la packages/Webkul/ThemeManager/
```

### Passo 2: Atualizar Autoload do Composer

Verifique se o autoload foi adicionado ao `composer.json` raiz:

```json
{
    "autoload": {
        "psr-4": {
            "Webkul\\ThemeManager\\": "packages/Webkul/ThemeManager/src"
        }
    }
}
```

Execute:

```bash
# Windows (PowerShell)
powershell -Command "cd 'C:\Users\Usuario\Desktop\Krayin-\laravel-crm'; C:\php\php.exe C:\php\composer.phar dump-autoload"

# Linux/Mac
composer dump-autoload
```

**Saída esperada:**
```
Generated optimized autoload files containing 9241 classes
```

### Passo 3: Registrar Módulo no Concord

Verifique se o módulo foi adicionado ao `config/concord.php`:

```php
'modules' => [
    // ... outros módulos ...

    // ThemeManager - SEMPRE POR ÚLTIMO
    \Webkul\ThemeManager\Providers\ModuleServiceProvider::class,
],
```

**IMPORTANTE:** O ThemeManager deve ser o último módulo registrado!

### Passo 4: Executar Migrations

**ATENÇÃO:** Certifique-se de que o MySQL está rodando antes de executar este comando.

```bash
# Windows (PowerShell)
powershell -Command "cd 'C:\Users\Usuario\Desktop\Krayin-\laravel-crm'; C:\php\php.exe artisan migrate"

# Linux/Mac
php artisan migrate
```

**Saída esperada:**
```
Migrating: 2024_12_20_000001_create_theme_configs_table
Migrated:  2024_12_20_000001_create_theme_configs_table (XX.XXms)
```

Este comando cria:
- Tabela `theme_configs` com 38 campos
- Registro padrão com configurações iniciais

### Passo 5: Criar Link Simbólico para Storage

O ThemeManager armazena uploads em `storage/app/public/theme-manager/`.
É necessário criar um link simbólico:

```bash
# Windows (PowerShell como Administrador)
powershell -Command "cd 'C:\Users\Usuario\Desktop\Krayin-\laravel-crm'; C:\php\php.exe artisan storage:link"

# Linux/Mac
php artisan storage:link
```

**Saída esperada:**
```
The [public/storage] link has been connected to [storage/app/public].
The links have been created.
```

### Passo 6: Configurar Permissões (Linux/Mac)

**Apenas para Linux/Mac:**

```bash
# Dar permissões de escrita ao storage
chmod -R 775 storage/
chown -R www-data:www-data storage/

# Dar permissões ao diretório de uploads
chmod -R 775 public/storage
chown -R www-data:www-data public/storage
```

**Windows:** As permissões geralmente são automáticas. Se houver problemas, execute como Administrador.

### Passo 7: Limpar Caches

```bash
# Windows (PowerShell)
powershell -Command "cd 'C:\Users\Usuario\Desktop\Krayin-\laravel-crm'; C:\php\php.exe artisan optimize:clear"

# Linux/Mac
php artisan optimize:clear
```

Este comando limpa:
- Cache de configuração
- Cache de rotas
- Cache de views
- Cache de eventos
- Cache geral

**Saída esperada:**
```
Configuration cache cleared successfully.
Route cache cleared successfully.
Compiled views cleared successfully.
Application cache cleared successfully.
Compiled services and packages files removed successfully.
```

### Passo 8: Verificar Instalação

#### 8.1. Verificar Rotas

```bash
# Windows (PowerShell)
powershell -Command "cd 'C:\Users\Usuario\Desktop\Krayin-\laravel-crm'; C:\php\php.exe artisan route:list | Select-String 'theme'"

# Linux/Mac
php artisan route:list | grep theme
```

**Saída esperada:**
```
GET|HEAD   admin/settings/theme ........ admin.settings.theme.index
POST       admin/settings/theme ........ admin.settings.theme.update
```

#### 8.2. Testar Helper via Tinker

```bash
# Windows (PowerShell)
powershell -Command "cd 'C:\Users\Usuario\Desktop\Krayin-\laravel-crm'; C:\php\php.exe artisan tinker"

# Linux/Mac
php artisan tinker
```

Dentro do Tinker, execute:

```php
// Verificar se o helper existe
app('theme')

// Verificar se está ativo (padrão: false)
app('theme')->isActive()

// Obter configuração
app('theme')->getConfig()

// Sair do Tinker
exit
```

### Passo 9: Acessar Interface

1. Acesse o Krayin CRM no navegador
2. Faça login como administrador
3. No menu lateral, procure: **Configurações → Tema**

Se o menu aparecer, a instalação foi bem-sucedida!

## Comandos de Manutenção

### Limpar Cache do Tema

```bash
# Via Artisan
php artisan cache:clear

# Via Tinker
php artisan tinker
>>> app('theme')->clearCache()
>>> exit
```

### Reexecutar Migrations (Reset)

**ATENÇÃO:** Isto apagará todos os dados de configuração do tema!

```bash
php artisan migrate:rollback --step=1
php artisan migrate
```

### Atualizar Autoload

Sempre que modificar arquivos PHP do package:

```bash
composer dump-autoload
php artisan optimize:clear
```

## Troubleshooting

### Erro: "Class ThemeManager not found"

**Solução:**
```bash
composer dump-autoload
php artisan optimize:clear
```

### Erro: "Table 'krayin.theme_configs' doesn't exist"

**Solução:**
```bash
# Verificar se MySQL está rodando
# Então executar:
php artisan migrate
```

### Erro: "Permission denied" ao fazer upload

**Linux/Mac:**
```bash
chmod -R 775 storage/
chown -R www-data:www-data storage/
```

**Windows:**
Execute o servidor como Administrador.

### Menu "Tema" não aparece

**Solução:**
```bash
composer dump-autoload
php artisan optimize:clear
# Recarregue a página com Ctrl+F5
```

### CSS não está aplicando

**Solução:**
```bash
php artisan view:clear
php artisan cache:clear
# Limpe o cache do navegador (Ctrl+Shift+Del)
```

### Erro: "SQLSTATE[HY000] [2002] Connection refused"

**Solução:**
- Inicie o MySQL
- Verifique credenciais no `.env`
- Teste conexão:
  ```bash
  php artisan tinker
  >>> DB::connection()->getPdo()
  ```

## Checklist de Instalação

Marque cada item conforme concluir:

- [ ] PHP 8.2+ instalado
- [ ] MySQL rodando
- [ ] Extensões PHP habilitadas
- [ ] Package em `packages/Webkul/ThemeManager/`
- [ ] Autoload adicionado ao `composer.json` raiz
- [ ] Módulo registrado em `config/concord.php` (por último)
- [ ] `composer dump-autoload` executado
- [ ] `php artisan migrate` executado
- [ ] `php artisan storage:link` executado
- [ ] Permissões configuradas (Linux/Mac)
- [ ] `php artisan optimize:clear` executado
- [ ] Rotas verificadas (`route:list | grep theme`)
- [ ] Helper testado via Tinker
- [ ] Menu "Tema" visível na interface
- [ ] Primeira configuração salva com sucesso

## Próximos Passos

Após instalação bem-sucedida:

1. 📖 Leia o [README.md](README.md) para entender todas as funcionalidades
2. 🎨 Configure suas cores de marca
3. 🖼️ Faça upload dos seus logos
4. 🔐 Customize a página de login
5. 📦 Configure empty states (opcional)
6. ✅ Ative o tema na interface

## Suporte

Se encontrar problemas:

1. Verifique os logs: `tail -f storage/logs/laravel.log`
2. Consulte a seção Troubleshooting do [README.md](README.md)
3. Reporte issues no repositório

---

**Instalação completa! 🎉**

Agora você pode começar a personalizar a aparência do seu Krayin CRM.
