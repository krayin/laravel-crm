<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('theme-manager::app.settings.title')
    </x-slot>

    {{-- ====================================================================
         TOOLTIP COMPONENT - Informação contextual para campos
         Movido para @pushOnce para garantir carregamento correto
         ==================================================================== --}}
    @pushOnce('styles')
    <style>
    /* Container do tooltip - DEVE ter position: relative */
    .theme-tooltip {
        position: relative !important;
        display: inline-flex !important;
        align-items: center;
        margin-left: 0.5rem;
        cursor: help;
    }

    /* Ícone - visível sempre */
    .theme-tooltip-icon {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #3B82F6;
        color: white;
        display: flex !important;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: bold;
        transition: all 0.2s;
        z-index: 1;
    }

    .theme-tooltip:hover .theme-tooltip-icon {
        background: #2563EB;
        transform: scale(1.1);
    }

    /* Content - ESCONDIDO por padrão */
    .theme-tooltip-content {
        display: none !important;
        position: absolute !important;
        left: 50%;
        bottom: calc(100% + 8px);
        transform: translateX(-50%);
        background: #1f2937;
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        line-height: 1.5;
        max-width: 280px;
        white-space: normal;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        z-index: 9999 !important;
        pointer-events: none;
    }

    /* Content - VISÍVEL no hover */
    .theme-tooltip:hover .theme-tooltip-content {
        display: block !important;
        opacity: 1;
        visibility: visible;
    }

    /* Seta do tooltip */
    .theme-tooltip-content::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 6px solid transparent;
        border-top-color: #1f2937;
    }

    /* Dark mode */
    .dark .theme-tooltip-content {
        background: #374151;
        border: 1px solid #4B5563;
    }

    .dark .theme-tooltip-content::after {
        border-top-color: #374151;
    }

    /* Ajuste para tooltips que ficam na borda direita */
    .theme-tooltip-right .theme-tooltip-content {
        left: auto;
        right: 0;
        transform: none;
    }
    .theme-tooltip-right .theme-tooltip-content::after {
        left: auto;
        right: 10px;
        transform: none;
    }

    /* Mobile */
    @media (max-width: 640px) {
        .theme-tooltip-content {
            position: fixed !important;
            left: 1rem !important;
            right: 1rem !important;
            bottom: auto !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            max-width: none;
            width: auto;
        }
        .theme-tooltip-content::after {
            display: none;
        }
    }

    /* ================================================================
       THEME CARDS - Seleção de tema
       ================================================================ */
    .theme-card-label {
        cursor: pointer;
        user-select: none;
        display: block;
        outline: none;
        border-radius: 8px;
    }

    /* Focus ring para A11y (navegação via Tab) */
    .theme-card-label:focus {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }

    .theme-card-label:focus-visible {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }

    .theme-card-label .theme-card {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    /* Highlight quando selecionado via peer-checked (Tailwind) */
    .theme-card-label input[type="radio"]:checked + .theme-card {
        border-color: #2563EB !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2) !important;
        background-color: rgba(239, 246, 255, 0.5) !important;
    }

    .dark .theme-card-label input[type="radio"]:checked + .theme-card {
        background-color: rgba(30, 58, 138, 0.2) !important;
    }

    /* ================================================================
       THEME PREVIEW - Thumbnail no topo do card
       ================================================================ */
    .theme-preview {
        width: 100%;
        height: 96px;
        overflow: hidden;
        border-bottom: 1px solid #e5e7eb;
    }

    .dark .theme-preview {
        border-bottom-color: #374151;
    }

    .theme-preview img,
    .theme-preview .theme-preview-gradient {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* ================================================================
       THEME COLORS GRID - 3x2
       ================================================================ */
    .theme-colors-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 6px;
        justify-items: center;
        margin-bottom: 12px;
    }

    .theme-color-circle {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #e5e7eb;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .dark .theme-color-circle {
        border-color: #374151;
    }

    /* ================================================================
       ANIMAÇÃO PULSE PARA BADGE ATIVO
       ================================================================ */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    /* ================================================================
       ANIMAÇÃO SPIN PARA LOADING
       ================================================================ */
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* ================================================================
       LIVE PREVIEW - Mini UI preview on hover
       ================================================================ */
    .kr-live-preview {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 15;
        pointer-events: none;
        opacity: 0;
        transform: scale(0.96);
        transition: opacity 0.25s ease, transform 0.25s ease;
        border-radius: 8px;
        overflow: hidden;
        background: #ffffff;
    }

    .dark .kr-live-preview {
        background: #1f2937;
    }

    /* Show on hover/focus of card wrapper */
    .theme-card-wrapper:hover .kr-live-preview,
    .theme-card-wrapper:focus-within .kr-live-preview,
    .theme-card-wrapper.kr-focused .kr-live-preview {
        opacity: 1;
        transform: scale(1);
    }

    /* Mini UI Layout */
    .kr-live-header {
        height: 14px;
        background: var(--p, #1E40AF);
        display: flex;
        align-items: center;
        padding: 0 4px;
        gap: 2px;
    }

    .kr-live-header-dot {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
    }

    .kr-live-body {
        display: flex;
        height: calc(100% - 14px - 20px);
    }

    .kr-live-sidebar {
        width: 18px;
        background: var(--p, #1E40AF);
        opacity: 0.15;
    }

    .kr-live-content {
        flex: 1;
        padding: 4px;
        display: flex;
        flex-direction: column;
        gap: 3px;
        background: #f9fafb;
    }

    .dark .kr-live-content {
        background: #111827;
    }

    .kr-live-card {
        flex: 1;
        background: white;
        border-radius: 2px;
        border-left: 2px solid var(--p, #1E40AF);
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .dark .kr-live-card {
        background: #1f2937;
    }

    .kr-live-footer {
        height: 20px;
        padding: 3px 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background: #f3f4f6;
        border-top: 1px solid #e5e7eb;
    }

    .dark .kr-live-footer {
        background: #111827;
        border-top-color: #374151;
    }

    .kr-live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        border: 1px solid rgba(0,0,0,0.1);
        transition: transform 0.15s ease;
    }

    .theme-card-wrapper:hover .kr-live-dot {
        transform: scale(1.15);
    }

    .kr-live-dot--p { background: var(--p, #1E40AF); }
    .kr-live-dot--s { background: var(--s, #10B981); }
    .kr-live-dot--w { background: var(--w, #F59E0B); }
    .kr-live-dot--d { background: var(--d, #EF4444); }

    /* ================================================================
       POINTER DnD - Handle e estados de arraste
       Usando Pointer Events (mais confiável que HTML5 DnD)
       ================================================================ */

    /* Card wrapper precisa position relative para o handle */
    .theme-card-wrapper {
        position: relative;
    }

    .kr-drag-handle {
        position: absolute;
        top: 4px;
        left: 4px;
        z-index: 9999;
        width: 20px;
        height: 20px;
        padding: 0;
        border: none;
        border-radius: 4px;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        font-size: 12px;
        font-weight: bold;
        cursor: grab;
        opacity: 0;
        transition: opacity 0.15s ease, background 0.15s ease, transform 0.1s ease;
        /* Bloquear drag nativo HTML5 */
        -webkit-user-drag: none;
        -moz-user-drag: none;
        user-drag: none;
        user-select: none;
        -webkit-user-select: none;
        touch-action: none;
        pointer-events: auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Mostrar handle no hover e focus (acessibilidade) */
    .theme-card-wrapper:hover .kr-drag-handle,
    .theme-card-wrapper:focus-within .kr-drag-handle,
    .kr-drag-handle:focus {
        opacity: 1;
    }

    .kr-drag-handle:hover {
        background: rgba(37, 99, 235, 0.9);
        transform: scale(1.1);
    }

    .kr-drag-handle:active,
    .kr-drag-handle.kr-dragging {
        cursor: grabbing;
        background: rgba(37, 99, 235, 1);
    }

    /* Card sendo arrastado (original fica semitransparente) */
    .theme-card-wrapper.kr-dragging {
        opacity: 0.4;
        transform: scale(0.98);
        pointer-events: none;
    }

    /* Indicador de drop zone */
    .theme-card-wrapper.kr-drag-over {
        outline: 2px dashed #3b82f6;
        outline-offset: 2px;
        background: rgba(59, 130, 246, 0.05);
    }

    /* Ghost element (clone durante arrasto) */
    .kr-drag-ghost {
        position: fixed;
        pointer-events: none;
        z-index: 2147483647;
        opacity: 0.85;
        transform: rotate(2deg) scale(1.02);
        box-shadow: 0 12px 40px rgba(0,0,0,0.3);
        border-radius: 8px;
        transition: none;
    }

    /* Grid durante drag */
    #themeCardsGrid.kr-drag-active {
        min-height: 100px;
    }

    /* Cursor global durante drag */
    body.kr-dragging-active {
        cursor: grabbing !important;
    }
    body.kr-dragging-active * {
        cursor: grabbing !important;
    }

    .dark .kr-drag-handle {
        background: rgba(255, 255, 255, 0.4);
    }

    .dark .kr-drag-handle:hover {
        background: rgba(59, 130, 246, 0.9);
    }

    /* ═══════════════════════════════════════════════════════════
       BOTÕES DOS CARDS - Preview e Selecionar
       Usa fallbacks inline para ser overridable pelo BrandKit
       BrandKit pode definir --kr-preview-* em qualquer lugar
       ═══════════════════════════════════════════════════════════ */
    .kr-btn-preview {
        flex: 1;
        padding: 4px 6px;
        border-radius: 3px;
        font-size: 8px;
        font-weight: 600;
        background: var(--kr-preview-bg, #f3f4f6);
        color: var(--kr-preview-fg, #6b7280);
        border: none;
        cursor: pointer;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 3px;
    }

    .kr-btn-preview:hover {
        background: var(--kr-preview-hover-bg, rgba(55, 65, 81, 0.12));
        color: var(--kr-preview-hover-fg, #1f2937);
    }

    .kr-btn-preview:focus-visible {
        outline: 2px solid currentColor;
        outline-offset: 2px;
    }

    .kr-btn-preview:active {
        transform: scale(0.97);
    }

    /* Dark mode defaults (BrandKit pode sobrescrever) */
    .dark .kr-btn-preview {
        background: var(--kr-preview-bg, #374151);
        color: var(--kr-preview-fg, #9ca3af);
    }

    .dark .kr-btn-preview:hover {
        background: var(--kr-preview-hover-bg, rgba(255, 255, 255, 0.15));
        color: var(--kr-preview-hover-fg, #f3f4f6);
    }

    /* ================================================================
       LAYOUT MELHORADO - Grid, Cards, Modal (Janeiro 2026)
       Polimento v2: menos !important, dark mode completo, hover/focus refinado
       ================================================================ */

    /* Variáveis neutras para layout */
    :root {
        --kr-card-radius: 14px;
        --kr-gap: 16px;
        --kr-card-bg: #ffffff;
        --kr-card-border: rgba(0,0,0,.08);
        --kr-card-shadow: 0 4px 20px rgba(0,0,0,.06);
        --kr-card-hover-shadow: 0 10px 30px rgba(0,0,0,.10);
        --kr-card-hover-border: rgba(0,0,0,.12);

        --kr-muted: #6b7280;
        --kr-text: #111827;

        --kr-badge-bg: rgba(16,185,129,.12);
        --kr-badge-fg: #059669;

        --kr-focus: rgba(99,102,241,.55);
        --kr-selected-border: #2563eb;
        --kr-selected-shadow: rgba(37,99,235,.18);
    }

    .dark {
        --kr-card-bg: #1f2937;
        --kr-card-border: rgba(255,255,255,.10);
        --kr-card-shadow: 0 4px 20px rgba(0,0,0,.30);
        --kr-card-hover-shadow: 0 10px 30px rgba(0,0,0,.40);
        --kr-card-hover-border: rgba(255,255,255,.18);

        --kr-muted: #9ca3af;
        --kr-text: #f3f4f6;

        --kr-badge-bg: rgba(16,185,129,.18);
        --kr-badge-fg: #34d399;

        --kr-focus: rgba(165,180,252,.65);
        --kr-selected-shadow: rgba(59,130,246,.25);
    }

    /* ─────────────────────────────────────────────────────────────
       GRID - responsivo, alinhado (mantém !important para override de Tailwind)
       ───────────────────────────────────────────────────────────── */
    #themeCardsGrid {
        display: grid !important;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;
        gap: var(--kr-gap) !important;
        align-items: stretch !important;
    }

    @media (min-width: 1280px) {
        #themeCardsGrid {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)) !important;
        }
    }

    /* ─────────────────────────────────────────────────────────────
       CARD WRAPPER - altura completa, sem !important desnecessário
       ───────────────────────────────────────────────────────────── */
    #themeCardsGrid .theme-card-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
        border-radius: var(--kr-card-radius);
    }

    /* ─────────────────────────────────────────────────────────────
       CARD LABEL (clicável) - focus ring e cursor
       ───────────────────────────────────────────────────────────── */
    #themeCardsGrid .theme-card-label {
        cursor: pointer;
        outline: none;
        border-radius: var(--kr-card-radius);
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    /* Focus ring no label (ao navegar com Tab) */
    #themeCardsGrid .theme-card-label:focus-visible .theme-card {
        box-shadow: 0 0 0 3px var(--kr-focus), var(--kr-card-shadow);
    }

    /* ─────────────────────────────────────────────────────────────
       CARD - área visual principal
       ───────────────────────────────────────────────────────────── */
    #themeCardsGrid .theme-card {
        border-radius: var(--kr-card-radius);
        background: var(--kr-card-bg);
        border: 1px solid var(--kr-card-border);
        box-shadow: var(--kr-card-shadow);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    }

    /* Hover eleva levemente */
    #themeCardsGrid .theme-card-label:hover .theme-card {
        transform: translateY(-2px);
        box-shadow: var(--kr-card-hover-shadow);
        border-color: var(--kr-card-hover-border);
    }

    /* Card selecionado (tema atual) - via classe no .theme-card */
    #themeCardsGrid .theme-card.kr-selected {
        border: 2px solid var(--kr-selected-border);
        box-shadow: 0 4px 20px var(--kr-selected-shadow);
    }

    /* Card selecionado - via classe no wrapper (mais específico, halo duplo) */
    #themeCardsGrid .theme-card-wrapper.kr-is-selected .theme-card {
        border-color: var(--kr-selected-border);
        box-shadow: 0 0 0 1px var(--kr-selected-border), 0 8px 26px var(--kr-selected-shadow);
    }

    /* Hover no selecionado: manter destaque mas ainda ter feedback */
    #themeCardsGrid .theme-card-wrapper.kr-is-selected .theme-card-label:hover .theme-card {
        transform: translateY(-1px);
        box-shadow: 0 0 0 1px var(--kr-selected-border), 0 12px 32px var(--kr-selected-shadow);
    }

    /* ─────────────────────────────────────────────────────────────
       THUMBNAIL - 16:9 fixa, encaixa no card
       ───────────────────────────────────────────────────────────── */
    .kr-theme-thumb {
        aspect-ratio: 16 / 9;
        width: 100%;
        overflow: hidden;
        background: rgba(0,0,0,.04);
        position: relative;
    }
    .dark .kr-theme-thumb {
        background: rgba(255,255,255,.06);
    }
    .kr-theme-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* ─────────────────────────────────────────────────────────────
       CARD BODY - conteúdo com flex
       ───────────────────────────────────────────────────────────── */
    .kr-theme-card-body {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 14px;
        flex: 1;
    }

    /* ─────────────────────────────────────────────────────────────
       HEADER - nome + badge (ellipsis para nomes longos)
       ───────────────────────────────────────────────────────────── */
    .kr-theme-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }

    .kr-theme-title {
        font-size: 15px;
        font-weight: 650;
        color: var(--kr-text);
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .kr-theme-sub {
        font-size: 12px;
        color: var(--kr-muted);
        margin-top: 3px;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .kr-theme-badge {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 999px;
        background: var(--kr-badge-bg);
        color: var(--kr-badge-fg);
        white-space: nowrap;
        font-weight: 600;
        flex-shrink: 0;
    }

    /* ─────────────────────────────────────────────────────────────
       CORES - dots premium com borda sutil
       ───────────────────────────────────────────────────────────── */
    .kr-theme-colors {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: auto;
        padding-top: 4px;
    }

    .kr-theme-color-dot {
        width: 16px;
        height: 16px;
        border-radius: 999px;
        box-shadow: 0 1px 3px rgba(0,0,0,.18);
        border: 1px solid rgba(0,0,0,.08);
        transition: transform .1s ease;
    }
    .dark .kr-theme-color-dot {
        border-color: rgba(255,255,255,.12);
    }

    .kr-theme-color-dot:hover {
        transform: scale(1.15);
    }

    /* ─────────────────────────────────────────────────────────────
       AÇÕES - sempre no rodapé
       ───────────────────────────────────────────────────────────── */
    #themeCardsGrid .theme-card-actions {
        margin-top: auto;
        display: flex;
        gap: 8px;
        padding: 0 14px 14px;
        background: transparent;
    }

    /* Botões com proporção (especificidade via ID para evitar !important) */
    #themeCardsGrid .kr-btn-preview {
        flex: 1;
        padding: 10px 12px;
        font-size: 13px;
        border-radius: 8px;
    }

    #themeCardsGrid .kr-btn-select {
        flex: 2;
        padding: 10px 12px;
        font-size: 13px;
        border-radius: 8px;
    }

    /* Botão selecionado fica visível */
    #themeCardsGrid .kr-btn-select[disabled] {
        opacity: 1;
        cursor: default;
    }

    /* ─────────────────────────────────────────────────────────────
       MODAL - imagem grande e centralizada
       ───────────────────────────────────────────────────────────── */
    #krThemePreviewPanel {
        max-width: min(1280px, 96vw);
    }
    #krThemePreviewImg {
        max-width: 100%;
        max-height: 78vh;
        object-fit: contain;
        border-radius: 12px;
    }

    /* ─────────────────────────────────────────────────────────────
       LIVE PREVIEW + DRAG HANDLE - ajustes para novo layout
       ───────────────────────────────────────────────────────────── */
    .kr-live-preview {
        border-radius: var(--kr-card-radius);
    }

    .kr-drag-handle {
        border-radius: 6px;
        z-index: 30; /* acima do card mas abaixo do modal */
    }

    /* ─────────────────────────────────────────────────────────────
       DENSIDADE DE GRID - breakpoints para diferentes telas
       ───────────────────────────────────────────────────────────── */

    /* Desktop médio (1024px+): cards um pouco maiores */
    @media (min-width: 1024px) {
        #themeCardsGrid {
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)) !important;
        }
    }

    /* Desktop grande (1536px+): cards maiores ainda */
    @media (min-width: 1536px) {
        #themeCardsGrid {
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)) !important;
        }
    }

    /* ─────────────────────────────────────────────────────────────
       COMPACT MODE - densidade alta para ver mais temas
       Ativado via body.kr-theme-compact (toggle JS)
       ───────────────────────────────────────────────────────────── */
    body.kr-theme-compact #themeCardsGrid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)) !important;
        gap: 12px !important;
    }

    body.kr-theme-compact .kr-theme-card-body {
        padding: 10px;
        gap: 8px;
    }

    body.kr-theme-compact .kr-theme-thumb {
        aspect-ratio: 16 / 8; /* mais raso */
    }

    body.kr-theme-compact .kr-theme-title {
        font-size: 13px;
    }

    body.kr-theme-compact .kr-theme-sub {
        font-size: 11px;
    }

    body.kr-theme-compact .kr-theme-color-dot {
        width: 12px;
        height: 12px;
    }

    body.kr-theme-compact .theme-card-actions {
        padding: 0 10px 10px;
        gap: 6px;
    }

    body.kr-theme-compact .kr-btn-preview,
    body.kr-theme-compact .kr-btn-select {
        padding: 8px 10px;
        font-size: 12px;
    }

    /* ─────────────────────────────────────────────────────────────
       TOGGLE COMPACT - botão flutuante
       ───────────────────────────────────────────────────────────── */
    .kr-density-toggle {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid var(--kr-card-border);
        background: var(--kr-card-bg);
        color: var(--kr-muted);
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .kr-density-toggle:hover {
        background: var(--kr-card-hover-border);
        color: var(--kr-text);
    }

    .kr-density-toggle.kr-active {
        background: var(--kr-selected-border);
        color: #fff;
        border-color: var(--kr-selected-border);
    }
    </style>
    @endPushOnce

    <x-admin::form
        method="POST"
        enctype="multipart/form-data"
        :action="route('admin.settings.theme.update')"
    >
        <div class="flex flex-col gap-4">
            <!-- Page Header -->
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <!-- Breadcrumbs -->
                    <x-admin::breadcrumbs name="settings" />

                    <div class="text-xl font-bold dark:text-white">
                        🎨 @lang('theme-manager::app.settings.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <!-- Save Button -->
                    <button
                        type="submit"
                        class="primary-button"
                    >
                        @lang('theme-manager::app.settings.save-btn')
                    </button>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex gap-2.5 max-xl:flex-wrap">
                <!-- Left Column - Main Settings -->
                <div class="flex flex-1 flex-col gap-2.5 max-xl:flex-auto">

                    <!-- ═══════════════════════════════════════════════════════════ -->
                    <!-- SEÇÃO 1: ATIVAÇÃO E TEMA BASE -->
                    <!-- ═══════════════════════════════════════════════════════════ -->
                    <div class="box-shadow rounded-lg border-2 border-blue-200 bg-gradient-to-br from-blue-50 to-white p-6 dark:border-blue-800 dark:from-gray-900 dark:to-gray-800 mb-6">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="text-3xl">🎨</span>
                            <div>
                                <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                                    Ativação e Tema Base
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Ative o sistema de temas e escolha um tema pré-configurado
                                </p>
                            </div>
                        </div>

                    <!-- SUB-SEÇÃO: ATIVAÇÃO DO TEMA -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('theme-manager::app.settings.activation.title')
                        </p>

                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                            @lang('theme-manager::app.settings.activation.description')
                        </p>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('theme-manager::app.settings.activation.is-active')
                                <span class="theme-tooltip">
                                    <span class="theme-tooltip-icon">i</span>
                                    <span class="theme-tooltip-content">Quando ativado, todas as customizações visuais serão aplicadas ao sistema. Desative para voltar ao tema padrão do Krayin.</span>
                                </span>
                            </x-admin::form.control-group.label>

                            <select
                                name="is_active"
                                class="block w-full rounded-md border border-gray-200 px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-blue-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-blue-400"
                            >
                                <option value="0" {{ old('is_active', $config->is_active) == 0 ? 'selected' : '' }}>
                                    @lang('theme-manager::app.settings.activation.no')
                                </option>
                                <option value="1" {{ old('is_active', $config->is_active) == 1 ? 'selected' : '' }}>
                                    @lang('theme-manager::app.settings.activation.yes')
                                </option>
                            </select>

                            <x-admin::form.control-group.error control-name="is_active" />
                        </x-admin::form.control-group>

                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            @lang('theme-manager::app.settings.activation.info')
                        </p>
                    </div>

                    <!-- SEÇÃO 1.5 - SELEÇÃO DE TEMA PREDEFINIDO -->
                    @if(!empty($availableThemes))
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        {{-- Header com título e toggle de densidade --}}
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                            <div>
                                <p class="text-base font-semibold text-gray-800 dark:text-white" style="margin-bottom:4px;">
                                    Tema Predefinido
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300" style="margin:0;">
                                    Escolha um tema base. Clique no card para preview, use os botões para selecionar.
                                </p>
                            </div>

                            {{-- Toggle Compact/Normal --}}
                            <button type="button"
                                    id="krDensityToggle"
                                    class="kr-density-toggle"
                                    onclick="window.krToggleDensity(); return false;"
                                    title="Alternar entre visualização normal e compacta">
                                <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                <span id="krDensityLabel">Compacto</span>
                            </button>
                        </div>

                        <!-- Grid de Cards Premium (Layout responsivo via CSS) -->
                        <div id="themeCardsGrid" class="mb-4">
                            @foreach($availableThemes as $theme)
                                @php
                                    $themeSlug = $theme['slug'] ?? 'default';
                                    $isSelected = old('selected_theme', $config->selected_theme ?? 'default') === $themeSlug;
                                    $colorPrimary = $theme['colors']['primary'] ?? $theme['color_primary'] ?? '#1E40AF';
                                    $colorPrimaryDark = $theme['colors']['primary_dark'] ?? $theme['color_primary_dark'] ?? '#1E3A8A';
                                    $colorPrimaryLight = $theme['colors']['primary_light'] ?? $theme['color_primary_light'] ?? '#3B82F6';
                                    $colorSuccess = $theme['colors']['success'] ?? $theme['color_success'] ?? '#10B981';
                                    $colorWarning = $theme['colors']['warning'] ?? $theme['color_warning'] ?? '#F59E0B';
                                    $colorDanger = $theme['colors']['danger'] ?? $theme['color_danger'] ?? '#EF4444';
                                    // Preferir preview_url do controller, senão calcular localmente
                                    $previewUrl = $theme['preview_url'] ?? (!empty($theme['preview']) ? asset('storage/themes/'.$themeSlug.'/'.$theme['preview']) : '');
                                @endphp

                                {{-- Radio FORA do label (fonte única de verdade) --}}
                                <input type="radio"
                                       name="selected_theme"
                                       id="theme_{{ $themeSlug }}"
                                       value="{{ $themeSlug }}"
                                       class="sr-only theme-radio"
                                       data-theme-name="{{ $theme['name'] }}"
                                       data-theme-slug="{{ $themeSlug }}"
                                       {{ $isSelected ? 'checked' : '' }}>

                                {{-- CARD WRAPPER (contém label + botões) --}}
                                <div class="theme-card-wrapper {{ $isSelected ? 'kr-is-selected' : '' }}"
                                     id="theme-card-{{ $themeSlug }}"
                                     style="position:relative;"
                                     draggable="false"
                                     data-theme-slug="{{ $themeSlug }}"
                                     data-theme-name="{{ $theme['name'] ?? $themeSlug }}"
                                     data-primary="{{ $colorPrimary }}"
                                     data-success="{{ $colorSuccess }}"
                                     data-warning="{{ $colorWarning }}"
                                     data-danger="{{ $colorDanger }}">

                                    {{-- DRAG HANDLE - Arraste para reordenar (div para evitar bloqueios de DnD) --}}
                                    <div class="kr-drag-handle"
                                         role="button"
                                         tabindex="0"
                                         draggable="false"
                                         aria-label="Arrastar para reordenar"
                                         title="Arrastar para reordenar">≡</div>

                                    {{-- LIVE PREVIEW - Mini UI que aparece no hover --}}
                                    <div class="kr-live-preview" aria-hidden="true">
                                        <div class="kr-live-header">
                                            <span class="kr-live-header-dot"></span>
                                            <span class="kr-live-header-dot"></span>
                                            <span class="kr-live-header-dot"></span>
                                        </div>
                                        <div class="kr-live-body">
                                            <div class="kr-live-sidebar"></div>
                                            <div class="kr-live-content">
                                                <div class="kr-live-card"></div>
                                                <div class="kr-live-card"></div>
                                            </div>
                                        </div>
                                        <div class="kr-live-footer">
                                            <span class="kr-live-dot kr-live-dot--p" title="Primary"></span>
                                            <span class="kr-live-dot kr-live-dot--s" title="Success"></span>
                                            <span class="kr-live-dot kr-live-dot--w" title="Warning"></span>
                                            <span class="kr-live-dot kr-live-dot--d" title="Danger"></span>
                                        </div>
                                    </div>

                                    {{-- DIV clicável que abre PREVIEW (não seleciona) --}}
                                    <div class="theme-card-label"
                                         role="button"
                                         tabindex="0"
                                         aria-label="Abrir preview do tema {{ $theme['name'] ?? $themeSlug }}"
                                         data-theme-slug="{{ $themeSlug }}"
                                         data-theme-name="{{ $theme['name'] ?? $themeSlug }}"
                                         data-radio-id="theme_{{ $themeSlug }}"
                                         data-preview-url="{{ asset('storage/themes/' . $themeSlug . '/' . ($theme['preview'] ?? 'preview.png')) }}"
                                         data-primary="{{ $colorPrimary }}"
                                         data-success="{{ $colorSuccess }}"
                                         data-warning="{{ $colorWarning }}"
                                         data-danger="{{ $colorDanger }}"
                                         onclick="event.preventDefault(); event.stopPropagation(); window.krOpenThemePreview(this); return false;"
                                         onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault(); window.krOpenThemePreview(this);}">

                                        {{-- CARD CONTAINER --}}
                                        <div class="theme-card {{ $isSelected ? 'kr-selected' : '' }}"
                                             data-theme="{{ $themeSlug }}">

                                            {{-- THUMBNAIL 16:9 --}}
                                            <div class="kr-theme-thumb">
                                                @if(!empty($theme['preview']))
                                                    <img src="{{ asset('storage/themes/' . $themeSlug . '/' . $theme['preview']) }}"
                                                         alt="Preview de {{ $theme['name'] ?? $themeSlug }}"
                                                         loading="lazy">
                                                @else
                                                    {{-- Gradiente fallback --}}
                                                    <div style="position:absolute; inset:0; background:linear-gradient(135deg, {{ $colorPrimary }} 0%, {{ $colorPrimaryDark ?? $colorPrimary }} 50%, {{ $colorSuccess }} 100%);">
                                                        {{-- Padrão decorativo --}}
                                                        <div style="position:absolute; inset:0; background-image:radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px); background-size:12px 12px;"></div>
                                                        {{-- Nome centralizado --}}
                                                        <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center;">
                                                            <div style="backdrop-filter:blur(4px); background:rgba(0,0,0,0.2); border-radius:8px; padding:8px 16px;">
                                                                <div style="color:white; font-size:14px; font-weight:700; text-shadow:0 2px 4px rgba(0,0,0,0.3);">
                                                                    {{ $theme['name'] }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- CARD BODY --}}
                                            <div class="kr-theme-card-body">
                                                {{-- Header: Título + Badge --}}
                                                <div class="kr-theme-card-header">
                                                    <div style="min-width:0; flex:1;">
                                                        <div class="kr-theme-title" title="{{ $theme['name'] }} v{{ $theme['version'] ?? '1.0' }}">
                                                            {{ $theme['name'] }}
                                                        </div>
                                                        <div class="kr-theme-sub">
                                                            <span>v{{ $theme['version'] ?? '1.0' }}</span>
                                                            <span style="opacity:0.5;">•</span>
                                                            <span>{{ $themeSlug }}</span>
                                                        </div>
                                                    </div>

                                                    @if($isSelected)
                                                        <span class="kr-theme-badge">✓ Tema Atual</span>
                                                    @endif
                                                </div>

                                                {{-- Cores do tema --}}
                                                <div class="kr-theme-colors">
                                                    <span class="kr-theme-color-dot" style="background:{{ $colorPrimary }};" title="Primary"></span>
                                                    <span class="kr-theme-color-dot" style="background:{{ $colorSuccess }};" title="Success"></span>
                                                    <span class="kr-theme-color-dot" style="background:{{ $colorWarning }};" title="Warning"></span>
                                                    <span class="kr-theme-color-dot" style="background:{{ $colorDanger }};" title="Danger"></span>
                                                </div>

                                                {{-- AÇÕES: PREVIEW + SELECIONAR --}}
                                                <div class="theme-card-actions">
                                                    {{-- Botão PREVIEW --}}
                                                    <button type="button"
                                                            class="kr-btn-preview"
                                                            data-theme-slug="{{ $themeSlug }}"
                                                            data-theme-name="{{ $theme['name'] ?? $themeSlug }}"
                                                            data-radio-id="theme_{{ $themeSlug }}"
                                                            data-preview-url="{{ asset('storage/themes/' . $themeSlug . '/' . ($theme['preview'] ?? 'preview.png')) }}"
                                                            data-primary="{{ $colorPrimary }}"
                                                            data-success="{{ $colorSuccess }}"
                                                            data-warning="{{ $colorWarning }}"
                                                            data-danger="{{ $colorDanger }}"
                                                            onclick="event.preventDefault(); event.stopPropagation(); window.krOpenThemePreview(this); return false;"
                                                            title="Ver preview ampliado">
                                                        <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        <span>Preview</span>
                                                    </button>

                                                    {{-- Botão SELECIONAR --}}
                                                    <button type="button"
                                                            class="kr-btn-select"
                                                            data-role="select-theme-btn"
                                                            data-radio-id="theme_{{ $themeSlug }}"
                                                            onclick="event.preventDefault(); event.stopPropagation(); window.krSelectTheme(this); return false;"
                                                            title="Selecionar este tema"
                                                            style="background:#3b82f6; color:#fff; border:none; cursor:pointer; transition:all 0.15s ease; display:flex; align-items:center; justify-content:center; gap:6px; font-weight:600;">
                                                        <svg class="kr-btn-select-icon" style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        <span class="kr-btn-select-text">Selecionar</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- FIM DO CARD WRAPPER --}}
                            @endforeach
                        </div>

                        <!-- Warning Box -->
                        <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-600 rounded-r flex items-start gap-3">
                            <span class="text-yellow-600 dark:text-yellow-400 text-2xl flex-shrink-0">⚠️</span>
                            <div>
                                <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-200 mb-1">
                                    Atenção ao Trocar de Tema
                                </p>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                    Ao selecionar um tema diferente, suas customizações atuais (cores, logos, etc) serão substituídas pelas configurações padrão do novo tema. Esta ação pode ser revertida usando o botão "Voltar Tema Anterior".
                                </p>
                            </div>
                        </div>

                    </div>
                    @endif

                    </div>
                    <!-- FIM SEÇÃO 1: ATIVAÇÃO E TEMA BASE -->

                    <!-- ═══════════════════════════════════════════════════════════ -->
                    <!-- SEÇÃO 2: PERSONALIZAÇÕES -->
                    <!-- ═══════════════════════════════════════════════════════════ -->
                    <div class="box-shadow rounded-lg border-2 border-purple-200 bg-gradient-to-br from-purple-50 to-white p-6 dark:border-purple-800 dark:from-gray-900 dark:to-gray-800 mb-6">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="text-3xl">✨</span>
                            <div>
                                <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                                    Personalizações
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Customize cores, logos e página de login
                                </p>
                            </div>
                        </div>

                    <!-- ═══════════════════════════════════════════════════════════════════════════
                         SUB-SEÇÃO: EDITOR DE TOKENS DE COR (Moderno)
                         - Preview ao vivo com CSS vars
                         - Validação hex/rgba
                         - Undo (valor salvo) e Reset (defaults do sistema)
                         Janeiro 2026
                         ═══════════════════════════════════════════════════════════════════════════ -->
                    <div id="krColorTokenEditor" class="box-shadow rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                        {{-- Header com título e ações globais --}}
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🎨</span>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">
                                        @lang('theme-manager::app.settings.colors.title')
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        @lang('theme-manager::app.settings.colors.description')
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        onclick="krColorEditor.undoAll()"
                                        class="kr-btn-ghost"
                                        title="Desfazer todas as alterações para valores salvos">
                                    <span>⏪</span> Undo All
                                </button>
                                <button type="button"
                                        onclick="krColorEditor.resetAll()"
                                        class="kr-btn-ghost"
                                        title="Restaurar todos para defaults do sistema">
                                    <span>🔄</span> Reset All
                                </button>
                            </div>
                        </div>

                        {{-- Preview ao vivo --}}
                        <div id="krColorPreviewBar" class="px-5 py-3 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-4 flex-wrap">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Preview:</span>
                                <div class="flex items-center gap-3 flex-wrap">
                                    {{-- Botão primário --}}
                                    <button type="button" class="kr-preview-btn kr-preview-primary" style="background-color: var(--kr-preview-primary, #1E40AF);">
                                        Primário
                                    </button>
                                    {{-- Botão sucesso --}}
                                    <button type="button" class="kr-preview-btn kr-preview-success" style="background-color: var(--kr-preview-success, #10B981);">
                                        Sucesso
                                    </button>
                                    {{-- Botão warning --}}
                                    <button type="button" class="kr-preview-btn kr-preview-warning" style="background-color: var(--kr-preview-warning, #F59E0B);">
                                        Alerta
                                    </button>
                                    {{-- Botão danger --}}
                                    <button type="button" class="kr-preview-btn kr-preview-danger" style="background-color: var(--kr-preview-danger, #EF4444);">
                                        Perigo
                                    </button>
                                    {{-- Badge exemplo --}}
                                    <span class="kr-preview-badge" style="background-color: var(--kr-preview-primary-light, #3B82F6);">
                                        Badge
                                    </span>
                                    {{-- Link exemplo --}}
                                    <a href="javascript:void(0)" class="kr-preview-link" style="color: var(--kr-preview-primary, #1E40AF);">
                                        Link de exemplo
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Grid de tokens de cor --}}
                        <div class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @php
                                    $colorTokens = [
                                        [
                                            'name' => 'color_primary',
                                            'label' => __('theme-manager::app.settings.colors.primary'),
                                            'description' => 'Cor principal da marca. Botões, links e destaques.',
                                            'cssVars' => ['--primary-color', '--primary-hover'],
                                            'previewVar' => '--kr-preview-primary',
                                            'type' => 'hex',
                                        ],
                                        [
                                            'name' => 'color_primary_dark',
                                            'label' => __('theme-manager::app.settings.colors.primary-dark'),
                                            'description' => 'Variação escura. Estados hover e pressed.',
                                            'cssVars' => ['--primary-dark'],
                                            'previewVar' => '--kr-preview-primary-dark',
                                            'type' => 'hex',
                                        ],
                                        [
                                            'name' => 'color_primary_light',
                                            'label' => __('theme-manager::app.settings.colors.primary-light'),
                                            'description' => 'Variação clara. Backgrounds e bordas sutis.',
                                            'cssVars' => ['--primary-light'],
                                            'previewVar' => '--kr-preview-primary-light',
                                            'type' => 'hex',
                                        ],
                                        [
                                            'name' => 'color_success',
                                            'label' => __('theme-manager::app.settings.colors.success'),
                                            'description' => 'Sucesso e confirmações. Badges positivos.',
                                            'cssVars' => ['--success-color'],
                                            'previewVar' => '--kr-preview-success',
                                            'type' => 'hex',
                                        ],
                                        [
                                            'name' => 'color_warning',
                                            'label' => __('theme-manager::app.settings.colors.warning'),
                                            'description' => 'Alertas e avisos. Atenção do usuário.',
                                            'cssVars' => ['--warning-color'],
                                            'previewVar' => '--kr-preview-warning',
                                            'type' => 'hex',
                                        ],
                                        [
                                            'name' => 'color_danger',
                                            'label' => __('theme-manager::app.settings.colors.danger'),
                                            'description' => 'Erros e ações destrutivas. Exclusões.',
                                            'cssVars' => ['--danger-color'],
                                            'previewVar' => '--kr-preview-danger',
                                            'type' => 'hex',
                                        ],
                                        [
                                            'name' => 'login_card_overlay_color',
                                            'label' => __('theme-manager::app.settings.login-card.overlay-color'),
                                            'description' => 'Overlay do card de login. Formato rgba().',
                                            'cssVars' => ['--login-card-overlay'],
                                            'previewVar' => '--kr-preview-overlay',
                                            'type' => 'rgba',
                                        ],
                                    ];
                                @endphp

                                @foreach($colorTokens as $token)
                                    @php
                                        $fieldName = $token['name'];
                                        $currentValue = old($fieldName, $config->$fieldName ?? '');
                                        $isRgba = $token['type'] === 'rgba';
                                    @endphp
                                    <div class="kr-color-token-card" data-token="{{ $fieldName }}" data-type="{{ $token['type'] }}">
                                        {{-- Header do token --}}
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1 min-w-0">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 truncate">
                                                    {{ $token['label'] }}
                                                </label>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">
                                                    {{ $token['description'] }}
                                                </p>
                                            </div>
                                            {{-- Chip de cor --}}
                                            <div class="kr-color-chip-wrapper ml-2 flex-shrink-0">
                                                <div class="kr-color-chip"
                                                     id="chip_{{ $fieldName }}"
                                                     style="background: {{ $currentValue ?: '#CCCCCC' }};"
                                                     title="Clique para copiar">
                                                </div>
                                                <span class="kr-chip-copied" id="copied_{{ $fieldName }}">✓</span>
                                            </div>
                                        </div>

                                        {{-- Controles: Picker + Input + Ações --}}
                                        <div class="flex items-center gap-2">
                                            @if(!$isRgba)
                                                {{-- Color picker (apenas para hex) --}}
                                                <input type="color"
                                                       id="picker_{{ $fieldName }}"
                                                       value="{{ $currentValue ?: '#1E40AF' }}"
                                                       class="kr-color-picker"
                                                       data-target="{{ $fieldName }}">
                                            @else
                                                {{-- Placeholder para rgba (sem picker nativo) --}}
                                                <div class="kr-rgba-indicator"
                                                     id="picker_{{ $fieldName }}"
                                                     style="background: {{ $currentValue ?: 'rgba(10,45,15,0.78)' }};"
                                                     title="Edite o valor rgba no campo texto">
                                                </div>
                                            @endif

                                            {{-- Input de texto --}}
                                            <input type="text"
                                                   id="text_{{ $fieldName }}"
                                                   value="{{ $currentValue }}"
                                                   placeholder="{{ $isRgba ? 'rgba(0,0,0,0.5)' : '#RRGGBB' }}"
                                                   class="kr-color-text-input flex-1"
                                                   data-target="{{ $fieldName }}"
                                                   data-type="{{ $token['type'] }}"
                                                   autocomplete="off"
                                                   spellcheck="false">

                                            {{-- Input hidden real (enviado no form) --}}
                                            <input type="hidden"
                                                   name="{{ $fieldName }}"
                                                   id="real_{{ $fieldName }}"
                                                   value="{{ $currentValue }}">

                                            {{-- Botões de ação --}}
                                            <div class="flex items-center gap-1">
                                                <button type="button"
                                                        class="kr-token-action"
                                                        onclick="krColorEditor.undo('{{ $fieldName }}')"
                                                        title="Desfazer para valor salvo">
                                                    ⏪
                                                </button>
                                                <button type="button"
                                                        class="kr-token-action"
                                                        onclick="krColorEditor.reset('{{ $fieldName }}')"
                                                        title="Restaurar para default do sistema">
                                                    🔄
                                                </button>
                                                <button type="button"
                                                        class="kr-token-action"
                                                        onclick="krColorEditor.copy('{{ $fieldName }}')"
                                                        title="Copiar valor">
                                                    📋
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Feedback de erro --}}
                                        <div class="kr-token-error hidden" id="error_{{ $fieldName }}">
                                            <span class="text-xs text-red-600 dark:text-red-400"></span>
                                        </div>

                                        {{-- Erro do Laravel --}}
                                        <x-admin::form.control-group.error control-name="{{ $fieldName }}" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- ═══════════════════════════════════════════════════════════════════════════
                         CSS do Editor de Tokens de Cor (inline, sem build)
                         ═══════════════════════════════════════════════════════════════════════════ --}}
                    <style>
                    /* Card de token individual */
                    .kr-color-token-card {
                        background: #fff;
                        border: 1px solid #e5e7eb;
                        border-radius: 0.75rem;
                        padding: 1rem;
                        transition: all 0.2s ease;
                    }
                    .dark .kr-color-token-card {
                        background: #1f2937;
                        border-color: #374151;
                    }
                    .kr-color-token-card:hover {
                        border-color: #3b82f6;
                        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
                    }
                    .kr-color-token-card.is-invalid {
                        border-color: #ef4444 !important;
                        background: #fef2f2;
                    }
                    .dark .kr-color-token-card.is-invalid {
                        background: rgba(239, 68, 68, 0.1);
                    }

                    /* Chip de cor */
                    .kr-color-chip-wrapper {
                        position: relative;
                    }
                    .kr-color-chip {
                        width: 32px;
                        height: 32px;
                        border-radius: 50%;
                        border: 2px solid #fff;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.15), inset 0 0 0 1px rgba(0,0,0,0.1);
                        cursor: pointer;
                        transition: transform 0.15s ease, box-shadow 0.15s ease;
                    }
                    .kr-color-chip:hover {
                        transform: scale(1.1);
                        box-shadow: 0 4px 8px rgba(0,0,0,0.2), inset 0 0 0 1px rgba(0,0,0,0.1);
                    }
                    .kr-chip-copied {
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%) scale(0);
                        background: #10b981;
                        color: #fff;
                        width: 20px;
                        height: 20px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 10px;
                        font-weight: bold;
                        opacity: 0;
                        transition: all 0.2s ease;
                        pointer-events: none;
                    }
                    .kr-chip-copied.show {
                        transform: translate(-50%, -50%) scale(1);
                        opacity: 1;
                    }

                    /* Color picker */
                    .kr-color-picker {
                        width: 36px;
                        height: 36px;
                        padding: 0;
                        border: 2px solid #e5e7eb;
                        border-radius: 0.5rem;
                        cursor: pointer;
                        background: transparent;
                        transition: border-color 0.15s ease;
                    }
                    .kr-color-picker:hover {
                        border-color: #3b82f6;
                    }
                    .kr-color-picker::-webkit-color-swatch-wrapper {
                        padding: 2px;
                    }
                    .kr-color-picker::-webkit-color-swatch {
                        border: none;
                        border-radius: 4px;
                    }

                    /* RGBA indicator (para campos rgba sem picker) */
                    .kr-rgba-indicator {
                        width: 36px;
                        height: 36px;
                        border: 2px solid #e5e7eb;
                        border-radius: 0.5rem;
                        background-image: linear-gradient(45deg, #ccc 25%, transparent 25%),
                                          linear-gradient(-45deg, #ccc 25%, transparent 25%),
                                          linear-gradient(45deg, transparent 75%, #ccc 75%),
                                          linear-gradient(-45deg, transparent 75%, #ccc 75%);
                        background-size: 8px 8px;
                        background-position: 0 0, 0 4px, 4px -4px, -4px 0px;
                        position: relative;
                        overflow: hidden;
                    }
                    .kr-rgba-indicator::after {
                        content: '';
                        position: absolute;
                        inset: 2px;
                        border-radius: 4px;
                        background: inherit;
                    }

                    /* Input de texto */
                    .kr-color-text-input {
                        height: 36px;
                        padding: 0 0.75rem;
                        border: 1px solid #e5e7eb;
                        border-radius: 0.5rem;
                        font-family: ui-monospace, SFMono-Regular, monospace;
                        font-size: 0.875rem;
                        text-transform: uppercase;
                        background: #fff;
                        color: #1f2937;
                        transition: all 0.15s ease;
                        min-width: 0;
                    }
                    .dark .kr-color-text-input {
                        background: #374151;
                        border-color: #4b5563;
                        color: #f9fafb;
                    }
                    .kr-color-text-input:focus {
                        outline: none;
                        border-color: #3b82f6;
                        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
                    }
                    .kr-color-text-input.is-invalid {
                        border-color: #ef4444 !important;
                        background: #fef2f2;
                    }
                    .dark .kr-color-text-input.is-invalid {
                        background: rgba(239, 68, 68, 0.15);
                    }
                    .kr-color-text-input[data-type="rgba"] {
                        text-transform: none;
                    }

                    /* Botões de ação do token */
                    .kr-token-action {
                        width: 28px;
                        height: 28px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border: none;
                        background: #f3f4f6;
                        border-radius: 0.375rem;
                        cursor: pointer;
                        font-size: 12px;
                        transition: all 0.15s ease;
                    }
                    .dark .kr-token-action {
                        background: #374151;
                    }
                    .kr-token-action:hover {
                        background: #e5e7eb;
                        transform: scale(1.05);
                    }
                    .dark .kr-token-action:hover {
                        background: #4b5563;
                    }

                    /* Erro do token */
                    .kr-token-error {
                        margin-top: 0.5rem;
                        padding: 0.25rem 0.5rem;
                        background: #fef2f2;
                        border-radius: 0.25rem;
                        border-left: 3px solid #ef4444;
                    }
                    .dark .kr-token-error {
                        background: rgba(239, 68, 68, 0.1);
                    }

                    /* Botões ghost do header */
                    .kr-btn-ghost {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.375rem;
                        padding: 0.5rem 0.75rem;
                        font-size: 0.75rem;
                        font-weight: 500;
                        color: #6b7280;
                        background: transparent;
                        border: 1px solid #e5e7eb;
                        border-radius: 0.5rem;
                        cursor: pointer;
                        transition: all 0.15s ease;
                    }
                    .dark .kr-btn-ghost {
                        color: #9ca3af;
                        border-color: #4b5563;
                    }
                    .kr-btn-ghost:hover {
                        background: #f3f4f6;
                        color: #374151;
                        border-color: #d1d5db;
                    }
                    .dark .kr-btn-ghost:hover {
                        background: #374151;
                        color: #f9fafb;
                    }

                    /* Preview buttons */
                    .kr-preview-btn {
                        padding: 0.375rem 0.75rem;
                        font-size: 0.75rem;
                        font-weight: 500;
                        color: #fff;
                        border: none;
                        border-radius: 0.375rem;
                        cursor: default;
                        transition: all 0.2s ease;
                        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
                    }
                    .kr-preview-badge {
                        padding: 0.25rem 0.625rem;
                        font-size: 0.625rem;
                        font-weight: 600;
                        color: #fff;
                        border-radius: 9999px;
                        text-transform: uppercase;
                        letter-spacing: 0.025em;
                    }
                    .kr-preview-link {
                        font-size: 0.75rem;
                        text-decoration: underline;
                        text-underline-offset: 2px;
                        transition: opacity 0.15s ease;
                    }
                    .kr-preview-link:hover {
                        opacity: 0.8;
                    }

                    /* Line clamp utility */
                    .line-clamp-2 {
                        display: -webkit-box;
                        -webkit-line-clamp: 2;
                        -webkit-box-orient: vertical;
                        overflow: hidden;
                    }

                    /* ═══════════════════════════════════════════════════════════════════════════
                       LAYOUT COMPACTO: .kr-color-row
                       Grid: [label/desc] [chip] [picker] [texto] [botões]
                       ═══════════════════════════════════════════════════════════════════════════ */
                    .kr-color-row {
                        display: grid;
                        grid-template-columns: 1fr auto auto 1fr auto;
                        grid-template-areas: "label chip picker input actions";
                        align-items: center;
                        gap: 0.75rem;
                        padding: 0.875rem 1rem;
                        background: #fff;
                        border: 1px solid #e5e7eb;
                        border-radius: 0.5rem;
                        transition: all 0.2s ease;
                    }
                    .dark .kr-color-row {
                        background: rgba(31, 41, 55, 0.5);
                        border-color: rgba(75, 85, 99, 0.5);
                    }
                    .kr-color-row:hover {
                        border-color: #93c5fd;
                        box-shadow: 0 1px 4px rgba(59, 130, 246, 0.08);
                    }
                    .dark .kr-color-row:hover {
                        border-color: rgba(96, 165, 250, 0.4);
                        box-shadow: 0 1px 4px rgba(59, 130, 246, 0.15);
                    }

                    /* Grid areas */
                    .kr-color-row .kr-row-label { grid-area: label; }
                    .kr-color-row .kr-row-chip { grid-area: chip; }
                    .kr-color-row .kr-row-picker { grid-area: picker; }
                    .kr-color-row .kr-row-input { grid-area: input; }
                    .kr-color-row .kr-row-actions { grid-area: actions; }

                    /* Label e descrição inline */
                    .kr-row-label {
                        display: flex;
                        flex-direction: column;
                        gap: 0.125rem;
                        min-width: 0;
                    }
                    .kr-row-label-text {
                        font-size: 0.8125rem;
                        font-weight: 600;
                        color: #374151;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                    .dark .kr-row-label-text {
                        color: #e5e7eb;
                    }
                    .kr-row-label-desc {
                        font-size: 0.6875rem;
                        color: #9ca3af;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                    .dark .kr-row-label-desc {
                        color: #6b7280;
                    }

                    /* Chip compacto na row */
                    .kr-row-chip .kr-color-chip {
                        width: 28px;
                        height: 28px;
                    }

                    /* Picker compacto na row */
                    .kr-row-picker .kr-color-picker,
                    .kr-row-picker .kr-rgba-indicator {
                        width: 32px;
                        height: 32px;
                    }

                    /* Input compacto na row */
                    .kr-row-input .kr-color-text-input {
                        height: 32px;
                        font-size: 0.8125rem;
                        min-width: 100px;
                        max-width: 140px;
                    }

                    /* Actions compacto */
                    .kr-row-actions {
                        display: flex;
                        gap: 0.25rem;
                    }
                    .kr-row-actions .kr-token-action {
                        width: 26px;
                        height: 26px;
                        font-size: 11px;
                    }

                    /* ═══════════════════════════════════════════════════════════════════════════
                       ESTADO: .is-dirty (valor alterado, não salvo)
                       ═══════════════════════════════════════════════════════════════════════════ */
                    .kr-color-row.is-dirty,
                    .kr-color-token-card.is-dirty {
                        border-color: #f59e0b;
                        background: linear-gradient(135deg, rgba(251, 191, 36, 0.04) 0%, rgba(251, 191, 36, 0.01) 100%);
                        box-shadow: 0 0 0 1px rgba(245, 158, 11, 0.1);
                    }
                    .dark .kr-color-row.is-dirty,
                    .dark .kr-color-token-card.is-dirty {
                        border-color: rgba(251, 191, 36, 0.5);
                        background: linear-gradient(135deg, rgba(251, 191, 36, 0.08) 0%, rgba(251, 191, 36, 0.02) 100%);
                        box-shadow: 0 0 0 1px rgba(245, 158, 11, 0.15);
                    }

                    /* Indicador visual de dirty */
                    .kr-color-row.is-dirty::before,
                    .kr-color-token-card.is-dirty::before {
                        content: '';
                        position: absolute;
                        top: 0.5rem;
                        right: 0.5rem;
                        width: 6px;
                        height: 6px;
                        background: #f59e0b;
                        border-radius: 50%;
                        animation: kr-pulse-dirty 1.5s ease-in-out infinite;
                    }
                    .kr-color-row,
                    .kr-color-token-card {
                        position: relative;
                    }

                    @keyframes kr-pulse-dirty {
                        0%, 100% { opacity: 1; transform: scale(1); }
                        50% { opacity: 0.5; transform: scale(0.85); }
                    }

                    /* ═══════════════════════════════════════════════════════════════════════════
                       ESTADO: .is-invalid melhorado (borda/outline mais visível)
                       ═══════════════════════════════════════════════════════════════════════════ */
                    .kr-color-row.is-invalid,
                    .kr-color-token-card.is-invalid {
                        border-color: #ef4444 !important;
                        background: linear-gradient(135deg, rgba(239, 68, 68, 0.06) 0%, rgba(239, 68, 68, 0.02) 100%);
                        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.15);
                    }
                    .dark .kr-color-row.is-invalid,
                    .dark .kr-color-token-card.is-invalid {
                        background: linear-gradient(135deg, rgba(239, 68, 68, 0.12) 0%, rgba(239, 68, 68, 0.04) 100%);
                        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.25);
                    }

                    .kr-color-text-input.is-invalid {
                        border-color: #ef4444 !important;
                        background: #fef2f2 !important;
                        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
                        outline: 2px solid transparent;
                        outline-offset: 2px;
                    }
                    .dark .kr-color-text-input.is-invalid {
                        background: rgba(239, 68, 68, 0.15) !important;
                        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.3);
                    }
                    .kr-color-text-input.is-invalid:focus {
                        border-color: #dc2626 !important;
                        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.25);
                    }

                    /* ═══════════════════════════════════════════════════════════════════════════
                       RESPONSIVO: <900px reorganiza para 2 linhas
                       Linha 1: [label] [chip] [actions]
                       Linha 2: [picker] [input]
                       ═══════════════════════════════════════════════════════════════════════════ */
                    @media (max-width: 899px) {
                        .kr-color-row {
                            grid-template-columns: 1fr auto auto;
                            grid-template-areas:
                                "label chip actions"
                                "picker input input";
                            row-gap: 0.625rem;
                            padding: 0.75rem;
                        }

                        .kr-row-label {
                            flex-direction: row;
                            align-items: baseline;
                            gap: 0.5rem;
                        }
                        .kr-row-label-text {
                            flex-shrink: 0;
                        }
                        .kr-row-label-desc {
                            flex: 1;
                            min-width: 0;
                        }

                        .kr-row-input .kr-color-text-input {
                            max-width: none;
                            width: 100%;
                        }

                        /* Grid do editor também ajusta */
                        #krColorTokenEditor .grid {
                            grid-template-columns: 1fr !important;
                        }
                    }

                    /* <600px: ainda mais compacto */
                    @media (max-width: 599px) {
                        .kr-color-row {
                            grid-template-columns: 1fr auto;
                            grid-template-areas:
                                "label actions"
                                "controls controls";
                            padding: 0.625rem;
                        }

                        .kr-row-chip,
                        .kr-row-picker,
                        .kr-row-input {
                            grid-area: controls;
                        }

                        /* Wrapper para controles em mobile */
                        .kr-color-row .kr-mobile-controls {
                            grid-area: controls;
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                            width: 100%;
                        }

                        .kr-row-label-desc {
                            display: none; /* Oculta descrição em mobile extremo */
                        }

                        .kr-row-input .kr-color-text-input {
                            flex: 1;
                            min-width: 0;
                        }
                    }

                    /* ═══════════════════════════════════════════════════════════════════════════
                       DARK MODE: Ajustes finos de opacidade
                       ═══════════════════════════════════════════════════════════════════════════ */
                    .dark .kr-color-row {
                        background: rgba(17, 24, 39, 0.6);
                        border-color: rgba(55, 65, 81, 0.6);
                    }
                    .dark .kr-color-row:hover {
                        background: rgba(17, 24, 39, 0.8);
                        border-color: rgba(96, 165, 250, 0.5);
                    }

                    .dark .kr-color-chip {
                        border-color: rgba(255, 255, 255, 0.2);
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3), inset 0 0 0 1px rgba(255, 255, 255, 0.1);
                    }

                    .dark .kr-color-picker {
                        border-color: rgba(75, 85, 99, 0.8);
                    }
                    .dark .kr-color-picker:hover {
                        border-color: rgba(96, 165, 250, 0.6);
                    }

                    .dark .kr-rgba-indicator {
                        border-color: rgba(75, 85, 99, 0.8);
                        background-image: linear-gradient(45deg, rgba(75, 85, 99, 0.5) 25%, transparent 25%),
                                          linear-gradient(-45deg, rgba(75, 85, 99, 0.5) 25%, transparent 25%),
                                          linear-gradient(45deg, transparent 75%, rgba(75, 85, 99, 0.5) 75%),
                                          linear-gradient(-45deg, transparent 75%, rgba(75, 85, 99, 0.5) 75%);
                    }

                    .dark .kr-token-action {
                        background: rgba(55, 65, 81, 0.6);
                        color: #d1d5db;
                    }
                    .dark .kr-token-action:hover {
                        background: rgba(75, 85, 99, 0.8);
                        color: #f9fafb;
                    }

                    .dark .kr-token-error {
                        background: rgba(127, 29, 29, 0.2);
                        border-left-color: #f87171;
                    }

                    .dark .kr-btn-ghost {
                        color: rgba(156, 163, 175, 0.9);
                        border-color: rgba(75, 85, 99, 0.6);
                    }
                    .dark .kr-btn-ghost:hover {
                        background: rgba(55, 65, 81, 0.6);
                        color: #f3f4f6;
                        border-color: rgba(107, 114, 128, 0.6);
                    }
                    </style>

                    {{-- ═══════════════════════════════════════════════════════════════════════════
                         JS do Editor de Tokens de Cor (inline, sem build, DOMContentLoaded 1x)
                         ═══════════════════════════════════════════════════════════════════════════ --}}
                    <script>
                    (function() {
                        'use strict';

                        // ═══════════════════════════════════════════════════════════════════════════
                        // DEBUG GATE: Logs condicionais (silenciados em produção)
                        // ═══════════════════════════════════════════════════════════════════════════
                        window.__krColorEditorDebug = @json((bool) config('app.debug', false));

                        function krLog() {
                            if (!window.__krColorEditorDebug) return;
                            console.log.apply(console, arguments);
                        }
                        function krLogWarn() {
                            if (!window.__krColorEditorDebug) return;
                            console.warn.apply(console, arguments);
                        }
                        function krLogInfo() {
                            if (!window.__krColorEditorDebug) return;
                            console.info.apply(console, arguments);
                        }

                        // ═══════════════════════════════════════════════════════════════════════════
                        // BOOT: Normalizar defaults e saved para chaves flat (sem hardcode por token)
                        // ═══════════════════════════════════════════════════════════════════════════

                        // Raw config do Laravel (estrutura aninhada: colors.primary, login.card_overlay_color)
                        // Guardamos imutável para debug
                        var rawDefaults = @json(config('theme-manager.defaults', []));
                        window.__krRawSystemDefaults = rawDefaults; // Debug: estrutura original

                        // Normalizar para chaves flat (color_primary, login_card_overlay_color, etc.)
                        function normalizeDefaults(raw) {
                            var flat = {};
                            var missing = [];

                            // Cores: raw.colors.X -> color_X
                            if (raw && raw.colors) {
                                Object.keys(raw.colors).forEach(function(key) {
                                    flat['color_' + key] = raw.colors[key];
                                });
                            } else {
                                missing.push('colors.*');
                            }

                            // Login: raw.login.card_overlay_color -> login_card_overlay_color
                            if (raw && raw.login) {
                                if (raw.login.card_overlay_color !== undefined) {
                                    flat['login_card_overlay_color'] = raw.login.card_overlay_color;
                                } else {
                                    missing.push('login.card_overlay_color');
                                }
                            } else {
                                missing.push('login.*');
                            }

                            // Log de aviso se defaults estiverem ausentes (apenas em debug)
                            if (missing.length > 0) {
                                krLogWarn('[KR Color Editor] Defaults ausentes em config(theme-manager.defaults):', missing);
                            }

                            return flat;
                        }

                        // Defaults normalizados (chaves flat, sem hardcode)
                        window.__krSystemDefaults = normalizeDefaults(rawDefaults);

                        // Valores salvos (snapshot do DB no momento do load)
                        window.__krSavedConfig = {
                            color_primary: @json($config->color_primary ?? ''),
                            color_primary_dark: @json($config->color_primary_dark ?? ''),
                            color_primary_light: @json($config->color_primary_light ?? ''),
                            color_success: @json($config->color_success ?? ''),
                            color_warning: @json($config->color_warning ?? ''),
                            color_danger: @json($config->color_danger ?? ''),
                            login_card_overlay_color: @json($config->login_card_overlay_color ?? ''),
                        };

                        // Lista de todos os campos de cor (single source of truth)
                        var COLOR_FIELDS = [
                            'color_primary',
                            'color_primary_dark',
                            'color_primary_light',
                            'color_success',
                            'color_warning',
                            'color_danger',
                            'login_card_overlay_color'
                        ];

                        // Mapa de CSS vars para preview
                        var CSS_VAR_MAP = {
                            color_primary: ['--primary-color', '--primary-hover', '--kr-preview-primary'],
                            color_primary_dark: ['--primary-dark', '--kr-preview-primary-dark'],
                            color_primary_light: ['--primary-light', '--kr-preview-primary-light'],
                            color_success: ['--success-color', '--kr-preview-success'],
                            color_warning: ['--warning-color', '--kr-preview-warning'],
                            color_danger: ['--danger-color', '--kr-preview-danger'],
                            login_card_overlay_color: ['--login-card-overlay', '--kr-preview-overlay']
                        };

                        // ═══════════════════════════════════════════════════════════════════════════
                        // VALIDADORES
                        // ═══════════════════════════════════════════════════════════════════════════
                        var HEX_REGEX = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
                        var RGBA_REGEX = /^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*(0|1|0?\.\d+))?\s*\)$/i;

                        function validateHex(value) {
                            if (!value) return { valid: false, error: 'Valor obrigatório' };
                            var normalized = value.trim().toUpperCase();
                            if (!normalized.startsWith('#')) normalized = '#' + normalized;
                            if (!HEX_REGEX.test(normalized)) {
                                return { valid: false, error: 'Formato inválido. Use #RRGGBB' };
                            }
                            // Expandir formato curto (#ABC -> #AABBCC)
                            if (normalized.length === 4) {
                                normalized = '#' + normalized[1] + normalized[1] + normalized[2] + normalized[2] + normalized[3] + normalized[3];
                            }
                            return { valid: true, value: normalized };
                        }

                        function validateRgba(value) {
                            if (!value) return { valid: false, error: 'Valor obrigatório' };
                            var trimmed = value.trim();
                            var match = trimmed.match(RGBA_REGEX);
                            if (!match) {
                                return { valid: false, error: 'Formato inválido. Use rgba(r,g,b,a)' };
                            }
                            var r = parseInt(match[1], 10);
                            var g = parseInt(match[2], 10);
                            var b = parseInt(match[3], 10);
                            var a = match[4] !== undefined ? parseFloat(match[4]) : 1;

                            if (r < 0 || r > 255 || g < 0 || g > 255 || b < 0 || b > 255) {
                                return { valid: false, error: 'RGB deve estar entre 0-255' };
                            }
                            if (a < 0 || a > 1) {
                                return { valid: false, error: 'Alpha deve estar entre 0-1' };
                            }
                            return { valid: true, value: 'rgba(' + r + ', ' + g + ', ' + b + ', ' + a + ')' };
                        }

                        // ═══════════════════════════════════════════════════════════════════════════
                        // GETTERS (sem hardcode - usam chaves flat normalizadas)
                        // ═══════════════════════════════════════════════════════════════════════════
                        function getDefaultValue(fieldName) {
                            var defaults = window.__krSystemDefaults || {};
                            return defaults[fieldName] ?? '';
                        }

                        function getSavedValue(fieldName) {
                            var saved = window.__krSavedConfig || {};
                            return saved[fieldName] ?? '';
                        }

                        function applyLivePreview(fieldName, value) {
                            var vars = CSS_VAR_MAP[fieldName] || [];
                            var root = document.documentElement;
                            vars.forEach(function(varName) {
                                root.style.setProperty(varName, value);
                            });
                        }

                        function updateUI(fieldName, value, isValid) {
                            var picker = document.getElementById('picker_' + fieldName);
                            var text = document.getElementById('text_' + fieldName);
                            var real = document.getElementById('real_' + fieldName);
                            var chip = document.getElementById('chip_' + fieldName);
                            var card = text ? text.closest('.kr-color-token-card') : null;
                            var errorDiv = document.getElementById('error_' + fieldName);

                            if (isValid) {
                                // Atualizar todos os elementos
                                if (picker && picker.type === 'color') picker.value = value;
                                if (picker && picker.classList.contains('kr-rgba-indicator')) picker.style.background = value;
                                if (text) text.value = value;
                                if (real) real.value = value;
                                if (chip) chip.style.background = value;

                                // Remover estado de erro
                                if (text) text.classList.remove('is-invalid');
                                if (card) card.classList.remove('is-invalid');
                                if (errorDiv) {
                                    errorDiv.classList.add('hidden');
                                    errorDiv.querySelector('span').textContent = '';
                                }

                                // Gerenciar estado .is-dirty (valor diferente do salvo)
                                var savedValue = getSavedValue(fieldName);
                                var isDirty = value !== savedValue;
                                if (card) {
                                    if (isDirty) {
                                        card.classList.add('is-dirty');
                                    } else {
                                        card.classList.remove('is-dirty');
                                    }
                                }

                                // Aplicar preview ao vivo
                                applyLivePreview(fieldName, value);
                            } else {
                                // Marcar erro (não propagar para input real)
                                if (text) text.classList.add('is-invalid');
                                if (card) card.classList.add('is-invalid');
                            }
                        }

                        function showError(fieldName, message) {
                            var errorDiv = document.getElementById('error_' + fieldName);
                            if (errorDiv) {
                                errorDiv.classList.remove('hidden');
                                errorDiv.querySelector('span').textContent = message;
                            }
                        }

                        function showCopiedFeedback(fieldName) {
                            var copiedEl = document.getElementById('copied_' + fieldName);
                            if (copiedEl) {
                                copiedEl.classList.add('show');
                                setTimeout(function() {
                                    copiedEl.classList.remove('show');
                                }, 1000);
                            }
                        }

                        // API pública
                        window.krColorEditor = {
                            validate: function(fieldName, value) {
                                var card = document.querySelector('[data-token="' + fieldName + '"]');
                                var type = card ? card.dataset.type : 'hex';
                                return type === 'rgba' ? validateRgba(value) : validateHex(value);
                            },

                            setValue: function(fieldName, value) {
                                var result = this.validate(fieldName, value);
                                if (result.valid) {
                                    updateUI(fieldName, result.value, true);
                                } else {
                                    updateUI(fieldName, value, false);
                                    showError(fieldName, result.error);
                                }
                                return result.valid;
                            },

                            undo: function(fieldName) {
                                var saved = getSavedValue(fieldName);
                                if (!saved) {
                                    krLogWarn('[KR Color] Undo: nenhum valor salvo para', fieldName);
                                    // Fallback: tenta usar default
                                    saved = getDefaultValue(fieldName);
                                }
                                if (saved) {
                                    this.setValue(fieldName, saved);
                                    krLog('[KR Color] Undo:', fieldName, '->', saved);
                                } else {
                                    krLogWarn('[KR Color] Undo: sem valor para restaurar', fieldName);
                                }
                            },

                            reset: function(fieldName) {
                                var defaultVal = getDefaultValue(fieldName);
                                if (!defaultVal) {
                                    krLogWarn('[KR Color] Reset: nenhum default configurado para', fieldName);
                                    krLogInfo('[KR Color] Verifique config(theme-manager.defaults) no Laravel');
                                    // Não faz nada se não houver default
                                    return;
                                }
                                this.setValue(fieldName, defaultVal);
                                krLog('[KR Color] Reset:', fieldName, '->', defaultVal);
                            },

                            undoAll: function() {
                                var self = this;
                                Object.keys(window.__krSavedConfig).forEach(function(fieldName) {
                                    self.undo(fieldName);
                                });
                            },

                            resetAll: function() {
                                if (!confirm('Restaurar TODAS as cores para os defaults do sistema?')) return;
                                var self = this;
                                Object.keys(CSS_VAR_MAP).forEach(function(fieldName) {
                                    self.reset(fieldName);
                                });
                            },

                            copy: function(fieldName) {
                                var real = document.getElementById('real_' + fieldName);
                                if (real && real.value) {
                                    navigator.clipboard.writeText(real.value).then(function() {
                                        showCopiedFeedback(fieldName);
                                        krLog('[KR Color] Copied:', real.value);
                                    }).catch(function(err) {
                                        krLogWarn('[KR Color] Copy failed:', err);
                                        // Fallback
                                        var temp = document.createElement('input');
                                        temp.value = real.value;
                                        document.body.appendChild(temp);
                                        temp.select();
                                        document.execCommand('copy');
                                        document.body.removeChild(temp);
                                        showCopiedFeedback(fieldName);
                                    });
                                }
                            }
                        };

                        // Inicializar quando DOM carregar
                        function init() {
                            krLog('[KR Color Editor] Initializing...');
                            krLog('[KR Color Editor] System defaults:', window.__krSystemDefaults);
                            krLog('[KR Color Editor] Saved config:', window.__krSavedConfig);

                            // Bind eventos nos color pickers
                            document.querySelectorAll('.kr-color-picker').forEach(function(picker) {
                                var fieldName = picker.dataset.target;

                                picker.addEventListener('input', function() {
                                    krColorEditor.setValue(fieldName, picker.value);
                                });

                                picker.addEventListener('change', function() {
                                    krColorEditor.setValue(fieldName, picker.value);
                                });
                            });

                            // Bind eventos nos text inputs
                            document.querySelectorAll('.kr-color-text-input').forEach(function(input) {
                                var fieldName = input.dataset.target;

                                input.addEventListener('input', function() {
                                    // Validar mas não bloquear digitação
                                    var result = krColorEditor.validate(fieldName, input.value);
                                    if (result.valid) {
                                        updateUI(fieldName, result.value, true);
                                    } else {
                                        // Mostrar erro visual sem bloquear
                                        input.classList.add('is-invalid');
                                    }
                                });

                                input.addEventListener('blur', function() {
                                    // Validar e aplicar no blur
                                    krColorEditor.setValue(fieldName, input.value);
                                });

                                input.addEventListener('keydown', function(e) {
                                    if (e.key === 'Enter') {
                                        e.preventDefault();
                                        krColorEditor.setValue(fieldName, input.value);
                                        input.blur();
                                    }
                                });
                            });

                            // Bind click nos chips para copiar
                            document.querySelectorAll('.kr-color-chip').forEach(function(chip) {
                                var fieldName = chip.id.replace('chip_', '');
                                chip.addEventListener('click', function() {
                                    krColorEditor.copy(fieldName);
                                });
                            });

                            // Aplicar preview inicial
                            Object.keys(CSS_VAR_MAP).forEach(function(fieldName) {
                                var real = document.getElementById('real_' + fieldName);
                                if (real && real.value) {
                                    applyLivePreview(fieldName, real.value);
                                }
                            });

                            // ═══════════════════════════════════════════════════════════════════════════
                            // SUBMIT HOOK: Blindagem - commit de todos os campos antes de enviar
                            // ═══════════════════════════════════════════════════════════════════════════
                            var form = document.querySelector('form[action*="theme"]');
                            if (form) {
                                form.addEventListener('submit', function(e) {
                                    krLog('[KR Color Editor] Submit intercepted, validating all fields...');

                                    var hasInvalid = false;
                                    var firstInvalid = null;

                                    // Força commit de todos os campos de cor
                                    COLOR_FIELDS.forEach(function(fieldName) {
                                        var textInput = document.getElementById('text_' + fieldName);
                                        if (!textInput) return;

                                        var value = textInput.value;
                                        var result = krColorEditor.validate(fieldName, value);

                                        if (result.valid) {
                                            // Commit: atualiza o input hidden real
                                            var real = document.getElementById('real_' + fieldName);
                                            if (real) real.value = result.value;
                                            textInput.classList.remove('is-invalid');
                                            var card = textInput.closest('.kr-color-token-card');
                                            if (card) card.classList.remove('is-invalid');
                                        } else {
                                            // Marca como inválido
                                            hasInvalid = true;
                                            textInput.classList.add('is-invalid');
                                            var card = textInput.closest('.kr-color-token-card');
                                            if (card) card.classList.add('is-invalid');
                                            showError(fieldName, result.error);
                                            if (!firstInvalid) firstInvalid = textInput;
                                        }
                                    });

                                    // Se houver algum inválido, bloqueia submit
                                    if (hasInvalid) {
                                        e.preventDefault();
                                        e.stopPropagation();

                                        krLogWarn('[KR Color Editor] Submit blocked: invalid fields found');

                                        // Scroll até o primeiro inválido
                                        if (firstInvalid) {
                                            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                            firstInvalid.focus();
                                        }

                                        // Feedback visual
                                        var editor = document.getElementById('krColorTokenEditor');
                                        if (editor) {
                                            editor.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.3)';
                                            setTimeout(function() {
                                                editor.style.boxShadow = '';
                                            }, 2000);
                                        }

                                        return false;
                                    }

                                    krLog('[KR Color Editor] All fields valid, proceeding with submit');
                                });
                            }

                            krLog('[KR Color Editor] Ready!');
                        }

                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', init);
                        } else {
                            init();
                        }
                    })();
                    </script>

                    <!-- SEÇÃO 3 - LOGOS E FAVICON -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            🖼️ @lang('theme-manager::app.settings.logos.title')
                        </p>

                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                            @lang('theme-manager::app.settings.logos.description')
                        </p>

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Logo Principal -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.logos.main')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Logo principal exibido no header. Recomendado: PNG/SVG com fundo transparente, altura máx. 40px.</span>
                                    </span>
                                </x-admin::form.control-group.label>

                                @if($config->logo_main)
                                    <div class="mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center justify-center h-16 w-32 overflow-hidden rounded border border-gray-200 bg-gray-50 p-2 dark:border-gray-700 dark:bg-gray-800">
                                                <img src="{{ asset('storage/theme-manager/' . $config->logo_main) }}"
                                                     alt="Logo Principal"
                                                     class="max-h-full max-w-full object-contain"
                                                     style="max-height: 60px !important; max-width: 120px !important; object-fit: contain !important;">
                                            </div>
                                            <button type="button"
                                                    class="reset-field-btn p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors dark:hover:bg-blue-900/20"
                                                    onclick="resetField('logo_main')"
                                                    title="Restaurar para o tema {{ $config->selected_theme ?? 'default' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </div>

                                        <label class="mt-2 flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="logo_main_delete" value="1" class="rounded">
                                            <span class="text-gray-600 dark:text-gray-400">
                                                @lang('theme-manager::app.settings.logos.delete-current')
                                            </span>
                                        </label>
                                    </div>
                                @endif

                                <x-admin::form.control-group.control
                                    type="file"
                                    name="logo_main"
                                    accept="image/*"
                                />

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.logos.main-help')
                                </p>

                                <x-admin::form.control-group.error control-name="logo_main" />
                            </x-admin::form.control-group>

                            <!-- Logo Claro -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.logos.light')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Logo para modo escuro. Deve ter cores claras para contraste com fundos escuros.</span>
                                    </span>
                                </x-admin::form.control-group.label>

                                @if($config->logo_light)
                                    <div class="mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center justify-center h-16 w-32 overflow-hidden rounded border border-gray-200 bg-gray-800 p-2">
                                                <img src="{{ asset('storage/theme-manager/' . $config->logo_light) }}"
                                                     alt="Logo Claro"
                                                     class="max-h-full max-w-full object-contain"
                                                     style="max-height: 60px !important; max-width: 120px !important; object-fit: contain !important;">
                                            </div>
                                            <button type="button"
                                                    class="reset-field-btn p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors dark:hover:bg-blue-900/20"
                                                    onclick="resetField('logo_light')"
                                                    title="Restaurar para o tema {{ $config->selected_theme ?? 'default' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </div>

                                        <label class="mt-2 flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="logo_light_delete" value="1" class="rounded">
                                            <span class="text-gray-600 dark:text-gray-400">
                                                @lang('theme-manager::app.settings.logos.delete-current')
                                            </span>
                                        </label>
                                    </div>
                                @endif

                                <x-admin::form.control-group.control
                                    type="file"
                                    name="logo_light"
                                    accept="image/*"
                                />

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.logos.light-help')
                                </p>

                                <x-admin::form.control-group.error control-name="logo_light" />
                            </x-admin::form.control-group>

                            <!-- Ícone do Logo -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.logos.icon')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Ícone quadrado para menu recolhido e mobile. Recomendado: 64x64px, PNG/SVG.</span>
                                    </span>
                                </x-admin::form.control-group.label>

                                @if($config->logo_icon)
                                    <div class="mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center justify-center h-16 w-16 overflow-hidden rounded border border-gray-200 bg-gray-50 p-1 dark:border-gray-700 dark:bg-gray-800">
                                                <img src="{{ asset('storage/theme-manager/' . $config->logo_icon) }}"
                                                     alt="Ícone do Logo"
                                                     class="h-full w-full object-contain"
                                                     style="max-height: 64px !important; max-width: 64px !important; object-fit: contain !important;">
                                            </div>
                                            <button type="button"
                                                    class="reset-field-btn p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors dark:hover:bg-blue-900/20"
                                                    onclick="resetField('logo_icon')"
                                                    title="Restaurar para o tema {{ $config->selected_theme ?? 'default' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </div>

                                        <label class="mt-2 flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="logo_icon_delete" value="1" class="rounded">
                                            <span class="text-gray-600 dark:text-gray-400">
                                                @lang('theme-manager::app.settings.logos.delete-current')
                                            </span>
                                        </label>
                                    </div>
                                @endif

                                <x-admin::form.control-group.control
                                    type="file"
                                    name="logo_icon"
                                    accept="image/*"
                                />

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.logos.icon-help')
                                </p>

                                <x-admin::form.control-group.error control-name="logo_icon" />
                            </x-admin::form.control-group>

                            <!-- Favicon -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.logos.favicon')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Ícone da aba do navegador. Recomendado: 32x32px ou 16x16px, formato .ico ou .png.</span>
                                    </span>
                                </x-admin::form.control-group.label>

                                @if($config->favicon)
                                    <div class="mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center justify-center h-8 w-8 overflow-hidden rounded border border-gray-200 bg-gray-50 p-1 dark:border-gray-700 dark:bg-gray-800">
                                                <img src="{{ asset('storage/theme-manager/' . $config->favicon) }}"
                                                     alt="Favicon"
                                                     class="h-full w-full object-contain"
                                                     style="max-height: 32px !important; max-width: 32px !important; object-fit: contain !important;">
                                            </div>
                                            <button type="button"
                                                    class="reset-field-btn p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors dark:hover:bg-blue-900/20"
                                                    onclick="resetField('favicon')"
                                                    title="Restaurar para o tema {{ $config->selected_theme ?? 'default' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </div>

                                        <label class="mt-2 flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="favicon_delete" value="1" class="rounded">
                                            <span class="text-gray-600 dark:text-gray-400">
                                                @lang('theme-manager::app.settings.logos.delete-current')
                                            </span>
                                        </label>
                                    </div>
                                @endif

                                <x-admin::form.control-group.control
                                    type="file"
                                    name="favicon"
                                    accept="image/*,.ico"
                                />

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.logos.favicon-help')
                                </p>

                                <x-admin::form.control-group.error control-name="favicon" />
                            </x-admin::form.control-group>
                        </div>
                    </div>

                    <!-- SEÇÃO 4 - PÁGINA DE LOGIN (BACKGROUND) -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            🔐 @lang('theme-manager::app.settings.login.title')
                        </p>

                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                            @lang('theme-manager::app.settings.login.description')
                        </p>

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Background Image -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login.bg-image')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Imagem de fundo da página de login. Recomendado: alta resolução (1920x1080+), JPG/PNG.</span>
                                    </span>
                                </x-admin::form.control-group.label>

                                @if($config->login_bg_image)
                                    <div class="mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-32 h-20 overflow-hidden rounded border border-gray-200 dark:border-gray-700">
                                                <img src="{{ asset('storage/theme-manager/' . $config->login_bg_image) }}"
                                                     alt="Login Background"
                                                     class="h-full w-full object-cover"
                                                     style="max-height: 80px !important; max-width: 128px !important; object-fit: cover !important;">
                                            </div>
                                            <button type="button"
                                                    class="reset-field-btn p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors dark:hover:bg-blue-900/20"
                                                    onclick="resetField('login_bg_image')"
                                                    title="Restaurar para o tema {{ $config->selected_theme ?? 'default' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </div>

                                        <label class="mt-2 flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="login_bg_image_delete" value="1" class="rounded">
                                            <span class="text-gray-600 dark:text-gray-400">
                                                @lang('theme-manager::app.settings.logos.delete-current')
                                            </span>
                                        </label>
                                    </div>
                                @endif

                                <x-admin::form.control-group.control
                                    type="file"
                                    name="login_bg_image"
                                    accept="image/*"
                                />

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.login.bg-image-help')
                                </p>

                                <x-admin::form.control-group.error control-name="login_bg_image" />
                            </x-admin::form.control-group>

                            <!-- Background Zoom (Slider) -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login.bg-zoom')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Zoom da imagem de fundo. 100% = tamanho original. Valores maiores ampliam a imagem.</span>
                                    </span>
                                    <span id="login_bg_zoom_value" class="ml-2 text-blue-600 dark:text-blue-400">{{ old('login_bg_zoom', $config->login_bg_zoom ?? 100) }}%</span>
                                </x-admin::form.control-group.label>

                                <input
                                    type="range"
                                    name="login_bg_zoom"
                                    id="login_bg_zoom"
                                    min="50"
                                    max="150"
                                    step="5"
                                    value="{{ old('login_bg_zoom', $config->login_bg_zoom ?? 100) }}"
                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                                    oninput="document.getElementById('login_bg_zoom_value').textContent = this.value + '%'"
                                />

                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span>50%</span>
                                    <span>100%</span>
                                    <span>150%</span>
                                </div>

                                <x-admin::form.control-group.error control-name="login_bg_zoom" />
                            </x-admin::form.control-group>

                            <!-- Background Opacity (Slider) -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login.bg-opacity')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Opacidade da imagem. 0% = invisível, 100% = totalmente visível. Use valores menores para destacar o card de login.</span>
                                    </span>
                                    <span id="login_bg_opacity_value" class="ml-2 text-blue-600 dark:text-blue-400">{{ old('login_bg_opacity', $config->login_bg_opacity ?? 50) }}%</span>
                                </x-admin::form.control-group.label>

                                <input
                                    type="range"
                                    name="login_bg_opacity"
                                    id="login_bg_opacity"
                                    min="0"
                                    max="100"
                                    step="5"
                                    value="{{ old('login_bg_opacity', $config->login_bg_opacity ?? 50) }}"
                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                                    oninput="document.getElementById('login_bg_opacity_value').textContent = this.value + '%'"
                                />

                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span>0% (oculto)</span>
                                    <span>50%</span>
                                    <span>100% (visível)</span>
                                </div>

                                <x-admin::form.control-group.error control-name="login_bg_opacity" />
                            </x-admin::form.control-group>

                            <!-- Show Powered By -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login.show-powered-by')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Exibe o texto "Powered by Krayin" no rodapé da página de login. Desative para remover.</span>
                                    </span>
                                </x-admin::form.control-group.label>

                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="hidden"
                                        name="login_show_powered_by"
                                        value="0"
                                    />
                                    <input
                                        type="checkbox"
                                        name="login_show_powered_by"
                                        value="1"
                                        class="sr-only peer"
                                        {{ old('login_show_powered_by', $config->login_show_powered_by ?? true) ? 'checked' : '' }}
                                    />
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-300">
                                        Exibir "Powered by Krayin"
                                    </span>
                                </label>

                                <x-admin::form.control-group.error control-name="login_show_powered_by" />
                            </x-admin::form.control-group>
                        </div>
                    </div>

                    <!-- SEÇÃO 5 - CAIXA DE LOGIN CUSTOMIZADA -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            🎴 @lang('theme-manager::app.settings.login-card.section-title')
                        </p>

                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                            @lang('theme-manager::app.settings.login-card.description')
                        </p>

                        <!-- Toggle Card Enabled -->
                        <x-admin::form.control-group class="mb-6">
                            <x-admin::form.control-group.label>
                                @lang('theme-manager::app.settings.login-card.enabled')
                                <span class="theme-tooltip">
                                    <span class="theme-tooltip-icon">i</span>
                                    <span class="theme-tooltip-content">Ativa customização avançada do card de login com imagem de fundo, título e cores personalizadas.</span>
                                </span>
                            </x-admin::form.control-group.label>

                            <label class="relative inline-flex items-center cursor-pointer">
                                <input
                                    type="hidden"
                                    name="login_card_enabled"
                                    value="0"
                                />
                                <input
                                    type="checkbox"
                                    name="login_card_enabled"
                                    id="login_card_enabled"
                                    value="1"
                                    class="sr-only peer"
                                    {{ old('login_card_enabled', $config->login_card_enabled ?? false) ? 'checked' : '' }}
                                    onchange="toggleLoginCardOptions()"
                                />
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-300">
                                    Ativar card customizado no login
                                </span>
                            </label>

                            <x-admin::form.control-group.error control-name="login_card_enabled" />
                        </x-admin::form.control-group>

                        <div id="login-card-options" class="grid grid-cols-1 gap-6" style="{{ old('login_card_enabled', $config->login_card_enabled ?? false) ? '' : 'display: none;' }}">
                            <!-- Card Background Image -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login-card.bg-image')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Imagem de fundo aplicada diretamente ao card de login. Combinada com overlay para melhor legibilidade.</span>
                                    </span>
                                </x-admin::form.control-group.label>

                                @if($config->login_card_bg_image)
                                    <div class="mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-32 h-20 overflow-hidden rounded border border-gray-200 dark:border-gray-700">
                                                <img src="{{ asset('storage/theme-manager/' . $config->login_card_bg_image) }}"
                                                     alt="Login Card Background"
                                                     class="h-full w-full object-cover"
                                                     style="max-height: 80px !important; max-width: 128px !important; object-fit: cover !important;">
                                            </div>
                                            <button type="button"
                                                    class="reset-field-btn p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors dark:hover:bg-blue-900/20"
                                                    onclick="resetField('login_card_bg_image')"
                                                    title="Restaurar para o tema {{ $config->selected_theme ?? 'default' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </div>

                                        <label class="mt-2 flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="login_card_bg_image_delete" value="1" class="rounded">
                                            <span class="text-gray-600 dark:text-gray-400">
                                                @lang('theme-manager::app.settings.logos.delete-current')
                                            </span>
                                        </label>
                                    </div>
                                @endif

                                <x-admin::form.control-group.control
                                    type="file"
                                    name="login_card_bg_image"
                                    accept="image/*"
                                />

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.login-card.bg-image-help')
                                </p>

                                <x-admin::form.control-group.error control-name="login_card_bg_image" />
                            </x-admin::form.control-group>

                            <!-- Card Background Opacity (Slider) -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login-card.bg-opacity')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Controla a visibilidade da imagem de fundo do card. Valores menores deixam o overlay mais evidente.</span>
                                    </span>
                                    <span id="login_card_bg_opacity_value" class="ml-2 text-blue-600 dark:text-blue-400">{{ old('login_card_bg_opacity', $config->login_card_bg_opacity ?? 62) }}%</span>
                                </x-admin::form.control-group.label>

                                <input
                                    type="range"
                                    name="login_card_bg_opacity"
                                    id="login_card_bg_opacity"
                                    min="0"
                                    max="100"
                                    step="5"
                                    value="{{ old('login_card_bg_opacity', $config->login_card_bg_opacity ?? 62) }}"
                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                                    oninput="document.getElementById('login_card_bg_opacity_value').textContent = this.value + '%'"
                                />

                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span>0%</span>
                                    <span>50%</span>
                                    <span>100%</span>
                                </div>

                                <x-admin::form.control-group.error control-name="login_card_bg_opacity" />
                            </x-admin::form.control-group>

                            <!-- Overlay Color - Movido para Editor de Cores acima -->
                            <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <p class="text-xs text-blue-700 dark:text-blue-300 flex items-center gap-2">
                                    <span>💡</span>
                                    <span>A cor de overlay do card agora é editada na seção <strong>"Cores do Tema"</strong> acima, junto com as outras cores do sistema.</span>
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Title -->
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>
                                        @lang('theme-manager::app.settings.login-card.welcome-title')
                                        <span class="theme-tooltip">
                                            <span class="theme-tooltip-icon">i</span>
                                            <span class="theme-tooltip-content">Título principal exibido acima do formulário de login.</span>
                                        </span>
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="login_card_title"
                                        :value="old('login_card_title', $config->login_card_title ?? 'Bem-vindo')"
                                        placeholder="Bem-vindo"
                                    />

                                    <x-admin::form.control-group.error control-name="login_card_title" />
                                </x-admin::form.control-group>

                                <!-- Subtitle -->
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>
                                        @lang('theme-manager::app.settings.login-card.subtitle')
                                        <span class="theme-tooltip">
                                            <span class="theme-tooltip-icon">i</span>
                                            <span class="theme-tooltip-content">Texto secundário abaixo do título, para contextualizar o usuário.</span>
                                        </span>
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="login_card_subtitle"
                                        :value="old('login_card_subtitle', $config->login_card_subtitle ?? 'Acesse sua conta para continuar')"
                                        placeholder="Acesse sua conta para continuar"
                                    />

                                    <x-admin::form.control-group.error control-name="login_card_subtitle" />
                                </x-admin::form.control-group>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Sparkles Toggle -->
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>
                                        @lang('theme-manager::app.settings.login-card.sparkles')
                                        <span class="theme-tooltip">
                                            <span class="theme-tooltip-icon">i</span>
                                            <span class="theme-tooltip-content">Adiciona partículas brilhantes animadas sobre o card. Efeito decorativo sutil.</span>
                                        </span>
                                    </x-admin::form.control-group.label>

                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="login_card_sparkles" value="0" />
                                        <input
                                            type="checkbox"
                                            name="login_card_sparkles"
                                            value="1"
                                            class="sr-only peer"
                                            {{ old('login_card_sparkles', $config->login_card_sparkles ?? false) ? 'checked' : '' }}
                                        />
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-300">
                                            Efeito de partículas
                                        </span>
                                    </label>

                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        @lang('theme-manager::app.settings.login-card.sparkles-help')
                                    </p>

                                    <x-admin::form.control-group.error control-name="login_card_sparkles" />
                                </x-admin::form.control-group>

                                <!-- Help Link Toggle -->
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>
                                        @lang('theme-manager::app.settings.login-card.help-link')
                                        <span class="theme-tooltip">
                                            <span class="theme-tooltip-icon">i</span>
                                            <span class="theme-tooltip-content">Exibe link "Precisa de ajuda?" com email de suporte configurado abaixo.</span>
                                        </span>
                                    </x-admin::form.control-group.label>

                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="login_card_help_link" value="0" />
                                        <input
                                            type="checkbox"
                                            name="login_card_help_link"
                                            value="1"
                                            class="sr-only peer"
                                            {{ old('login_card_help_link', $config->login_card_help_link ?? true) ? 'checked' : '' }}
                                        />
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-600 dark:text-gray-300">
                                            Exibir link de ajuda
                                        </span>
                                    </label>

                                    <x-admin::form.control-group.error control-name="login_card_help_link" />
                                </x-admin::form.control-group>
                            </div>

                            <!-- Support Email -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login-card.support-email')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Email de suporte exibido quando o link de ajuda estiver ativado. Clicável como mailto.</span>
                                    </span>
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="email"
                                    name="login_card_support_email"
                                    :value="old('login_card_support_email', $config->login_card_support_email ?? 'suporte@empresa.com.br')"
                                    placeholder="suporte@empresa.com.br"
                                />

                                <x-admin::form.control-group.error control-name="login_card_support_email" />
                            </x-admin::form.control-group>
                        </div>
                    </div>

                    <!-- SEÇÃO 6 - EMPTY STATES -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            📭 @lang('theme-manager::app.settings.empty-states.title')
                        </p>

                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                            @lang('theme-manager::app.settings.empty-states.description')
                        </p>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            @foreach(['activities', 'calls', 'emails', 'meetings', 'notes', 'organizations', 'persons', 'leads', 'products'] as $emptyState)
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label>
                                        @lang('theme-manager::app.settings.empty-states.' . $emptyState)
                                    </x-admin::form.control-group.label>

                                    @if($config->{'empty_state_' . $emptyState})
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/theme-manager/' . $config->{'empty_state_' . $emptyState}) }}"
                                                 alt="{{ ucfirst($emptyState) }} Empty State"
                                                 class="h-24 w-24 rounded border border-gray-200 bg-gray-50 p-2 dark:border-gray-700 dark:bg-gray-800">

                                            <label class="mt-2 flex items-center gap-2 text-sm">
                                                <input type="checkbox" name="empty_state_{{ $emptyState }}_delete" value="1" class="rounded">
                                                <span class="text-gray-600 dark:text-gray-400">
                                                    @lang('theme-manager::app.settings.logos.delete-current')
                                                </span>
                                            </label>
                                        </div>
                                    @endif

                                    <x-admin::form.control-group.control
                                        type="file"
                                        name="empty_state_{{ $emptyState }}"
                                        accept=".svg,image/svg+xml"
                                    />

                                    <x-admin::form.control-group.error control-name="empty_state_{{ $emptyState }}" />
                                </x-admin::form.control-group>
                            @endforeach
                        </div>
                    </div>

                    </div>
                    <!-- FIM SEÇÃO 2: PERSONALIZAÇÕES -->

                </div>
            </div>
        </div>
    </x-admin::form>

    {{-- ================================================================
         BARRA STICKY: "Alterações pendentes" + Undo
         Aparece quando o usuário seleciona um tema diferente do atual
         ================================================================ --}}
    <div id="krPendingBar"
         role="alert"
         aria-live="polite"
         style="display:none; position:fixed; bottom:0; left:0; right:0; z-index:9999; background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color:#fff; padding:12px 24px; box-shadow:0 -4px 20px rgba(0,0,0,0.15); align-items:center; justify-content:space-between; gap:16px;">

        {{-- Indicador de alteração pendente --}}
        <div style="display:flex; align-items:center; gap:12px;">
            <span style="font-size:20px;">⚠️</span>
            <span class="kr-pending-text" style="font-weight:600; font-size:14px;">
                Alterações pendentes
            </span>
        </div>

        {{-- Botões de ação --}}
        <div style="display:flex; gap:10px; align-items:center;">
            {{-- Botão Desfazer --}}
            <button type="button"
                    onclick="window.krUndoThemeSelection(); return false;"
                    style="padding:8px 16px; border-radius:6px; border:2px solid rgba(255,255,255,0.4); background:transparent; color:#fff; font-weight:600; font-size:13px; cursor:pointer; transition:all 0.15s ease; display:flex; align-items:center; gap:6px;"
                    onmouseenter="this.style.background='rgba(255,255,255,0.15)'"
                    onmouseleave="this.style.background='transparent'">
                <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                Desfazer
            </button>

            {{-- Botão Salvar Agora --}}
            <button type="button"
                    onclick="window.krSaveWithConfirm(); return false;"
                    style="padding:8px 20px; border-radius:6px; border:none; background:#fff; color:#d97706; font-weight:700; font-size:13px; cursor:pointer; transition:all 0.15s ease; display:flex; align-items:center; gap:6px; box-shadow:0 2px 8px rgba(0,0,0,0.15);"
                    onmouseenter="this.style.transform='scale(1.02)'"
                    onmouseleave="this.style.transform='scale(1)'">
                <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Salvar Agora
            </button>
        </div>
    </div>

    {{-- ================================================================
         NOVO MODAL DE PREVIEW DO TEMA - #krThemePreviewModal
         Fullscreen, lazy-load de imagem, fallback robusto, zoom 1x/2x
         A11y: role="dialog", aria-modal, aria-labelledby, focus trap
         ================================================================ --}}
    <div id="krThemePreviewModal"
         role="dialog"
         aria-modal="true"
         aria-hidden="true"
         aria-labelledby="krThemePreviewTitle"
         tabindex="-1"
         style="position:fixed; inset:0; z-index:2147483647; display:none; background:rgba(0,0,0,.85);">

        {{-- Overlay clicável para fechar --}}
        <div id="krThemePreviewOverlay"
             onclick="window.krCloseThemePreview(); return false;"
             style="position:absolute; inset:0; cursor:pointer;"
             aria-hidden="true"></div>

        {{-- Painel do Modal - stopPropagation para não fechar ao clicar dentro --}}
        <div id="krThemePreviewPanel"
             onclick="event.stopPropagation();"
             tabindex="-1"
             style="position:relative; width:100%; height:100%; max-width:1400px; margin:0 auto; background:#0f172a; border-radius:0; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 25px 80px rgba(0,0,0,.6);">

            {{-- Header --}}
            <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 20px; background:rgba(255,255,255,.03); border-bottom:1px solid rgba(255,255,255,.08); flex-shrink:0;">
                {{-- Título do tema --}}
                <div style="display:flex; align-items:center; gap:12px;">
                    <span style="font-size:20px;">🎨</span>
                    <div id="krThemePreviewTitle" style="color:#fff; font-size:18px; font-weight:700;">
                        Preview do Tema
                    </div>
                </div>

                {{-- Botões de ação --}}
                <div style="display:flex; gap:10px; align-items:center;">
                    {{-- Toggle Zoom 1x/2x --}}
                    <button id="krThemePreviewZoomBtn"
                            type="button"
                            onclick="window.krTogglePreviewZoom(); return false;"
                            style="padding:8px 14px; border-radius:8px; border:1px solid rgba(255,255,255,0.15); background:rgba(255,255,255,0.06); color:#94a3b8; font-weight:600; font-size:13px; cursor:pointer; transition:all 0.15s ease; display:flex; align-items:center; gap:6px;">
                        <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                        </svg>
                        <span id="krZoomLabel">1x</span>
                    </button>

                    {{-- Botão "Aplicar este tema" (NÃO submete form) --}}
                    <button id="krThemePreviewApplyBtn"
                            type="button"
                            onclick="window.krApplyThemeFromPreview(); return false;"
                            style="padding:10px 18px; border-radius:8px; border:none; background:linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color:#fff; font-weight:700; font-size:14px; cursor:pointer; transition:all 0.2s ease; display:flex; align-items:center; gap:8px; box-shadow:0 4px 12px rgba(34,197,94,0.3);">
                        <svg style="width:16px; height:16px;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Aplicar este tema
                    </button>

                    {{-- Botão Fechar --}}
                    <button id="krThemePreviewCloseBtn"
                            type="button"
                            onclick="window.krCloseThemePreview(); return false;"
                            style="padding:10px 16px; border-radius:8px; border:1px solid rgba(255,255,255,0.15); background:rgba(255,255,255,0.06); color:#fff; font-weight:600; font-size:14px; cursor:pointer; transition:all 0.15s ease;">
                        ✕ Fechar
                    </button>
                </div>
            </div>

            {{-- Body - Preview Area --}}
            <div id="krThemePreviewBody" style="position:relative; flex:1; overflow:auto; padding:20px; display:flex; align-items:center; justify-content:center; background:#0a0f1a;">

                {{-- Botão ANTERIOR (← ) --}}
                <button id="krThemePreviewPrevBtn"
                        type="button"
                        onclick="window.krNavigateTheme(-1); return false;"
                        style="position:absolute; left:12px; top:50%; transform:translateY(-50%); z-index:100; width:48px; height:48px; border-radius:50%; border:1px solid rgba(255,255,255,0.2); background:rgba(0,0,0,0.5); color:#fff; font-size:20px; cursor:pointer; transition:all 0.2s ease; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(4px);"
                        onmouseenter="this.style.background='rgba(59,130,246,0.7)'; this.style.transform='translateY(-50%) scale(1.1)';"
                        onmouseleave="this.style.background='rgba(0,0,0,0.5)'; this.style.transform='translateY(-50%) scale(1)';"
                        title="Tema anterior (←)">
                    <svg style="width:24px; height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                {{-- Botão PRÓXIMO (→) --}}
                <button id="krThemePreviewNextBtn"
                        type="button"
                        onclick="window.krNavigateTheme(1); return false;"
                        style="position:absolute; right:12px; top:50%; transform:translateY(-50%); z-index:100; width:48px; height:48px; border-radius:50%; border:1px solid rgba(255,255,255,0.2); background:rgba(0,0,0,0.5); color:#fff; font-size:20px; cursor:pointer; transition:all 0.2s ease; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(4px);"
                        onmouseenter="this.style.background='rgba(59,130,246,0.7)'; this.style.transform='translateY(-50%) scale(1.1)';"
                        onmouseleave="this.style.background='rgba(0,0,0,0.5)'; this.style.transform='translateY(-50%) scale(1)';"
                        title="Próximo tema (→)">
                    <svg style="width:24px; height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                {{-- Loading spinner (mostrado durante carregamento) --}}
                <div id="krThemePreviewLoading" style="display:none; text-align:center;">
                    <div style="width:48px; height:48px; border:4px solid rgba(255,255,255,0.1); border-top-color:#3b82f6; border-radius:50%; animation:krSpin 0.8s linear infinite; margin:0 auto 16px;"></div>
                    <div style="color:#94a3b8; font-size:14px;">Carregando preview...</div>
                </div>

                {{-- Imagem do Preview (lazy-load: src setado ao abrir) --}}
                <img id="krThemePreviewImg"
                     alt="Theme preview"
                     style="max-width:100%; max-height:100%; height:auto; display:none; border-radius:12px; border:1px solid rgba(255,255,255,.08); box-shadow:0 8px 32px rgba(0,0,0,.4); cursor:zoom-in; transition:transform 0.25s ease;">

                {{-- Fallback quando imagem não existe (404/error) --}}
                <div id="krThemePreviewFallback"
                     style="display:none; width:100%; max-width:700px; min-height:400px; border-radius:16px; border:1px solid rgba(255,255,255,.1);
                            padding:60px 40px; text-align:center; position:relative; overflow:hidden;">

                    {{-- Gradiente de fundo dinâmico --}}
                    <div id="krThemePreviewGradient" style="position:absolute; inset:0; border-radius:16px; opacity:0.9;"></div>

                    {{-- Padrão decorativo --}}
                    <div style="position:absolute; inset:0; opacity:0.06; background-image:radial-gradient(circle, rgba(255,255,255,0.4) 1px, transparent 1px); background-size:20px 20px;"></div>

                    {{-- Efeito de luz --}}
                    <div style="position:absolute; top:-30%; left:-30%; width:160%; height:160%; background:radial-gradient(circle at 30% 30%, rgba(255,255,255,0.12), transparent 50%); pointer-events:none;"></div>

                    <div style="position:relative; z-index:10;">
                        {{-- Ícone --}}
                        <div style="font-size:64px; margin-bottom:20px; opacity:0.9;">🎨</div>

                        {{-- Nome do tema --}}
                        <div id="krThemePreviewFallbackName"
                             style="color:#fff; font-size:36px; font-weight:800; margin-bottom:12px; text-shadow:0 4px 20px rgba(0,0,0,0.5); letter-spacing:-0.01em;">
                            Tema
                        </div>

                        {{-- Mensagem --}}
                        <div style="color:rgba(255,255,255,0.75); font-size:16px; margin-bottom:32px; font-weight:500;">
                            Preview indisponível para este tema
                        </div>

                        {{-- Círculos de cores --}}
                        <div id="krThemePreviewFallbackColors" style="display:flex; justify-content:center; gap:16px; flex-wrap:wrap;">
                            {{-- Inseridos via JS --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CSS do spinner --}}
    <style>
    @keyframes krSpin {
        to { transform: rotate(360deg); }
    }
    </style>

    @pushOnce('scripts')
