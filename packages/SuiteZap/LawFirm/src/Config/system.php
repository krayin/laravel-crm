<?php

return [
    [
        'key' => 'lawfirm',
        'name' => 'Jurídico',
        'info' => 'Configurações do módulo Jurídico',
        'sort' => 5,
    ],
    [
        'key' => 'lawfirm.settings',
        'name' => 'Personalização',
        'info' => 'Personalize a identidade visual e dados de contato',
        'sort' => 1,
        'icon' => 'icon-setting',
        'icon-class' => 'icon-setting',
    ],
    [
        'key' => 'lawfirm.settings.general',
        'name' => 'Identidade & Qualificação',
        'info' => 'Defina o nome, logo e rodapé dos documentos',
        'sort' => 1,
        'fields' => [
            // --- BLOCO 1: Identificação (Híbrido PF/PJ) ---
            [
                'name' => 'company_name',
                'title' => 'Nome do Escritório ou Advogado(a)',
                'type' => 'text',
                'validation' => 'required', // Obrigatório
                'channel_based' => true,
                'info' => 'Nome que aparecerá no cabeçalho dos documentos.',
            ],
            [
                'name' => 'document_id',
                'title' => 'CPF ou CNPJ',
                'type' => 'text',
                'validation' => 'required', // Vital para contratos
                'channel_based' => true,
                'info' => 'Documento fiscal para qualificação em contratos.',
            ],
            [
                'name' => 'oab_number',
                'title' => 'Registro OAB',
                'type' => 'text',
                'channel_based' => true,
                'info' => 'Ex: OAB/SP 123.456',
            ],
            [
                'name' => 'logo',
                'title' => 'Logo (Cabeçalho)',
                'type' => 'image',
                'validation' => 'mimes:jpeg,bmp,png,jpg',
                'channel_based' => true,
            ],

            // --- BLOCO 2: Contatos (Validados) ---
            [
                'name' => 'contact_whatsapp', // MANTIDO 'contact_whatsapp' para compatibilidade
                'title' => 'WhatsApp / Contato Principal',
                'type' => 'text',
                'validation' => 'required', // Obrigatório
                'channel_based' => true,
                'info' => 'Aparecerá no rodapé dos recibos.',
            ],
            [
                'name' => 'contact_email',
                'title' => 'E-mail Profissional',
                'type' => 'text',
                'validation' => 'required|email', // Validação estrita de e-mail
                'channel_based' => true,
            ],
            [
                'name' => 'website',
                'title' => 'Site / Redes Sociais',
                'type' => 'text',
                'channel_based' => true,
            ],

            // --- BLOCO 3: Endereço ---
            [
                'name' => 'address',
                'title' => 'Endereço Completo',
                'type' => 'textarea',
                'validation' => 'required', // Obrigatório para contratos
                'channel_based' => true,
                'info' => 'Rua, Número, Bairro, Cidade - UF, CEP.',
            ],
            [
                'name' => 'city',
                'title' => 'Cidade (para Data de Documentos)',
                'type' => 'text',
                'channel_based' => true,
                'info' => 'Ex: São Paulo. Usada na data de procurações e contratos.',
            ],
        ],
    ],
];
