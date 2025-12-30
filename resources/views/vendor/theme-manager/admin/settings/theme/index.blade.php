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
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            Tema Predefinido
                        </p>

                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                            Escolha um tema base. As cores e configurações do tema selecionado serão aplicadas automaticamente.
                        </p>

                        <!-- Grid de Cards Premium (Formato 3:4 Compacto) -->
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-3 mb-4" id="theme-selector-grid">
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
                                @endphp
                                <label class="theme-card-label group cursor-pointer block">
                                    <input type="radio"
                                           name="selected_theme"
                                           value="{{ $themeSlug }}"
                                           class="peer sr-only theme-radio"
                                           data-theme-name="{{ $theme['name'] }}"
                                           data-theme-slug="{{ $themeSlug }}"
                                           {{ $isSelected ? 'checked' : '' }}>

                                    {{-- CARD CONTAINER COM HOVER EFFECTS (super mini) --}}
                                    <div class="theme-card relative flex flex-col h-full bg-white dark:bg-gray-900 overflow-hidden"
                                         style="border-radius:8px; border:{{ $isSelected ? '2px solid #2563eb' : '1px solid #e5e7eb' }}; box-shadow:{{ $isSelected ? '0 2px 12px rgba(37,99,235,0.3)' : '0 1px 2px rgba(0,0,0,0.08)' }}; transition:all 0.2s ease;"
                                         data-theme="{{ $themeSlug }}"
                                         @if(!$isSelected)
                                         onmouseenter="this.style.transform='translateY(-2px) scale(1.01)'; this.style.boxShadow='0 8px 16px -4px rgba(0,0,0,0.12)';"
                                         onmouseleave="this.style.transform=''; this.style.boxShadow='0 1px 2px rgba(0,0,0,0.08)';"
                                         @endif>

                                        {{-- INDICADORES DE TEMA ATIVO --}}
                                        @if($isSelected)
                                            {{-- RIBBON DIAGONAL "EM USO" (ultra mini) --}}
                                            <div style="position:absolute; top:0; left:0; z-index:20; overflow:hidden; width:40px; height:40px; pointer-events:none;">
                                                <div style="position:absolute; top:8px; left:-12px; width:56px; background:linear-gradient(90deg, #059669, #10b981); color:white; font-size:6px; font-weight:bold; padding:1px 0; text-align:center; box-shadow:0 1px 2px rgba(0,0,0,0.2); transform:rotate(-45deg);">
                                                    EM USO
                                                </div>
                                            </div>

                                            {{-- BADGE "ATIVO" (ultra mini) --}}
                                            <div style="position:absolute; top:2px; right:2px; z-index:20; background:#2563eb; color:white; font-size:6px; font-weight:bold; padding:1px 4px; border-radius:9999px; box-shadow:0 1px 3px rgba(37,99,235,0.35); display:flex; align-items:center; gap:1px; animation:pulse 2s infinite;">
                                                <svg style="width:5px; height:5px;" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Ativo
                                            </div>
                                        @endif

                                        {{-- PREVIEW AREA (16:9 ultra compacto ~80px) --}}
                                        <div style="position:relative; width:100%; padding-top:56.25%; max-height:80px; background:#f9fafb; overflow:hidden;">
                                            @if(!empty($theme['preview']))
                                                <img src="{{ asset('storage/themes/' . $themeSlug . '/' . $theme['preview']) }}"
                                                     alt="Preview de {{ $theme['name'] ?? $themeSlug }}"
                                                     loading="lazy"
                                                     style="position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover;">
                                            @else
                                                @php
                                                // Define padrão visual único para cada tema
                                                $patterns = [
                                                    'default' => 'circles',
                                                    'azul-oceano' => 'waves',
                                                    'roxo-moderno' => 'grid',
                                                    'stelium-sanctuary' => 'hexagons',
                                                    'verde-natureza' => 'leaves',
                                                    'minimalista-cinza' => 'lines',
                                                    'vermelho-corporativo' => 'squares',
                                                ];
                                                $pattern = $patterns[$themeSlug] ?? 'circles';
                                                @endphp

                                                {{-- Gradiente base --}}
                                                <div style="position:absolute; inset:0; opacity:0.85; background:linear-gradient(135deg, {{ $colorPrimary }} 0%, {{ $colorPrimaryDark }} 50%, {{ $colorSuccess }} 100%);"></div>

                                                {{-- Padrão único por tema --}}
                                                @if($pattern === 'circles')
                                                    <div style="position:absolute; inset:0; background-image:radial-gradient(circle, rgba(255,255,255,0.12) 2px, transparent 2px); background-size:16px 16px;"></div>
                                                    <div style="position:absolute; top:6px; right:6px; width:20px; height:20px; border-radius:50%; border:2px solid rgba(255,255,255,0.25);"></div>
                                                    <div style="position:absolute; bottom:6px; left:6px; width:12px; height:12px; border-radius:50%; border:2px solid rgba(255,255,255,0.2);"></div>
                                                @elseif($pattern === 'waves')
                                                    <svg style="position:absolute; inset:0; width:100%; height:100%; opacity:0.2;" preserveAspectRatio="none" viewBox="0 0 100 60">
                                                        <path d="M0,15 Q15,5 30,15 T60,15 T90,15 T120,15" stroke="white" fill="none" stroke-width="1.5"/>
                                                        <path d="M0,30 Q15,20 30,30 T60,30 T90,30 T120,30" stroke="white" fill="none" stroke-width="1.5"/>
                                                        <path d="M0,45 Q15,35 30,45 T60,45 T90,45 T120,45" stroke="white" fill="none" stroke-width="1.5"/>
                                                    </svg>
                                                @elseif($pattern === 'grid')
                                                    <div style="position:absolute; inset:0; background-image:linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size:12px 12px;"></div>
                                                    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%) rotate(45deg); width:16px; height:16px; border:2px solid rgba(255,255,255,0.3);"></div>
                                                @elseif($pattern === 'hexagons')
                                                    <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; gap:4px;">
                                                        <div style="width:12px; height:14px; background:rgba(255,255,255,0.1); clip-path:polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);"></div>
                                                        <div style="width:12px; height:14px; background:rgba(255,255,255,0.2); clip-path:polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);"></div>
                                                        <div style="width:12px; height:14px; background:rgba(255,255,255,0.1); clip-path:polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);"></div>
                                                    </div>
                                                @elseif($pattern === 'leaves')
                                                    <div style="position:absolute; top:4px; right:6px; font-size:14px; opacity:0.25;">🌿</div>
                                                    <div style="position:absolute; bottom:4px; left:6px; font-size:12px; opacity:0.2;">🍃</div>
                                                @elseif($pattern === 'lines')
                                                    <div style="position:absolute; top:12px; left:0; right:0; height:1px; background:rgba(255,255,255,0.2);"></div>
                                                    <div style="position:absolute; top:50%; left:0; right:0; height:1px; background:rgba(255,255,255,0.25);"></div>
                                                    <div style="position:absolute; bottom:12px; left:0; right:0; height:1px; background:rgba(255,255,255,0.2);"></div>
                                                @else
                                                    <div style="position:absolute; top:8px; left:8px; display:flex; flex-wrap:wrap; gap:3px;">
                                                        <div style="width:8px; height:8px; background:rgba(255,255,255,0.15);"></div>
                                                        <div style="width:8px; height:8px; background:rgba(255,255,255,0.1);"></div>
                                                        <div style="width:8px; height:8px; background:rgba(255,255,255,0.2);"></div>
                                                    </div>
                                                @endif

                                                {{-- Nome do tema (mini glass) --}}
                                                <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center;">
                                                    <div style="backdrop-filter:blur(2px); background:rgba(255,255,255,0.1); border-radius:3px; padding:2px 6px; border:1px solid rgba(255,255,255,0.2);">
                                                        <div style="color:white; font-size:8px; font-weight:bold; text-shadow:0 1px 2px rgba(0,0,0,0.5); text-align:center;">
                                                            {{ $theme['name'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- CARD BODY (ultra mini) --}}
                                        <div style="padding:5px; display:flex; flex-direction:column; gap:3px;">
                                            {{-- Nome (versão apenas no title) --}}
                                            <h3 class="font-semibold text-gray-900 dark:text-white truncate" style="font-size:9px; line-height:1.1;" title="{{ $theme['name'] }} v{{ $theme['version'] ?? '1.0' }}">
                                                {{ $theme['name'] }}
                                            </h3>

                                            {{-- Cores (3 principais) --}}
                                            <div style="display:flex; align-items:center; justify-content:space-between;">
                                                <span class="text-gray-400" style="font-size:6px; text-transform:uppercase; letter-spacing:0.04em;">Cores</span>
                                                <div style="display:flex; gap:2px;">
                                                    <span style="width:7px; height:7px; border-radius:50%; border:1px solid rgba(0,0,0,0.08); background:{{ $colorPrimary }};"></span>
                                                    <span style="width:7px; height:7px; border-radius:50%; border:1px solid rgba(0,0,0,0.08); background:{{ $colorSuccess }};"></span>
                                                    <span style="width:7px; height:7px; border-radius:50%; border:1px solid rgba(0,0,0,0.08); background:{{ $colorDanger }};"></span>
                                                </div>
                                            </div>

                                            {{-- Botão de Ação (ultra mini) --}}
                                            @if($isSelected)
                                                <div style="width:100%; padding:2px 3px; border-radius:2px; font-size:7px; font-weight:600; text-align:center; background:#2563eb; color:white;">
                                                    ✓ Ativo
                                                </div>
                                            @else
                                                <div style="width:100%; padding:2px 3px; border-radius:2px; font-size:7px; font-weight:500; text-align:center; background:#f3f4f6; color:#374151; transition:all 0.15s ease; cursor:pointer;"
                                                     onmouseenter="this.style.background='#dbeafe'; this.style.color='#1d4ed8';"
                                                     onmouseleave="this.style.background='#f3f4f6'; this.style.color='#374151';">
                                                    Selecionar
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </label>
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

                    <!-- SUB-SEÇÃO: CORES DO TEMA -->
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            🎨 @lang('theme-manager::app.settings.colors.title')
                        </p>

                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                            @lang('theme-manager::app.settings.colors.description')
                        </p>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Cor Primária -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.colors.primary')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Cor principal da marca. Usada em botões, links e elementos de destaque em todo o sistema.</span>
                                    </span>
                                </x-admin::form.control-group.label>

                                <div class="flex items-center gap-2">
                                    <x-admin::form.control-group.control
                                        type="color"
                                        name="color_primary"
                                        :value="old('color_primary', $config->color_primary)"
                                        class="h-10 w-20"
                                    />
                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="color_primary"
                                        :value="old('color_primary', $config->color_primary)"
                                        class="flex-1"
                                    />
                                </div>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.colors.primary-help')
                                </p>

                                <x-admin::form.control-group.error control-name="color_primary" />
                            </x-admin::form.control-group>

                            <!-- Cor Primária Escura -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.colors.primary-dark')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Variação escura da cor primária. Usada em estados hover de botões e elementos interativos.</span>
                                    </span>
                                </x-admin::form.control-group.label>

                                <div class="flex items-center gap-2">
                                    <x-admin::form.control-group.control
                                        type="color"
                                        name="color_primary_dark"
                                        :value="old('color_primary_dark', $config->color_primary_dark)"
                                        class="h-10 w-20"
                                    />
                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="color_primary_dark"
                                        :value="old('color_primary_dark', $config->color_primary_dark)"
                                        class="flex-1"
                                    />
                                </div>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.colors.primary-dark-help')
                                </p>

                                <x-admin::form.control-group.error control-name="color_primary_dark" />
                            </x-admin::form.control-group>

                            <!-- Cor Primária Clara -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.colors.primary-light')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Variação clara da cor primária. Usada em backgrounds sutis, bordas e elementos secundários.</span>
                                    </span>
                                </x-admin::form.control-group.label>

                                <div class="flex items-center gap-2">
                                    <x-admin::form.control-group.control
                                        type="color"
                                        name="color_primary_light"
                                        :value="old('color_primary_light', $config->color_primary_light)"
                                        class="h-10 w-20"
                                    />
                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="color_primary_light"
                                        :value="old('color_primary_light', $config->color_primary_light)"
                                        class="flex-1"
                                    />
                                </div>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.colors.primary-light-help')
                                </p>

                                <x-admin::form.control-group.error control-name="color_primary_light" />
                            </x-admin::form.control-group>

                            <!-- Cor de Sucesso -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.colors.success')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Cor para indicar sucesso. Usada em mensagens de confirmação, badges de status positivo e ícones de check.</span>
                                    </span>
                                </x-admin::form.control-group.label>

                                <div class="flex items-center gap-2">
                                    <x-admin::form.control-group.control
                                        type="color"
                                        name="color_success"
                                        :value="old('color_success', $config->color_success)"
                                        class="h-10 w-20"
                                    />
                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="color_success"
                                        :value="old('color_success', $config->color_success)"
                                        class="flex-1"
                                    />
                                </div>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.colors.success-help')
                                </p>

                                <x-admin::form.control-group.error control-name="color_success" />
                            </x-admin::form.control-group>

                            <!-- Cor de Alerta -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.colors.warning')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Cor para alertas e avisos. Usada em mensagens de atenção, badges de pendência e notificações.</span>
                                    </span>
                                </x-admin::form.control-group.label>

                                <div class="flex items-center gap-2">
                                    <x-admin::form.control-group.control
                                        type="color"
                                        name="color_warning"
                                        :value="old('color_warning', $config->color_warning)"
                                        class="h-10 w-20"
                                    />
                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="color_warning"
                                        :value="old('color_warning', $config->color_warning)"
                                        class="flex-1"
                                    />
                                </div>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.colors.warning-help')
                                </p>

                                <x-admin::form.control-group.error control-name="color_warning" />
                            </x-admin::form.control-group>

                            <!-- Cor de Perigo -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.colors.danger')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Cor para erros e ações destrutivas. Usada em mensagens de erro, botões de exclusão e validações.</span>
                                    </span>
                                </x-admin::form.control-group.label>

                                <div class="flex items-center gap-2">
                                    <x-admin::form.control-group.control
                                        type="color"
                                        name="color_danger"
                                        :value="old('color_danger', $config->color_danger)"
                                        class="h-10 w-20"
                                    />
                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="color_danger"
                                        :value="old('color_danger', $config->color_danger)"
                                        class="flex-1"
                                    />
                                </div>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.colors.danger-help')
                                </p>

                                <x-admin::form.control-group.error control-name="color_danger" />
                            </x-admin::form.control-group>
                        </div>
                    </div>

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

                            <!-- Overlay Color -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('theme-manager::app.settings.login-card.overlay-color')
                                    <span class="theme-tooltip">
                                        <span class="theme-tooltip-icon">i</span>
                                        <span class="theme-tooltip-content">Cor de sobreposição sobre a imagem. Use formato rgba() para controlar transparência. Ex: rgba(10, 45, 15, 0.78)</span>
                                    </span>
                                </x-admin::form.control-group.label>

                                <div class="flex items-center gap-2">
                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="login_card_overlay_color"
                                        id="login_card_overlay_color"
                                        :value="old('login_card_overlay_color', $config->login_card_overlay_color ?? 'rgba(10, 45, 15, 0.78)')"
                                        placeholder="rgba(10, 45, 15, 0.78)"
                                        class="flex-1"
                                    />
                                    <div
                                        id="overlay_color_preview"
                                        class="w-10 h-10 rounded border border-gray-300 dark:border-gray-600"
                                        style="background-color: {{ old('login_card_overlay_color', $config->login_card_overlay_color ?? 'rgba(10, 45, 15, 0.78)') }};"
                                    ></div>
                                </div>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @lang('theme-manager::app.settings.login-card.overlay-color-help')
                                </p>

                                <x-admin::form.control-group.error control-name="login_card_overlay_color" />
                            </x-admin::form.control-group>

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

    window.updateOverlayColorPreview = function() {
        const input = document.getElementById('login_card_overlay_color');
        const preview = document.getElementById('overlay_color_preview');
        if (input && preview) preview.style.backgroundColor = input.value;
    };

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
        const overlayInput = document.getElementById('login_card_overlay_color');
        if (overlayInput) {
            overlayInput.addEventListener('input', updateOverlayColorPreview);
            overlayInput.addEventListener('change', updateOverlayColorPreview);
        }
        document.querySelectorAll('input[type="color"]').forEach(function(picker) {
            const name = picker.name;
            const textInputs = document.querySelectorAll('input[type="text"][name="' + name + '"]');
            picker.addEventListener('input', function() {
                textInputs.forEach(function(txt) {txt.value = picker.value.toUpperCase();});
            });
            textInputs.forEach(function(txt) {
                txt.addEventListener('input', function() {
                    if (txt.value.match(/^#[0-9A-Fa-f]{6}$/)) picker.value = txt.value;
                });
            });
        });

        // ════════════════════════════════════════════════════════════
        // Confirmação ao Trocar Tema
        // ════════════════════════════════════════════════════════════
        const currentTheme = '{{ $config->selected_theme ?? "default" }}';
        let currentThemeName = 'Default';

        // Obter nome do tema atual
        const currentRadio = document.querySelector('.theme-radio[value="' + currentTheme + '"]');
        if (currentRadio) {
            currentThemeName = currentRadio.dataset.themeName || 'Default';
        }

        // Adicionar listener em todos os radio buttons de tema
        document.querySelectorAll('.theme-radio').forEach(function(radio) {
            radio.addEventListener('change', function(e) {
                if (this.value !== currentTheme) {
                    const newThemeName = this.dataset.themeName || 'Desconhecido';

                    const confirmMessage =
                        '⚠️ CONFIRMAR ALTERAÇÃO DE TEMA\n\n' +
                        '═══════════════════════════════════\n' +
                        'Tema Atual:  ' + currentThemeName + '\n' +
                        'Novo Tema:   ' + newThemeName + '\n' +
                        '═══════════════════════════════════\n\n' +
                        '⚠️ ATENÇÃO:\n' +
                        '• Suas customizações atuais serão substituídas\n' +
                        '• Cores, logos e configurações serão redefinidas\n' +
                        '• Você pode reverter usando "Voltar Tema Anterior"\n\n' +
                        'Deseja continuar com a alteração?';

                    if (!confirm(confirmMessage)) {
                        // Usuário cancelou - reverter seleção
                        e.preventDefault();
                        const originalRadio = document.querySelector('.theme-radio[value="' + currentTheme + '"]');
                        if (originalRadio) {
                            originalRadio.checked = true;
                        }
                    }
                    // Se confirmou, o radio já está selecionado
                }
            });
        });

        console.log('🎨 Theme selection confirmation initialized');

        // ════════════════════════════════════════════════════════════
        // Tornar cards clicáveis (fallback)
        // ════════════════════════════════════════════════════════════
        document.querySelectorAll('.theme-card-label').forEach(function(label) {
            label.addEventListener('click', function(e) {
                // Se clicou no label mas não no input
                if (e.target !== this.querySelector('input')) {
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio && !radio.disabled) {
                        // Trigger change event manually if needed
                        if (!radio.checked) {
                            radio.checked = true;
                            radio.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }
                }
            });
        });

        console.log('✅ Theme cards made clickable');
    });
})();
</script>
@endPushOnce
</x-admin::layouts>