<script>
(function() {
    // Dados de temas pré-processados no Controller
    window.themeData = {!! json_encode($themesForJs) !!};

    console.log('✅ ThemeData initialized:', window.themeData);
    console.log('=== THEME COLORS VERIFICATION ===');
    Object.keys(window.themeData).forEach(function(key) {
        const colors = window.themeData[key].colors;
        console.log('%c ' + key + ': ' + colors.primary + ' ', 'background: ' + colors.primary + '; color: white; padding: 2px 5px;');
    });
    console.log('=================================');

    window.toggleLoginCardOptions = function() {
        const checkbox = document.getElementById('login_card_enabled');
        const options = document.getElementById('login-card-options');
        if (checkbox && options) options.style.display = checkbox.checked ? 'grid' : 'none';
    };

    // NOTA: updateOverlayColorPreview removida - agora gerenciada pelo krColorEditor

    // Reset individual field to theme default
    window.resetField = function(fieldName) {
        if (!confirm('Restaurar este campo para o valor do tema selecionado?')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.settings.theme.reset-field") }}';

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        const fieldInput = document.createElement('input');
        fieldInput.type = 'hidden';
        fieldInput.name = 'field_name';
        fieldInput.value = fieldName;
        form.appendChild(fieldInput);

        document.body.appendChild(form);
        form.submit();
    };

    document.addEventListener('DOMContentLoaded', function() {
        toggleLoginCardOptions();
        // NOTA: Sync de color pickers agora é gerenciado pelo krColorEditor (Editor de Tokens de Cor)
        console.log('🎨 Legacy color picker init skipped - using krColorEditor');
    });
})();
</script>
@endPushOnce

{{-- ════════════════════════════════════════════════════════════════════════════
     LÓGICA AUXILIAR: Confirmação de troca de tema e sync inicial
     As funções principais estão no script global no início do arquivo
     ════════════════════════════════════════════════════════════════════════════ --}}
<script>
(function() {
    'use strict';

    console.log('🔧 Theme Helper Script Loading...');

    function initThemeHelpers() {
        console.log('🔧 Initializing Theme Helpers...');

        // Sync quando mudar radio (via label ou botão)
        document.addEventListener('change', function(e) {
            if (e.target && e.target.matches('input[type="radio"][name="selected_theme"]')) {
                if (typeof window.syncThemeButtonStates === 'function') {
                    window.syncThemeButtonStates();
                }
            }
        });

        // ═══════════════════════════════════════════════════════════
        // ESTADO DIRTY: rastreia se há alteração pendente de save
        // ═══════════════════════════════════════════════════════════
        var initialTheme = document.querySelector('input[type="radio"][name="selected_theme"]:checked');
        window.__krInitialThemeValue = initialTheme ? initialTheme.value : 'default';
        window.__krInitialThemeName = initialTheme ? (initialTheme.dataset.themeName || 'Default') : 'Default';
        window.__krDirtyThemeSelection = false;

        // Detectar mudança de tema e marcar como dirty
        document.addEventListener('change', function(e) {
            if (e.target && e.target.matches('input[type="radio"][name="selected_theme"]')) {
                var newValue = e.target.value;
                var newName = e.target.dataset.themeName || 'Tema';

                // Marcar dirty se diferente do inicial
                if (newValue !== window.__krInitialThemeValue) {
                    window.__krDirtyThemeSelection = true;
                    window.__krPendingThemeValue = newValue;
                    window.__krPendingThemeName = newName;
                } else {
                    window.__krDirtyThemeSelection = false;
                    window.__krPendingThemeValue = null;
                    window.__krPendingThemeName = null;
                }

                // Atualizar barra sticky
                krUpdatePendingBar();
            }
        });

        // ═══════════════════════════════════════════════════════════
        // BARRA STICKY: "Alterações pendentes" com Undo
        // ═══════════════════════════════════════════════════════════
        function krUpdatePendingBar() {
            var bar = document.getElementById('krPendingBar');
            if (!bar) return;

            if (window.__krDirtyThemeSelection) {
                var pendingName = window.__krPendingThemeName || 'Novo tema';
                bar.querySelector('.kr-pending-text').textContent =
                    'Tema selecionado: ' + pendingName + ' (não salvo)';
                bar.style.display = 'flex';
                bar.style.opacity = '0';
                requestAnimationFrame(function() {
                    bar.style.transition = 'opacity 0.3s ease';
                    bar.style.opacity = '1';
                });
            } else {
                bar.style.opacity = '0';
                setTimeout(function() { bar.style.display = 'none'; }, 300);
            }
        }
        window.krUpdatePendingBar = krUpdatePendingBar;

        // Undo: volta para tema original
        window.krUndoThemeSelection = function() {
            var originalRadio = document.querySelector('input[type="radio"][name="selected_theme"][value="' + window.__krInitialThemeValue + '"]');
            if (originalRadio) {
                window.__krSkipConfirm = true;
                try {
                    originalRadio.checked = true;
                    originalRadio.dispatchEvent(new Event('change', { bubbles: true }));
                } finally {
                    window.__krSkipConfirm = false;
                }
            }
            window.__krDirtyThemeSelection = false;
            krUpdatePendingBar();
            if (typeof window.syncThemeButtonStates === 'function') {
                window.syncThemeButtonStates();
            }
        };

        // ═══════════════════════════════════════════════════════════
        // CONFIRM NO SUBMIT DO FORM (blindagem contra Enter, etc)
        // ═══════════════════════════════════════════════════════════
        var themeForm = document.querySelector('form[action*="theme"]');
        if (themeForm) {
            themeForm.addEventListener('submit', function(e) {
                // Só mostra confirm se tiver mudança de tema pendente
                if (window.__krDirtyThemeSelection) {
                    var pendingName = window.__krPendingThemeName || 'Novo tema';
                    var initialName = window.__krInitialThemeName || 'Tema anterior';

                    var confirmMessage =
                        '⚠️ CONFIRMAR ALTERAÇÃO DE TEMA\n\n' +
                        '═══════════════════════════════════\n' +
                        'Tema Atual:    ' + initialName + '\n' +
                        'Novo Tema:     ' + pendingName + '\n' +
                        '═══════════════════════════════════\n\n' +
                        '⚠️ ATENÇÃO:\n' +
                        '• Suas customizações atuais serão substituídas\n' +
                        '• Cores, logos e configurações serão redefinidas\n' +
                        '• Você pode reverter usando "Voltar Tema Anterior"\n\n' +
                        'Deseja continuar com a alteração?';

                    if (!confirm(confirmMessage)) {
                        e.preventDefault();
                        return false;
                    }

                    // Se confirmou, limpar dirty para não triggar beforeunload
                    window.__krDirtyThemeSelection = false;
                }
                // Se não está dirty, submete normal
            });
        }

        // Atalho: "Salvar Agora" da barra sticky apenas dispara submit
        window.krSaveWithConfirm = function() {
            var form = document.querySelector('form[action*="theme"]');
            if (form) {
                // requestSubmit dispara o evento submit (diferente de form.submit())
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    // Fallback para browsers antigos
                    form.submit();
                }
            }
        };

        // ═══════════════════════════════════════════════════════════
        // BEFOREUNLOAD: Aviso ao tentar sair com alterações pendentes
        // ═══════════════════════════════════════════════════════════
        window.addEventListener('beforeunload', function(e) {
            if (window.__krDirtyThemeSelection) {
                var msg = 'Você tem alterações de tema não salvas. Deseja realmente sair?';
                e.returnValue = msg;
                return msg;
            }
        });

        // Sync inicial
        if (typeof window.syncThemeButtonStates === 'function') {
            window.syncThemeButtonStates();
        }

        // ═══════════════════════════════════════════════════════════
        // DENSIDADE DO GRID - Toggle Compact/Normal com persistência
        // ═══════════════════════════════════════════════════════════
        var DENSITY_KEY = 'kr_theme_grid_compact';

        function krApplyDensity(compact) {
            if (compact) {
                document.body.classList.add('kr-theme-compact');
            } else {
                document.body.classList.remove('kr-theme-compact');
            }

            var btn = document.getElementById('krDensityToggle');
            var label = document.getElementById('krDensityLabel');

            if (btn) {
                if (compact) {
                    btn.classList.add('kr-active');
                } else {
                    btn.classList.remove('kr-active');
                }
            }

            if (label) {
                label.textContent = compact ? 'Normal' : 'Compacto';
            }
        }

        // Restaurar preferência salva
        var savedDensity = localStorage.getItem(DENSITY_KEY);
        if (savedDensity === 'true') {
            krApplyDensity(true);
        }

        window.krToggleDensity = function() {
            var isCompact = document.body.classList.contains('kr-theme-compact');
            var newState = !isCompact;

            krApplyDensity(newState);

            // Persistir
            try {
                localStorage.setItem(DENSITY_KEY, newState ? 'true' : 'false');
            } catch(e) {
                console.warn('⚠️ Não foi possível salvar preferência de densidade');
            }

            console.log('[KR] Densidade:', newState ? 'compact' : 'normal');
        };

        console.log('✅ Theme Helpers Initialized');
    }

    // Inicializar quando DOM carregar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initThemeHelpers);
    } else {
        initThemeHelpers();
    }

})();
</script>

