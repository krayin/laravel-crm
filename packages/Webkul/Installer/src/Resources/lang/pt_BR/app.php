<?php

return [
    'seeders' => [
        'attributes' => [
            'leads' => [
                'description'         => 'Descrição',
                'expected-close-date' => 'Data de Fechamento Esperada',
                'lead-value'          => 'Valor da Oportunidade',
                'sales-owner'         => 'Responsável pela Venda',
                'source'              => 'Origem',
                'title'               => 'Título',
                'type'                => 'Tipo',
                'pipeline'            => 'Funil',
                'stage'               => 'Estágio',
            ],

            'persons' => [
                'contact-numbers' => 'Números de Contato',
                'emails'          => 'E-mails',
                'job-title'       => 'Cargo',
                'name'            => 'Nome',
                'organization'    => 'Empresa',
                'sales-owner'     => 'Responsável pela Venda',
            ],

            'organizations' => [
                'address'     => 'Endereço',
                'name'        => 'Nome',
                'sales-owner' => 'Responsável pela Venda',
            ],

            'products' => [
                'description' => 'Descrição',
                'name'        => 'Nome',
                'price'       => 'Preço',
                'quantity'    => 'Quantidade',
                'sku'         => 'Código',
            ],

            'quotes' => [
                'adjustment-amount' => 'Valor de Ajuste',
                'billing-address'   => 'Endereço de Cobrança',
                'description'       => 'Descrição',
                'discount-amount'   => 'Valor do Desconto',
                'discount-percent'  => 'Percentual de Desconto',
                'expired-at'        => 'Expira em',
                'grand-total'       => 'Total Geral',
                'person'            => 'Pessoa',
                'sales-owner'       => 'Responsável pela Venda',
                'shipping-address'  => 'Endereço de Entrega',
                'sub-total'         => 'Subtotal',
                'subject'           => 'Assunto',
                'tax-amount'        => 'Valor do Imposto',
            ],

            'warehouses' => [
                'contact-address' => 'Endereço de Contato',
                'contact-emails'  => 'Emails de Contato',
                'contact-name'    => 'Nome do Contato',
                'contact-numbers' => 'Números de Contato',
                'description'     => 'Descrição',
                'name'            => 'Nome',
            ],
        ],

        'email' => [
            'activity-created'      => 'Atividade Adicionada',
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
                'default' => 'Funil Padrão',

                'pipeline-stages' => [
                    'follow-up'   => 'Acompanhamento',
                    'lost'        => 'Perdido',
                    'negotiation' => 'Negociação',
                    'new'         => 'Novo',
                    'prospect'    => 'Qualificado',
                    'won'         => 'Ganho',
                ],
            ],

            'source' => [
                'direct'   => 'Direto',
                'email'    => 'E-mail',
                'phone'    => 'Telefone',
                'web'      => 'Web',
                'web-form' => 'Formulário Web',
            ],

            'type' => [
                'existing-business' => 'Negócio Existente',
                'new-business'      => 'Novo Negócio',
            ],
        ],

        'user' => [
            'role' => [
                'administrator-role' => 'Função de Administrador',
                'administrator'      => 'Administrador',
            ],
        ],

        'workflow' => [
            'email-to-participants-after-activity-updation' => 'E-mails para participantes após atualização de atividade',
            'email-to-participants-after-activity-creation' => 'E-mails para participantes após adicionar atividade',
        ],
    ],

    'installer' => [
        'index' => [
            'create-administrator' => [
                'admin'            => 'Administrador',
                'krayin'           => 'Krayin',
                'confirm-password' => 'Confirmar Senha',
                'email'            => 'E-mail',
                'email-address'    => 'admin@example.com',
                'password'         => 'Senha',
                'title'            => 'Adicionar Administrador',
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
                'database-connection'         => 'Conexão com Banco de Dados',
                'database-hostname'           => 'Nome do Host do Banco de Dados',
                'database-name'               => 'Nome do Banco de Dados',
                'database-password'           => 'Senha do Banco de Dados',
                'database-port'               => 'Porta do Banco de Dados',
                'database-prefix'             => 'Prefixo do Banco de Dados',
                'database-username'           => 'Usuário do Banco de Dados',
                'default-currency'            => 'Moeda Padrão',
                'default-locale'              => 'Idioma Padrão',
                'default-timezone'            => 'Fuso Horário Padrão',
                'default-url'                 => 'URL Padrão',
                'default-url-link'            => 'https://localhost',
                'euro'                        => 'Euro (EUR)',
                'mysql'                       => 'MySQL',
                'pgsql'                       => 'pgSQL',
                'select-timezone'             => 'Selecionar Fuso Horário',
                'warning-message'             => 'Atenção! As configurações de idioma e moeda padrão não podem ser alteradas após definidas.',
                'united-states-dollar'        => 'Dólar Americano (USD)',
            ],

            'installation-processing' => [
                'krayin'       => 'Instalação do Krayin',
                'krayin-info'  => 'Criando as tabelas do banco de dados, isso pode levar alguns momentos',
                'title'        => 'Instalação',
            ],

            'installation-completed' => [
                'admin-panel'                => 'Painel de Administração',
                'krayin-forums'              => 'Fórum Krayin',
                'customer-panel'             => 'Painel do Cliente',
                'explore-krayin-extensions'  => 'Explorar Extensões Krayin',
                'title'                      => 'Instalação Concluída',
                'title-info'                 => 'Krayin foi instalado com sucesso no seu sistema.',
            ],

            'ready-for-installation' => [
                'create-databsae-table'   => 'Adicionar tabela do banco de dados',
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
            'krayin-info'              => 'um projeto comunitário de',
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
