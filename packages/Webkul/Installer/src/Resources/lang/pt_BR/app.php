<?php

return [
    'seeders' => [
        'attributes' => [
            'leads' => [
                'description'         => 'Descrição',
                'expected-close-date' => 'Fechamento',
                'lead-value'          => 'Valor',
                'sales-owner'         => 'Vendedor',
                'source'              => 'Origem',
                'title'               => 'Título',
                'type'                => 'Tipo',
                'pipeline'            => 'Funil',
                'stage'               => 'Etapa',
            ],

            'persons' => [
                'contact-numbers' => 'Telefone',
                'emails'          => 'E-mails',
                'job-title'       => 'Cargo',
                'name'            => 'Nome',
                'organization'    => 'Empresa',
                'sales-owner'     => 'Vendedor',
            ],

            'organizations' => [
                'address'     => 'Endereço',
                'name'        => 'Nome',
                'sales-owner' => 'Vendedor',
            ],

            'products' => [
                'description' => 'Descrição',
                'name'        => 'Nome',
                'price'       => 'Preço',
                'quantity'    => 'Quantidade',
                'sku'         => 'Código',
            ],

            'quotes' => [
                'adjustment-amount' => 'Ajuste',
                'billing-address'   => 'Cobrança',
                'description'       => 'Descrição',
                'discount-amount'   => 'Desconto',
                'discount-percent'  => 'Desconto (%)',
                'expired-at'        => 'Expira em',
                'grand-total'       => 'Total geral',
                'person'            => 'Pessoa',
                'sales-owner'       => 'Vendedor',
                'shipping-address'  => 'Entrega',
                'sub-total'         => 'Subtotal',
                'subject'           => 'Assunto',
                'tax-amount'        => 'Imposto',
            ],

            'warehouses' => [
                'contact-address' => 'Contato',
                'contact-emails'  => 'E-mails de contato',
                'contact-name'    => 'Nome do contato',
                'contact-numbers' => 'Telefone',
                'description'     => 'Descrição',
                'name'            => 'Nome',
            ],
        ],

        'email' => [
            'activity-created'      => 'Atividade adicionada',
            'activity-modified'     => 'Atividade modificada',
            'date'                  => 'Data',
            'new-activity'          => 'Você tem uma nova atividade, veja os detalhes abaixo',
            'new-activity-modified' => 'Uma nova atividade foi modificada, veja os detalhes abaixo',
            'participants'          => 'Participantes',
            'title'                 => 'Título',
            'type'                  => 'Tipo',
        ],

        'lead' => [
            'pipeline' => [
                'default' => 'Funil padrão',

                'pipeline-stages' => [
                    'follow-up'   => 'Qualificação',
                    'lost'        => 'Perdido',
                    'negotiation' => 'Negociação',
                    'new'         => 'Novo negócio',
                    'prospect'    => 'Proposta',
                    'won'         => 'Ganho',
                ],
            ],

            'source' => [
                'direct'   => 'Direto',
                'email'    => 'E-mail',
                'phone'    => 'Telefone',
                'web'      => 'Web',
                'web-form' => 'Formulário web',
            ],

            'type' => [
                'existing-business' => 'Base atual',
                'new-business'      => 'Novo negócio',
            ],
        ],

        'user' => [
            'role' => [
                'administrator-role' => 'Função de administrador',
                'administrator'      => 'Administrador',
            ],
        ],

        'workflow' => [
            'email-to-participants-after-activity-updation' => 'E-mails para participantes após atualização de atividade',
            'email-to-participants-after-activity-creation' => 'E-mails para participantes após criação de atividade',
        ],
    ],

    'installer' => [
        'index' => [
            'create-administrator' => [
                'admin'            => 'Administrador',
                'krayin'           => 'Krayin',
                'confirm-password' => 'Confirmar senha',
                'email'            => 'E-mail',
                'email-address'    => 'admin@example.com',
                'password'         => 'Senha',
                'title'            => 'Criar administrador',
            ],

            'environment-configuration' => [
                'algerian-dinar'              => 'Dinar Argelino (DZD)',
                'allowed-currencies'          => 'Moedas Permitidas',
                'allowed-locales'             => 'Idiomas Permitidos',
                'application-name'            => 'Nome do Aplicativo',
                'argentine-peso'              => 'Peso Argentino (ARS)',
                'australian-dollar'           => 'Dólar Australiano (AUD)',
                'krayin'                      => 'Krayin',
                'bangladeshi-taka'            => 'Taka de Bangladesh (BDT)',
                'brazilian-real'              => 'Real Brasileiro (BRL)',
                'british-pound-sterling'      => 'Libra Esterlina (GBP)',
                'canadian-dollar'             => 'Dólar Canadense (CAD)',
                'cfa-franc-bceao'             => 'Franco CFA BCEAO (XOF)',
                'cfa-franc-beac'              => 'Franco CFA BEAC (XAF)',
                'chilean-peso'                => 'Peso Chileno (CLP)',
                'chinese-yuan'                => 'Yuan Chinês (CNY)',
                'colombian-peso'              => 'Peso Colombiano (COP)',
                'czech-koruna'                => 'Coroa Checa (CZK)',
                'danish-krone'                => 'Coroa Dinamarquesa (DKK)',
                'database-connection'         => 'Conexão com banco de dados',
                'database-hostname'           => 'Nome do host do banco de dados',
                'database-name'               => 'Nome do banco de dados',
                'database-password'           => 'Senha do banco de dados',
                'database-port'               => 'Porta do banco de dados',
                'database-prefix'             => 'Prefixo do banco de bados',
                'database-username'           => 'Usuário do banco de dados',
                'default-currency'            => 'Moeda (BRL)',
                'default-locale'              => 'Idioma',
                'default-timezone'            => 'Fuso horário',
                'default-url'                 => 'URL Padrão',
                'default-url-link'            => 'https://localhost',
                'euro'                        => 'Euro (EUR)',
                'mysql'                       => 'MySQL',
                'pgsql'                       => 'pgSQL',
                'select-timezone'             => 'Selecione o fuso horário',
                'warning-message'             => 'Atenção! As configurações de idioma e moeda padrão não podem ser alteradas após definidas.',
                'united-states-dollar'        => 'Dólar Americano (USD)',
            ],

            'installation-processing' => [
                'krayin'       => 'Instalação do Krayin',
                'krayin-info'  => 'Criando as tabelas do banco de dados, isso pode levar alguns minutos',
                'title'        => 'Instalação',
            ],

            'installation-completed' => [
                'admin-panel'                => 'Painel de administração',
                'krayin-forums'              => 'Fórum Krayin',
                'customer-panel'             => 'Painel do Cliente',
                'explore-krayin-extensions'  => 'Explorar Extensões Krayin',
                'title'                      => 'Instalação Concluída',
                'title-info'                 => 'Krayin foi instalado com sucesso.',
            ],

            'ready-for-installation' => [
                'create-databsae-table'   => 'Criar tabela do banco de dados',
                'install'                 => 'Instalação',
                'start-installation'      => 'Iniciar Instalação',
                'title'                   => 'Pronto para Instalação',
            ],

            'start' => [
                'locale'        => 'Idioma',
                'main'          => 'Início',
                'select-locale' => 'Selecionar Idioma',
                'title'         => 'Instalação do Krayin',
                'welcome-title' => 'Bem-vindo ao Krayin',
            ],

            'server-requirements' => [
                'php-version' => '8.1 ou superior',
                'title'       => 'Requisitos do Sistema',
            ],

            'back'                     => 'Voltar',
            'krayin'                   => 'Krayin',
            'krayin-info'              => 'um projeto de código aberto de',
            'krayin-logo'              => 'Logotipo Krayin',
            'continue'                 => 'Continuar',
            'installation-description' => 'A instalação do Krayin geralmente envolve várias etapas. Aqui está uma visão geral do processo de instalação do Krayin',
            'installation-info'        => 'Estamos felizes em ver você aqui!',
            'installation-title'       => 'Bem-vindo à Instalação',
            'installation-wizard'      => 'Assistente de Instalação - Idioma',
            'title'                    => 'Instalador do Krayin',
            'webkul'                   => 'Webkul',
        ],
    ],
];
