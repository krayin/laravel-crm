<?php

return [
    'menu' => [
        'theme'      => 'Tema',
        'theme-info' => 'Personalize a aparência visual do seu CRM',
    ],

    'settings' => [
        'title'          => 'Configurações do Tema',
        'update-success' => 'Configurações do tema atualizadas com sucesso!',
        'save-btn'       => 'Salvar Configurações',

        'activation' => [
            'title'       => 'Ativar Tema Personalizado',
            'description' => 'Habilite ou desabilite o tema personalizado. Quando desativado, o tema padrão do Krayin será utilizado.',
            'is-active'   => 'Tema Ativo',
            'info'        => 'Quando desativado, o tema padrão do Krayin será usado em todo o sistema.',
            'yes'         => 'Sim',
            'no'          => 'Não',
        ],

        'colors' => [
            'title'       => 'Cores do Tema',
            'description' => 'Configure as cores principais do seu tema. Essas cores serão aplicadas em todo o sistema.',

            'primary'      => 'Cor Primária da Marca',
            'primary-help' => 'Cor principal usada em botões, links e destaques',

            'primary-dark'      => 'Cor Primária Escura',
            'primary-dark-help' => 'Variação mais escura da cor primária para estados hover',

            'primary-light'      => 'Cor Primária Clara',
            'primary-light-help' => 'Variação mais clara da cor primária para fundos e bordas',

            'success'      => 'Cor de Sucesso',
            'success-help' => 'Cor usada para mensagens de sucesso e confirmações',

            'warning'      => 'Cor de Alerta',
            'warning-help' => 'Cor usada para avisos e alertas',

            'danger'      => 'Cor de Perigo',
            'danger-help' => 'Cor usada para erros e ações destrutivas',
        ],

        'logos' => [
            'title'       => 'Logos e Favicon',
            'description' => 'Envie seus logos personalizados. Formatos recomendados: SVG, PNG ou ICO.',

            'main'      => 'Logo Principal',
            'main-help' => 'Usado na sidebar. Recomendado: SVG ou PNG transparente (altura ideal: 40-50px)',

            'light'      => 'Logo Claro',
            'light-help' => 'Usado em fundos escuros ou modo dark. Recomendado: SVG ou PNG transparente',

            'icon'      => 'Ícone do Logo',
            'icon-help' => 'Usado quando a sidebar está recolhida. Formato quadrado (32x32px ou 64x64px)',

            'favicon'      => 'Favicon',
            'favicon-help' => 'Ícone exibido na aba do navegador. Recomendado: ICO ou PNG 32x32px',

            'delete-current' => 'Deletar imagem atual',
            'current-image'  => 'Imagem atual',
        ],

        'login' => [
            'title'       => 'Página de Login',
            'description' => 'Personalize a aparência da página de login do sistema.',

            'bg-image'      => 'Imagem de Fundo',
            'bg-image-help' => 'Recomendado: JPG ou PNG com resolução mínima de 1920x1080px',

            'bg-zoom'      => 'Zoom da Imagem de Fundo',
            'bg-zoom-help' => 'Ajuste o zoom da imagem de fundo (100% = tamanho original)',

            'bg-opacity'      => 'Opacidade da Sobreposição',
            'bg-opacity-help' => 'Opacidade da camada escura sobre a imagem de fundo (0% = transparente, 100% = opaco)',

            'show-powered-by'      => 'Mostrar "Powered By"',
            'show-powered-by-help' => 'Exibir o texto "Powered by Krayin" no rodapé da página de login',
        ],

        'login-card' => [
            'section-title' => 'Caixa de Login Customizada',
            'description'   => 'Personalize completamente a aparência da caixa de login. Crie uma experiência única para seus usuários.',

            'enabled'      => 'Habilitar Caixa Customizada',
            'enabled-help' => 'Quando ativado, a caixa de login terá aparência personalizada',

            'bg-image'      => 'Imagem de Fundo do Card',
            'bg-image-help' => 'Imagem de fundo exibida dentro da caixa de login',

            'bg-opacity'      => 'Opacidade da Imagem do Card',
            'bg-opacity-help' => 'Opacidade da imagem de fundo da caixa de login',

            'overlay-color'      => 'Cor da Sobreposição',
            'overlay-color-help' => 'Cor da camada sobre a imagem. Use formato rgba(), ex: rgba(10, 45, 15, 0.78)',

            'welcome-title'      => 'Título de Boas-vindas',
            'welcome-title-help' => 'Texto principal exibido na caixa de login',

            'subtitle'      => 'Subtítulo',
            'subtitle-help' => 'Texto secundário exibido abaixo do título',

            'sparkles'      => 'Efeito de Brilhos',
            'sparkles-help' => 'Adiciona pontos animados de brilho na caixa de login',

            'help-link'      => 'Mostrar Link de Ajuda',
            'help-link-help' => 'Exibe link "Precisa de ajuda?" na caixa de login',

            'support-email'      => 'E-mail de Suporte',
            'support-email-help' => 'E-mail para contato de suporte exibido no link de ajuda',

            'custom-code'      => 'Código HTML/CSS/JavaScript Customizado',
            'custom-code-hint' => 'Cole seu código HTML, CSS e JavaScript personalizado aqui. Este código será injetado diretamente no card de login. Use com cautela!',
        ],

        'empty-states' => [
            'title'       => 'Estados Vazios',
            'description' => 'Substitua as imagens de estado vazio exibidas quando não há dados. Aceita apenas arquivos SVG.',

            'activities'    => 'Atividades',
            'calls'         => 'Chamadas',
            'emails'        => 'E-mails',
            'meetings'      => 'Reuniões',
            'notes'         => 'Notas',
            'organizations' => 'Organizações',
            'persons'       => 'Pessoas',
            'leads'         => 'Leads',
            'products'      => 'Produtos',

            'delete-current' => 'Deletar SVG atual',
            'current-svg'    => 'SVG atual',
            'info'           => 'Recomendado: arquivos SVG otimizados com dimensões entre 200x200px e 400x400px',
        ],
    ],
];
