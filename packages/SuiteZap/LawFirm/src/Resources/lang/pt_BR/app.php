<?php

return [
    // --- 1. SEÇÃO DASHBOARD (Raiz) ---
    'dashboard' => [
        'title' => 'Visão Geral Jurídica',
        'active-processes' => 'Processos Ativos',
        'view-all' => 'Ver Todos',
        'upcoming-hearings' => 'Próximas Audiências (7 dias)',
        'no-hearings' => 'Nenhuma audiência próxima.',
        'new-case' => 'Novo Processo',
    ],

    // --- 3. SEÇÃO PRAZOS ---
    'prazos' => [
        'title' => 'Prazos e Datas Fatais',
        'section-title' => 'Gestão de Prazos',
        'new-btn' => 'Novo Prazo',
        'new-header' => 'Cadastrar Novo Prazo',
        'status' => 'Status',
        'title-table' => 'Título',
        'due-date' => 'Vencimento',
        'type' => 'Tipo',
        'actions' => 'Ações',
        'status-done' => 'Concluído',
        'status-pending' => 'Pendente',
        'fatal' => 'FATAL',
        'common' => 'Comum',
        'overdue' => 'ATRASADO',
        'conclude' => 'Concluir Prazo',
        'delete' => 'Excluir',
        'cancel' => 'Cancelar',
        'save' => 'Salvar',
        'empty' => 'Nenhum prazo cadastrado para este processo.',
        'confirm-delete' => 'Tem certeza que deseja remover este prazo?',
        'confirm-conclude' => 'Deseja marcar este prazo como concluído?',
        'create-success' => 'Prazo criado com sucesso!',
        'conclude-success' => 'Prazo concluído com sucesso!',
        'delete-success' => 'Prazo removido com sucesso!',
        'form' => [
            'title' => 'Título do Prazo',
            'date' => 'Data e Hora Limite',
            'type' => 'Tipo de Prazo',
            'common' => 'Comum',
            'fatal' => 'Fatal / Peremptório',
            'description' => 'Descrição / Observações',
        ],
    ],

    'deadlines' => [
        'title' => 'Prazos',
        'status' => 'Status Real',
        'due_date' => 'Data de Vencimento',
        'name' => 'Nome do Prazo',
    ],

    // --- 2. SEÇÃO PROCESSOS (Raiz) ---
    'processos' => [
        // Títulos de Página e Ações Principais
        'index' => 'Listagem de Processos',
        'title' => 'Processos',
        'create' => 'Novo Processo',
        'create-title' => 'Novo Processo',
        'edit' => 'Editar Processo',
        'edit-title' => 'Editar Processo',
        'view' => 'Visualizar Processo',
        'save' => 'Salvar Processo',
        'save-btn' => 'Salvar Processo',
        'delete' => 'Excluir',
        'cancel' => 'Cancelar',

        // Mensagens de Sucesso/Erro
        'create-success' => 'Processo criado com sucesso!',
        'update-success' => 'Processo atualizado com sucesso!',
        'delete-success' => 'Processo removido com sucesso!',
        'delete-failed' => 'Erro ao remover processo.',

        // Mass Actions
        'mass-delete' => [
            'success' => '{count} processo(s) removido(s) com sucesso!',
            'failed' => 'Erro ao remover processos em massa.',
            'no-selection' => 'Nenhum processo selecionado.',
        ],

        // Chaves usadas no DataGrid (Tabela)
        'datagrid' => [
            'id' => 'ID',
            'titulo' => 'Título',
            'cnj' => 'Número CNJ',
            'data_audiencia' => 'Data da Audiência',
            'status' => 'Status',
            'lead_id' => 'Oportunidade (Lead)',
            'person_id' => 'Cliente (Pessoa)',
        ],

        // Chaves usadas no Formulário (Create/Edit/View)
        'form' => [
            // Títulos de Grupos/Seções
            'group-info' => 'Informações Básicas',
            'group-parts' => 'Partes Envolvidas',
            'group-details' => 'Detalhes Processuais',
            'group-strategy' => 'Gestão e Estratégia',
            'group-dates' => 'Datas e Observações',

            // Campos do Formulário
            'titulo' => 'Título do Processo',
            'cnj' => 'Número CNJ',
            'link' => 'Link do Processo',
            'cliente' => 'Cliente',
            'person' => 'Pessoa Vinculada',
            'lead' => 'Lead Vinculado',
            'tipo_parte' => 'Tipo da Parte',
            'cpf_cnpj' => 'CPF/CNPJ',
            'status' => 'Status',
            'data_distribuicao' => 'Data Distribuição',
            'valor' => 'Valor da Causa',
            'valor_causa' => 'Valor da Causa',
            'area' => 'Área do Direito',
            'area_direito' => 'Área do Direito',
            'subarea' => 'Sub-área',
            'fase' => 'Fase Processual',
            'fase_processual' => 'Fase Processual',
            'data_audiencia' => 'Data da Audiência',
            'link_audiencia' => 'Link da Audiência',
            'vara' => 'Vara',
            'vara_forum' => 'Vara/Fórum',
            'tribunal' => 'Tribunal/Fórum',
            'comarca' => 'Comarca',
            'reu_parte_contraria' => 'Réu / Parte Contrária',
            'adversary' => 'Parte Contrária',
            'advogado_contrario' => 'Advogado Contrário',
            'advogado_adversary' => 'Advogado da Parte Contrária',
            'email' => 'E-mail (Adv. Contrário)', // Legacy check
            'email_advogado' => 'E-mail (Adv. Contrário)', // Correct key
            'oab' => 'OAB (Adv. Contrário)',
            'whatsapp' => 'WhatsApp (Adv. Contrário)',
            'observacoes' => 'Observações',
            'desc' => 'Descrição / Observações',
            'probabilidade' => 'Probabilidade de Êxito',

            // Placeholders
            'select-choose' => 'Selecione...',
            'select-lead' => 'Selecione um Lead...',
            'select-person' => 'Selecione um Cliente...',
            'search-client' => 'Buscar Cliente...',
            'search-lead' => 'Buscar Lead de Origem...',
            'placeholder-cpf' => 'Informe o documento',
            'placeholder-vara' => 'Ex: 1ª Vara Cível',
            'placeholder-subarea' => 'Ex: Contratos, Divórcio...',
        ],

        // Valores de Status (Enums)
        'status-options' => [
            'active' => 'Ativo',
            'suspended' => 'Suspenso',
            'archived' => 'Arquivado',
            'encerrado' => 'Encerrado',
            // Aliases em português
            'ativo' => 'Ativo',
            'suspenso' => 'Suspenso',
            'arquivado' => 'Arquivado',
        ],

        // Tipos de Parte
        'party-type' => [
            'individual' => 'Pessoa Física',
            'company' => 'Pessoa Jurídica',
        ],

        // Áreas do Direito
        'areas' => [
            'civil' => 'Cível',
            'labor' => 'Trabalhista',
            'criminal' => 'Criminal/Penal',
            'tax' => 'Tributário',
            'family' => 'Família',
            'consumer' => 'Consumidor',
            'social-security' => 'Previdenciário',
            // Aliases em português
            'civel' => 'Cível',
            'trabalhista' => 'Trabalhista',
            'familia' => 'Família',
            'tributario' => 'Tributário',
            'previdenciario' => 'Previdenciário',
        ],

        // Fases Processuais
        'phases' => [
            'initial' => 'Petição Inicial',
            'answer' => 'Contestação',
            'reply' => 'Réplica',
            'instruction' => 'Instrução e Julgamento',
            'sentence' => 'Sentença',
            'appeal' => 'Fase Recursal',
            'execution' => 'Execução / Cumprimento',
            // Aliases em português
            'inicial' => 'Petição Inicial',
            'contestacao' => 'Contestação',
            'replica' => 'Réplica',
            'instrucao' => 'Instrução e Julgamento',
            'sentenca' => 'Sentença',
            'recurso' => 'Fase Recursal',
            'execucao' => 'Execução / Cumprimento',
        ],

        // Probabilidade de Êxito
        'probability' => [
            'very-low' => 'Muito Baixa',
            'low' => 'Baixa',
            'medium' => 'Média',
            'high' => 'Alta',
            'very-high' => 'Muito Alta',
        ],
    ],
    // --- 4. ACL / Permissões ---
    'acl' => [
        'lawfirm' => 'Advocacia (Módulo)',
        'processos' => 'Processos',
        'financial' => 'Financeiro',
        'prazos' => 'Gestão de Prazos',
    ],
];