{{-- ════════════════════════════════════════════════════════════════════════════
     LIVE PREVIEW - Hover para mostrar cores do tema em tempo real
     Aplica CSS variables no hover/focus dos cards de tema
     ════════════════════════════════════════════════════════════════════════════ --}}
<script>
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        console.log('✅ Theme hover preview JS loaded');

        // Selecionar todos os card wrappers
        var cardWrappers = document.querySelectorAll('.theme-card-wrapper[data-primary]');

        if (!cardWrappers.length) {
            console.warn('⚠️ No theme card wrappers found');
            return;
        }

        console.log('🎨 Found ' + cardWrappers.length + ' theme cards for hover preview');

        cardWrappers.forEach(function(wrapper) {
            var livePreview = wrapper.querySelector('.kr-live-preview');
            if (!livePreview) return;

            // Extrair cores do dataset
            var primary = wrapper.dataset.primary || '#1E40AF';
            var success = wrapper.dataset.success || '#10B981';
            var warning = wrapper.dataset.warning || '#F59E0B';
            var danger = wrapper.dataset.danger || '#EF4444';

            // Aplicar variáveis CSS no próprio wrapper (as classes CSS já usam var())
            wrapper.style.setProperty('--p', primary);
            wrapper.style.setProperty('--s', success);
            wrapper.style.setProperty('--w', warning);
            wrapper.style.setProperty('--d', danger);

            // Também aplicar no livePreview para garantir
            livePreview.style.setProperty('--p', primary);
            livePreview.style.setProperty('--s', success);
            livePreview.style.setProperty('--w', warning);
            livePreview.style.setProperty('--d', danger);

            // Handlers de hover para debug (opcional, as cores já estão aplicadas via CSS)
            wrapper.addEventListener('mouseenter', function() {
                // Apenas log para debug - a animação é via CSS puro
                // console.log('🎨 Hovering:', wrapper.dataset.themeSlug, primary);
            });

            // Suporte a focus/blur para acessibilidade (tab navigation)
            wrapper.addEventListener('focusin', function() {
                wrapper.classList.add('kr-focused');
            });

            wrapper.addEventListener('focusout', function() {
                wrapper.classList.remove('kr-focused');
            });
        });

        console.log('✅ Theme hover preview initialized');
    });
})();
</script>

