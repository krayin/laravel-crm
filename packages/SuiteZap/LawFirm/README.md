# SuiteZap LawFirm Extension for Krayin CRM

Extensão modular para transformar o Krayin CRM em um sistema especializado para escritórios de advocacia.

## Descrição

Este pacote adiciona funcionalidades específicas para gestão de escritórios de advocacia ao Krayin CRM, incluindo:

- **Gestão de Processos**: Controle completo de processos judiciais
- **Agenda de Audiências**: Calendário de audiências e compromissos
- **Gestão de Documentos**: Organização de documentos processuais
- **Integração com Clientes**: Vinculação de processos aos clientes do CRM
- **Relatórios Jurídicos**: Relatórios específicos para advocacia

## Estrutura do Pacote

```
packages/SuiteZap/LawFirm/
├── src/
│   ├── Config/              # Configurações do módulo
│   ├── Contracts/           # Interfaces de contratos
│   ├── Database/
│   │   └── Migrations/      # Migrações de banco de dados
│   ├── Http/
│   │   └── Controllers/     # Controllers
│   │       └── Admin/       # Controllers administrativos
│   ├── Models/              # Modelos Eloquent
│   ├── Providers/           # Service Providers
│   ├── Repositories/        # Repositórios
│   ├── Resources/
│   │   ├── assets/          # CSS, JS, imagens
│   │   ├── lang/            # Traduções
│   │   │   ├── en/          # Inglês
│   │   │   └── pt_BR/       # Português Brasil
│   │   └── views/           # Views Blade
│   │       └── admin/       # Views administrativas
│   └── Routes/              # Arquivos de rotas
│       ├── admin.php        # Rotas administrativas
│       └── api.php          # Rotas de API
└── composer.json            # Configuração do Composer
```

## Instalação

O pacote já está configurado no projeto principal. Para ativá-lo:

1. Execute o autoload do Composer:
```bash
composer dump-autoload
```

2. Limpe o cache do Laravel:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

3. Publique os assets (quando necessário):
```bash
php artisan vendor:publish --tag=lawfirm-assets
```

## Uso

Após a instalação, acesse o módulo através do painel administrativo:

```
http://seu-dominio.com/admin/lawfirm
```

## Desenvolvimento

### Adicionando Novos Models

1. Crie o model em `src/Models/`
2. Crie o contrato em `src/Contracts/`
3. Crie o repository em `src/Repositories/`
4. Registre o model em `ModuleServiceProvider.php`

### Adicionando Migrations

Crie as migrations em `src/Database/Migrations/` seguindo o padrão Laravel.

### Adicionando Rotas

- Rotas administrativas: `src/Routes/admin.php`
- Rotas de API: `src/Routes/api.php`

## Dependências

Este módulo depende dos seguintes pacotes do Krayin:

- krayin/laravel-activity
- krayin/laravel-attribute
- krayin/laravel-contact
- krayin/laravel-core
- krayin/laravel-email
- krayin/laravel-lead
- krayin/laravel-product
- krayin/laravel-user

## Licença

MIT License

## Autor

SuiteZap - sac@suitezap.com.br