{{-- ════════════════════════════════════════════════════════════════════════════
     POINTER DnD v2 - Patch Definitivo
     - Capture phase para interceptar antes de qualquer bubble
     - Mouse fallback caso pointer seja bloqueado
     - Non-passive listeners para garantir preventDefault
     - Bloqueia dragstart nativo no document
     Janeiro 2026
     ════════════════════════════════════════════════════════════════════════════ --}}
<script>
(function () {
  var grid = document.getElementById('themeCardsGrid');
  if (!grid) {
    console.warn('🟥 PointerDnD: grid #themeCardsGrid não encontrado');
    return;
  }

  var STORAGE_KEY = 'kr_theme_order.v1';

  var state = {
    active: false,
    card: null,
    slug: null,
    ghost: null,
    offsetX: 0,
    offsetY: 0,
    pointerId: null,
  };

  function log() {
    var args = Array.prototype.slice.call(arguments);
    args.unshift('🟦 PointerDnD');
    console.log.apply(console, args);
  }

  function findHandle(evt) {
    // pega .kr-drag-handle de forma robusta (composedPath + closest)
    if (evt && typeof evt.composedPath === 'function') {
      var path = evt.composedPath() || [];
      for (var i = 0; i < path.length; i++) {
        var el = path[i];
        if (el && el.classList && el.classList.contains('kr-drag-handle')) return el;
      }
    }
    return evt.target && evt.target.closest ? evt.target.closest('.kr-drag-handle') : null;
  }

  function getSlug(card) {
    return (
      card.dataset.themeSlug ||
      card.getAttribute('data-theme-slug') ||
      (card.id ? card.id.replace('theme-card-', '') : null)
    );
  }

  function createGhost(card) {
    var rect = card.getBoundingClientRect();
    var g = card.cloneNode(true);
    g.classList.add('kr-drag-ghost');
    g.style.width = rect.width + 'px';
    g.style.height = rect.height + 'px';
    g.style.left = rect.left + 'px';
    g.style.top = rect.top + 'px';
    document.body.appendChild(g);
    return g;
  }

  function positionGhost(clientX, clientY) {
    if (!state.ghost) return;
    var x = clientX - state.offsetX;
    var y = clientY - state.offsetY;
    state.ghost.style.left = x + 'px';
    state.ghost.style.top = y + 'px';
  }

  function getCardUnderPointer(x, y) {
    if (state.ghost) state.ghost.style.display = 'none';
    var el = document.elementFromPoint(x, y);
    if (state.ghost) state.ghost.style.display = '';
    return el ? el.closest('.theme-card-wrapper') : null;
  }

  function reorder(dragCard, targetCard, pointerY) {
    var rect = targetCard.getBoundingClientRect();
    var before = pointerY < rect.top + rect.height / 2;

    if (before) {
      if (dragCard !== targetCard.previousElementSibling) {
        grid.insertBefore(dragCard, targetCard);
      }
    } else {
      if (dragCard !== targetCard.nextElementSibling) {
        grid.insertBefore(dragCard, targetCard.nextElementSibling);
      }
    }
  }

  function saveOrder() {
    var slugs = [];
    var cards = grid.querySelectorAll('.theme-card-wrapper');
    cards.forEach(function(c) {
      var s = getSlug(c);
      if (s) slugs.push(s);
    });
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(slugs));
      console.log('💾 PointerDnD order saved:', slugs);
    } catch (e) {
      console.warn('⚠️ Failed to save order:', e);
    }
  }

  function restoreOrder() {
    var savedOrder;
    try {
      var stored = localStorage.getItem(STORAGE_KEY);
      if (!stored) {
        console.log('📭 No saved order found');
        return;
      }
      savedOrder = JSON.parse(stored);
    } catch (e) {
      console.warn('⚠️ Failed to read saved order:', e);
      return;
    }

    if (!Array.isArray(savedOrder) || savedOrder.length === 0) {
      return;
    }

    console.log('📂 Restoring order:', savedOrder);

    var fragment = document.createDocumentFragment();
    var processed = {};

    savedOrder.forEach(function(slug) {
      var radio = document.getElementById('theme_' + slug);
      var card = document.getElementById('theme-card-' + slug);

      if (radio && card) {
        fragment.appendChild(radio);
        fragment.appendChild(card);
        processed[slug] = true;
      }
    });

    // Adicionar temas novos não salvos (no final)
    var allCards = grid.querySelectorAll('.theme-card-wrapper');
    allCards.forEach(function(card) {
      var slug = getSlug(card);
      if (slug && !processed[slug]) {
        var radio = document.getElementById('theme_' + slug);
        if (radio) fragment.appendChild(radio);
        fragment.appendChild(card);
      }
    });

    grid.innerHTML = '';
    grid.appendChild(fragment);
    console.log('✅ Order restored');
  }

  function startDrag(evt, clientX, clientY) {
    var handle = findHandle(evt);
    if (!handle) return;

    var card = handle.closest('.theme-card-wrapper');
    if (!card) return;

    // BLOQUEIA TUDO que possa competir (clique, label, etc.)
    evt.preventDefault();
    evt.stopPropagation();
    if (evt.stopImmediatePropagation) evt.stopImmediatePropagation();

    state.active = true;
    state.card = card;
    state.slug = getSlug(card);
    state.pointerId = evt.pointerId != null ? evt.pointerId : null;

    card.classList.add('kr-dragging');
    document.body.classList.add('kr-dragging-active');

    var rect = card.getBoundingClientRect();
    state.offsetX = clientX - rect.left;
    state.offsetY = clientY - rect.top;

    if (state.ghost) state.ghost.remove();
    state.ghost = createGhost(card);
    positionGhost(clientX, clientY);

    // pointer capture (quando existir)
    try {
      if (evt.pointerId != null && handle.setPointerCapture) {
        handle.setPointerCapture(evt.pointerId);
      }
    } catch (e) {}

    log('start', { slug: state.slug, pointerId: state.pointerId });
  }

  function moveDrag(evt, clientX, clientY) {
    if (!state.active) return;

    // precisa ser não-passivo
    evt.preventDefault();

    // performance: rAF
    window.requestAnimationFrame(function() {
      positionGhost(clientX, clientY);

      var target = getCardUnderPointer(clientX, clientY);
      if (target && target !== state.card) {
        reorder(state.card, target, clientY);
      }
    });
  }

  function endDrag(cancelled) {
    if (!state.active) return;

    if (state.ghost) {
      state.ghost.remove();
      state.ghost = null;
    }

    if (state.card) {
      state.card.classList.remove('kr-dragging');
    }

    document.body.classList.remove('kr-dragging-active');

    log('end', { slug: state.slug, cancelled: cancelled });

    state.active = false;
    state.card = null;
    state.slug = null;
    state.pointerId = null;

    if (!cancelled) saveOrder();
  }

  // Restaurar ordem salva no load
  restoreOrder();

  // --- LISTENERS (CAPTURE PHASE) ---
  // POINTER
  document.addEventListener('pointerdown', function(e) { startDrag(e, e.clientX, e.clientY); }, true);
  document.addEventListener('pointermove', function(e) { moveDrag(e, e.clientX, e.clientY); }, { capture: true, passive: false });
  document.addEventListener('pointerup', function() { endDrag(false); }, true);
  document.addEventListener('pointercancel', function() { endDrag(true); }, true);

  // MOUSE FALLBACK (caso pointer esteja sendo "comido")
  document.addEventListener('mousedown', function(e) {
    if (e.button !== 0) return;
    startDrag(e, e.clientX, e.clientY);
  }, true);

  document.addEventListener('mousemove', function(e) { moveDrag(e, e.clientX, e.clientY); }, { capture: true, passive: false });
  document.addEventListener('mouseup', function() { endDrag(false); }, true);

  // ESC cancela
  window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') endDrag(true);
  });

  // BLOQUEIA DRAG NATIVO (qualquer tentativa)
  document.addEventListener('dragstart', function(e) {
    var handle = e.target && e.target.closest ? e.target.closest('.kr-drag-handle') : null;
    var card = e.target && e.target.closest ? e.target.closest('.theme-card-wrapper') : null;
    if (handle || card) {
      e.preventDefault();
      e.stopPropagation();
      console.log('⛔ dragstart nativo bloqueado');
    }
  }, true);

  console.log('✅ PointerDnD v2 armed (capture + mouse fallback + non-passive)');
})();
</script>

{{-- ════════════════════════════════════════════════════════════════════════════
     NOVO SISTEMA DE PREVIEW + SELEÇÃO DE TEMAS - Janeiro 2026 (Hardened)
     Funções globais: krOpenThemePreview, krCloseThemePreview, krApplyThemeFromPreview,
                      krSelectTheme, krSyncThemeButtons, krTogglePreviewZoom
     Aliases de compatibilidade: openThemePreview, closeThemePreview, applyThemeFromPreview,
                                  syncThemeButtons, syncThemeButtonStates
     ════════════════════════════════════════════════════════════════════════════ --}}
<script>
(function() {
    'use strict';

    console.log('✅ Theme preview modal JS loaded');

    // ═══════════════════════════════════════════════════════════
    // ESTADO GLOBAL
    // ═══════════════════════════════════════════════════════════
    var currentTheme = null;
    var zoomLevel = 1; // 1 = 1x, 2 = 2x
    var previouslyFocusedElement = null; // A11y: salvar elemento focado antes de abrir

    // ═══════════════════════════════════════════════════════════
    // ABRIR MODAL DE PREVIEW
    // ═══════════════════════════════════════════════════════════
    window.krOpenThemePreview = function(btnOrEvent) {
        console.log('[KR] krOpenThemePreview chamado');

        // Extrair o elemento com dados do tema
        var btn = btnOrEvent;
        if (btnOrEvent && btnOrEvent.target) {
            // Tentar encontrar o elemento com data-theme-slug
            btn = btnOrEvent.target.closest('.kr-btn-preview') ||
                  btnOrEvent.target.closest('.theme-card-label') ||
                  btnOrEvent.target.closest('.theme-card-wrapper');
        }

        if (!btn || !btn.dataset) {
            console.error('[KR] Elemento inválido:', btn);
            return;
        }

        // Se não tem previewUrl diretamente, tentar construir
        var previewUrl = btn.dataset.previewUrl;
        if (!previewUrl && btn.dataset.themeSlug) {
            // Tentar pegar do botão preview dentro do card
            var previewBtn = document.querySelector('.kr-btn-preview[data-theme-slug="' + btn.dataset.themeSlug + '"]');
            if (previewBtn) {
                previewUrl = previewBtn.dataset.previewUrl;
            }
        }

        // Extrair dados do elemento (pode ser botão, div, ou card)
        currentTheme = {
            slug: btn.dataset.themeSlug || '',
            name: btn.dataset.themeName || 'Tema',
            radioId: btn.dataset.radioId || 'theme_' + (btn.dataset.themeSlug || ''),
            previewUrl: previewUrl || btn.dataset.previewUrl || '',
            primary: btn.dataset.primary || '#1E40AF',
            success: btn.dataset.success || '#10B981',
            warning: btn.dataset.warning || '#F59E0B',
            danger: btn.dataset.danger || '#EF4444'
        };

        console.log('[KR] Tema extraído:', currentTheme);

        // Elementos do modal
        var modal = document.getElementById('krThemePreviewModal');
        var title = document.getElementById('krThemePreviewTitle');
        var loading = document.getElementById('krThemePreviewLoading');
        var img = document.getElementById('krThemePreviewImg');
        var fallback = document.getElementById('krThemePreviewFallback');
        var zoomLabel = document.getElementById('krZoomLabel');

        if (!modal) {
            console.error('[KR] Modal #krThemePreviewModal não encontrado!');
            return;
        }

        // Reset estado completo
        zoomLevel = 1;
        if (img) {
            img.style.display = 'none';
            img.style.transform = 'scale(1)';
            img.src = ''; // Limpa antes de carregar nova
        }
        if (fallback) fallback.style.display = 'none';
        if (loading) loading.style.display = 'block';
        if (zoomLabel) zoomLabel.textContent = '1x';

        // Atualizar título
        if (title) {
            title.textContent = 'Preview: ' + currentTheme.name;
        }

        // Atualizar estado do botão Aplicar
        krUpdateApplyButton();

        // A11y: Salvar elemento previamente focado
        previouslyFocusedElement = document.activeElement;

        // Mostrar modal
        modal.style.display = 'block';
        modal.style.opacity = '0';
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        // Fade in
        requestAnimationFrame(function() {
            modal.style.transition = 'opacity 0.25s ease';
            modal.style.opacity = '1';

            // A11y: Focar no botão Fechar após animação
            var closeBtn = document.getElementById('krThemePreviewCloseBtn');
            if (closeBtn) {
                closeBtn.focus();
            }
        });

        // Carregar imagem (lazy-load)
        if (currentTheme.previewUrl && img) {
            console.log('[KR] Carregando preview:', currentTheme.previewUrl);

            img.onload = function() {
                console.log('[KR] Imagem carregada com sucesso');
                if (loading) loading.style.display = 'none';
                img.style.display = 'block';
                if (fallback) fallback.style.display = 'none';
            };

            img.onerror = function() {
                console.warn('[KR] Erro ao carregar imagem, usando fallback');
                if (loading) loading.style.display = 'none';
                img.style.display = 'none';
                krShowFallback();
            };

            img.src = currentTheme.previewUrl;
        } else {
            console.log('[KR] Sem URL de preview, usando fallback');
            if (loading) loading.style.display = 'none';
            krShowFallback();
        }

        console.log('[KR] Modal aberto');
    };

    // ═══════════════════════════════════════════════════════════
    // MOSTRAR FALLBACK
    // ═══════════════════════════════════════════════════════════
    function krShowFallback() {
        var fallback = document.getElementById('krThemePreviewFallback');
        var fallbackName = document.getElementById('krThemePreviewFallbackName');
        var gradient = document.getElementById('krThemePreviewGradient');
        var colorsContainer = document.getElementById('krThemePreviewFallbackColors');

        if (!fallback || !currentTheme) return;

        fallback.style.display = 'block';

        // Nome
        if (fallbackName) {
            fallbackName.textContent = currentTheme.name;
        }

        // Gradiente
        if (gradient) {
            gradient.style.background = 'linear-gradient(135deg, ' + currentTheme.primary + ' 0%, ' + currentTheme.success + ' 100%)';
        }

        // Círculos de cores
        if (colorsContainer) {
            colorsContainer.innerHTML = '';

            var colors = [
                { color: currentTheme.primary, label: 'Primária' },
                { color: currentTheme.success, label: 'Sucesso' },
                { color: currentTheme.warning, label: 'Aviso' },
                { color: currentTheme.danger, label: 'Perigo' }
            ];

            colors.forEach(function(item) {
                var wrapper = document.createElement('div');
                wrapper.style.cssText = 'display:flex; flex-direction:column; align-items:center; gap:8px;';

                var circle = document.createElement('div');
                circle.style.cssText = 'width:48px; height:48px; border-radius:50%; background:' + item.color + '; border:3px solid rgba(255,255,255,0.3); box-shadow:0 4px 12px rgba(0,0,0,0.3);';

                var label = document.createElement('div');
                label.style.cssText = 'color:rgba(255,255,255,0.7); font-size:11px; font-weight:500;';
                label.textContent = item.label;

                wrapper.appendChild(circle);
                wrapper.appendChild(label);
                colorsContainer.appendChild(wrapper);
            });
        }
    }

    // ═══════════════════════════════════════════════════════════
    // ATUALIZAR BOTÃO APLICAR
    // ═══════════════════════════════════════════════════════════
    function krUpdateApplyButton() {
        var btn = document.getElementById('krThemePreviewApplyBtn');
        if (!btn || !currentTheme) return;

        var radio = document.getElementById(currentTheme.radioId);
        var isCurrentTheme = radio && radio.checked;

        if (isCurrentTheme) {
            btn.innerHTML = '<svg style="width:16px; height:16px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Tema Atual';
            btn.style.background = 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)';
            btn.style.boxShadow = '0 4px 12px rgba(59,130,246,0.3)';
            btn.disabled = true;
            btn.style.cursor = 'default';
        } else {
            btn.innerHTML = '<svg style="width:16px; height:16px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Aplicar este tema';
            btn.style.background = 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)';
            btn.style.boxShadow = '0 4px 12px rgba(34,197,94,0.3)';
            btn.disabled = false;
            btn.style.cursor = 'pointer';
        }
    }

    // ═══════════════════════════════════════════════════════════
    // FECHAR MODAL (com cleanup completo e A11y)
    // ═══════════════════════════════════════════════════════════
    window.krCloseThemePreview = function() {
        console.log('[KR] Fechando modal');

        var modal = document.getElementById('krThemePreviewModal');
        var img = document.getElementById('krThemePreviewImg');
        var loading = document.getElementById('krThemePreviewLoading');
        var fallback = document.getElementById('krThemePreviewFallback');
        var zoomLabel = document.getElementById('krZoomLabel');

        // A11y: Marcar como escondido imediatamente
        if (modal) {
            modal.setAttribute('aria-hidden', 'true');
            modal.style.transition = 'opacity 0.2s ease';
            modal.style.opacity = '0';

            setTimeout(function() {
                modal.style.display = 'none';
                document.body.style.overflow = '';

                // Cleanup completo após fade-out
                if (img) {
                    img.onload = null;
                    img.onerror = null;
                    img.src = '';
                    img.style.display = 'none';
                    img.style.transform = 'scale(1)';
                }
                if (loading) loading.style.display = 'none';
                if (fallback) fallback.style.display = 'none';
            }, 200);
        }

        // Reset zoom
        zoomLevel = 1;
        if (zoomLabel) zoomLabel.textContent = '1x';

        // A11y: Restaurar foco ao elemento que abriu o modal
        if (previouslyFocusedElement && typeof previouslyFocusedElement.focus === 'function') {
            try {
                previouslyFocusedElement.focus();
            } catch (e) {
                // Elemento pode ter sido removido do DOM
                console.log('[KR] Não foi possível restaurar foco');
            }
        }
        previouslyFocusedElement = null;

        currentTheme = null;
    };

    // ═══════════════════════════════════════════════════════════
    // APLICAR TEMA (NÃO SUBMETE FORM) - com try/finally
    // ═══════════════════════════════════════════════════════════
    window.krApplyThemeFromPreview = function() {
        console.log('[KR] Aplicando tema do preview');

        if (!currentTheme || !currentTheme.radioId) {
            console.error('[KR] Nenhum tema selecionado');
            return;
        }

        var radio = document.getElementById(currentTheme.radioId);
        if (!radio) {
            console.error('[KR] Radio não encontrado:', currentTheme.radioId);
            return;
        }

        // Skip confirm dialog com try/finally para garantir reset
        window.__krSkipConfirm = true;
        try {
            radio.checked = true;
            radio.dispatchEvent(new Event('change', { bubbles: true }));
        } finally {
            window.__krSkipConfirm = false;
        }

        console.log('[KR] Radio marcado:', currentTheme.radioId);

        // Sync botões
        krSyncThemeButtons();

        // Fechar modal
        window.krCloseThemePreview();
    };

    // ═══════════════════════════════════════════════════════════
    // TOGGLE ZOOM 1x/2x
    // ═══════════════════════════════════════════════════════════
    window.krTogglePreviewZoom = function() {
        var img = document.getElementById('krThemePreviewImg');
        var label = document.getElementById('krZoomLabel');

        if (!img) return;

        zoomLevel = zoomLevel === 1 ? 2 : 1;
        img.style.transform = 'scale(' + zoomLevel + ')';
        img.style.cursor = zoomLevel === 1 ? 'zoom-in' : 'zoom-out';

        if (label) {
            label.textContent = zoomLevel + 'x';
        }

        console.log('[KR] Zoom:', zoomLevel + 'x');
    };

    // ═══════════════════════════════════════════════════════════
    // NAVEGAR ENTRE TEMAS (← →) SEM FECHAR MODAL
    // ═══════════════════════════════════════════════════════════
    window.krNavigateTheme = function(direction) {
        if (!currentTheme || !currentTheme.slug) {
            console.warn('[KR] Nenhum tema ativo para navegar');
            return;
        }

        // Obter lista de temas na ordem atual do grid
        var themeCards = document.querySelectorAll('.theme-card-wrapper[data-theme-slug]');
        var themeSlugs = [];
        themeCards.forEach(function(card) {
            themeSlugs.push(card.dataset.themeSlug);
        });

        if (themeSlugs.length === 0) {
            console.warn('[KR] Nenhum tema encontrado');
            return;
        }

        // Encontrar índice atual
        var currentIndex = themeSlugs.indexOf(currentTheme.slug);
        if (currentIndex === -1) {
            console.warn('[KR] Tema atual não encontrado na lista');
            return;
        }

        // Calcular novo índice (circular)
        var newIndex = currentIndex + direction;
        if (newIndex < 0) newIndex = themeSlugs.length - 1;
        if (newIndex >= themeSlugs.length) newIndex = 0;

        var newSlug = themeSlugs[newIndex];
        console.log('[KR] Navegando:', currentTheme.slug, '->', newSlug);

        // Encontrar o botão/div do novo tema para extrair dados
        var newCard = document.querySelector('.theme-card-wrapper[data-theme-slug="' + newSlug + '"]');
        if (!newCard) {
            console.error('[KR] Card não encontrado para:', newSlug);
            return;
        }

        // Encontrar o elemento clicável (div ou botão preview) com dados completos
        var previewBtn = newCard.querySelector('.kr-btn-preview[data-theme-slug]') ||
                         newCard.querySelector('.theme-card-label[data-theme-slug]');

        if (!previewBtn) {
            // Usar dados do card wrapper como fallback
            previewBtn = newCard;
        }

        // Carregar o novo tema no modal (reutiliza krOpenThemePreview)
        window.krOpenThemePreview(previewBtn);
    };

    // ═══════════════════════════════════════════════════════════
    // SELECIONAR TEMA (BOTÃO NO CARD) - com try/finally
    // ═══════════════════════════════════════════════════════════
    window.krSelectTheme = function(btn) {
        console.log('[KR] krSelectTheme chamado');

        if (!btn || !btn.dataset) return;

        var radioId = btn.dataset.radioId;
        var radio = document.getElementById(radioId);

        if (!radio) {
            console.error('[KR] Radio não encontrado:', radioId);
            return;
        }

        // Skip confirm dialog com try/finally para garantir reset
        window.__krSkipConfirm = true;
        try {
            radio.checked = true;
            radio.dispatchEvent(new Event('change', { bubbles: true }));
        } finally {
            window.__krSkipConfirm = false;
        }

        console.log('[KR] Tema selecionado:', radioId);

        // Sync botões
        krSyncThemeButtons();
    };

    // ═══════════════════════════════════════════════════════════
    // SYNC ESTADO DOS BOTÕES
    // ═══════════════════════════════════════════════════════════
    function krSyncThemeButtons() {
        console.log('[KR] Sincronizando botões');

        var buttons = document.querySelectorAll('.kr-btn-select[data-role="select-theme-btn"]');

        buttons.forEach(function(btn) {
            var radioId = btn.dataset.radioId;
            var radio = document.getElementById(radioId);
            var isChecked = radio && radio.checked;

            var textEl = btn.querySelector('.kr-btn-select-text');

            if (isChecked) {
                // Estado: Selecionado
                if (textEl) textEl.textContent = 'Selecionado';
                btn.style.background = '#22c55e';
                btn.style.color = '#fff';
                btn.disabled = true;
                btn.style.cursor = 'default';
                btn.title = 'Este tema está selecionado';
            } else {
                // Estado: Selecionar
                if (textEl) textEl.textContent = 'Selecionar';
                btn.style.background = '#3b82f6';
                btn.style.color = '#fff';
                btn.disabled = false;
                btn.style.cursor = 'pointer';
                btn.title = 'Selecionar este tema';
            }
        });
    }

    // ═══════════════════════════════════════════════════════════
    // ALIASES DE COMPATIBILIDADE (contrato antigo)
    // ═══════════════════════════════════════════════════════════
    window.openThemePreview = function() { return window.krOpenThemePreview.apply(null, arguments); };
    window.closeThemePreview = function() { return window.krCloseThemePreview.apply(null, arguments); };
    window.applyThemeFromPreview = function() { return window.krApplyThemeFromPreview.apply(null, arguments); };
    window.syncThemeButtons = function() { return krSyncThemeButtons.apply(null, arguments); };
    window.syncThemeButtonStates = function() { return krSyncThemeButtons.apply(null, arguments); };
    window.krSyncThemeButtons = krSyncThemeButtons;

    // ═══════════════════════════════════════════════════════════
    // FOCUS TRAP - A11y
    // ═══════════════════════════════════════════════════════════
    function krGetFocusableElements() {
        var modal = document.getElementById('krThemePreviewModal');
        if (!modal) return [];

        // Seletor para elementos focáveis
        var focusableSelector = 'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
        var elements = modal.querySelectorAll(focusableSelector);

        // Filtrar apenas elementos visíveis
        return Array.prototype.filter.call(elements, function(el) {
            return el.offsetParent !== null && getComputedStyle(el).visibility !== 'hidden';
        });
    }

    function krHandleFocusTrap(e) {
        var modal = document.getElementById('krThemePreviewModal');
        if (!modal || modal.style.display !== 'block') return;

        if (e.key !== 'Tab') return;

        var focusable = krGetFocusableElements();
        if (focusable.length === 0) return;

        var firstEl = focusable[0];
        var lastEl = focusable[focusable.length - 1];

        if (e.shiftKey) {
            // Shift+Tab: se está no primeiro, vai pro último
            if (document.activeElement === firstEl) {
                e.preventDefault();
                lastEl.focus();
            }
        } else {
            // Tab: se está no último, vai pro primeiro
            if (document.activeElement === lastEl) {
                e.preventDefault();
                firstEl.focus();
            }
        }
    }

    // ═══════════════════════════════════════════════════════════
    // EVENT LISTENERS
    // ═══════════════════════════════════════════════════════════

    // ESC fecha modal + Focus trap com Tab + Setas navegam temas
    document.addEventListener('keydown', function(e) {
        var modal = document.getElementById('krThemePreviewModal');
        if (!modal || modal.style.display !== 'block') return;

        // ESC fecha modal
        if (e.key === 'Escape') {
            window.krCloseThemePreview();
            return;
        }

        // ← Tema anterior
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            window.krNavigateTheme(-1);
            return;
        }

        // → Próximo tema
        if (e.key === 'ArrowRight') {
            e.preventDefault();
            window.krNavigateTheme(1);
            return;
        }

        // Focus trap
        if (e.key === 'Tab') {
            krHandleFocusTrap(e);
        }
    });

    // Change do radio -> sync botões (usa alias)
    document.addEventListener('change', function(e) {
        if (e.target && e.target.matches('input[type="radio"][name="selected_theme"]')) {
            console.log('[KR] Radio changed:', e.target.value);
            window.syncThemeButtons();
        }
    });

    // ═══════════════════════════════════════════════════════════
    // INIT
    // ═══════════════════════════════════════════════════════════
    function krInit() {
        console.log('[KR] Inicializando...');
        krSyncThemeButtons();
        console.log('[KR] Pronto!');
    }

    // Rodar ao carregar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', krInit);
    } else {
        krInit();
    }

    // Log de verificação (funções kr* e aliases)
    console.log('[KR] Funções disponíveis:', {
        krOpenThemePreview: typeof window.krOpenThemePreview,
        krCloseThemePreview: typeof window.krCloseThemePreview,
        krApplyThemeFromPreview: typeof window.krApplyThemeFromPreview,
        krSelectTheme: typeof window.krSelectTheme,
        krSyncThemeButtons: typeof window.krSyncThemeButtons,
        krTogglePreviewZoom: typeof window.krTogglePreviewZoom,
        // Aliases
        openThemePreview: typeof window.openThemePreview,
        closeThemePreview: typeof window.closeThemePreview,
        applyThemeFromPreview: typeof window.applyThemeFromPreview,
        syncThemeButtons: typeof window.syncThemeButtons
    });

})();
</script>
</x-admin::layouts>
