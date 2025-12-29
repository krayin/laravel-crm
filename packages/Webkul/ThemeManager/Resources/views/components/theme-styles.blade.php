<style>
    {!! app('theme')->getCssVariables() !!}

    /* =================================
       BUTTONS - PRIMARY BRAND COLOR
       ================================= */

    /* Primary buttons */
    .primary-button,
    .btn-primary,
    button[type="submit"]:not(.secondary-button),
    .button.primary {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }

    .primary-button:hover,
    .btn-primary:hover,
    button[type="submit"]:not(.secondary-button):hover,
    .button.primary:hover {
        background-color: var(--primary-dark-color) !important;
        border-color: var(--primary-dark-color) !important;
    }

    .primary-button:focus,
    .btn-primary:focus,
    button[type="submit"]:not(.secondary-button):focus,
    .button.primary:focus {
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25) !important;
    }

    /* Primary outlined buttons */
    .primary-button.outlined,
    .btn-outline-primary {
        color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        background-color: transparent !important;
    }

    .primary-button.outlined:hover,
    .btn-outline-primary:hover {
        color: white !important;
        background-color: var(--primary-color) !important;
    }

    /* =================================
       LINKS
       ================================= */

    a:not(.button):not(.btn),
    .text-primary {
        color: var(--primary-color) !important;
    }

    a:not(.button):not(.btn):hover {
        color: var(--primary-dark-color) !important;
    }

    /* =================================
       FORMS
       ================================= */

    /* Input focus */
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    input[type="number"]:focus,
    input[type="tel"]:focus,
    input[type="url"]:focus,
    textarea:focus,
    select:focus {
        border-color: var(--primary-light-color) !important;
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.1) !important;
    }

    /* Checkbox and radio checked state */
    input[type="checkbox"]:checked,
    input[type="radio"]:checked {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }

    /* Toggle switch */
    .toggle-switch input:checked + .slider {
        background-color: var(--primary-color) !important;
    }

    /* =================================
       NAVIGATION
       ================================= */

    /* Active menu items */
    .sidebar .menu-item.active,
    .sidebar .menu-item:hover,
    .nav-tabs .nav-link.active {
        background-color: var(--primary-light-color) !important;
        color: var(--primary-color) !important;
    }

    /* Active tab underline */
    .nav-tabs .nav-link.active {
        border-bottom-color: var(--primary-color) !important;
    }

    /* Sidebar selected item */
    .sidebar .selected,
    .sidebar .active > a {
        background-color: var(--primary-light-color) !important;
        border-left-color: var(--primary-color) !important;
    }

    /* =================================
       BADGES & PILLS
       ================================= */

    .badge-primary,
    .pill-primary {
        background-color: var(--primary-color) !important;
    }

    .badge-outline-primary {
        color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }

    /* =================================
       ALERTS & MESSAGES
       ================================= */

    /* Success */
    .alert-success,
    .badge-success,
    .text-success,
    .bg-success {
        background-color: var(--success-color) !important;
        border-color: var(--success-color) !important;
    }

    .alert-success {
        background-color: rgba(var(--success-rgb), 0.1) !important;
        color: var(--success-color) !important;
        border-color: rgba(var(--success-rgb), 0.3) !important;
    }

    /* Warning */
    .alert-warning,
    .badge-warning,
    .text-warning,
    .bg-warning {
        background-color: var(--warning-color) !important;
        border-color: var(--warning-color) !important;
    }

    .alert-warning {
        background-color: rgba(var(--warning-rgb), 0.1) !important;
        color: var(--warning-color) !important;
        border-color: rgba(var(--warning-rgb), 0.3) !important;
    }

    /* Danger */
    .alert-danger,
    .badge-danger,
    .text-danger,
    .bg-danger {
        background-color: var(--danger-color) !important;
        border-color: var(--danger-color) !important;
    }

    .alert-danger {
        background-color: rgba(var(--danger-rgb), 0.1) !important;
        color: var(--danger-color) !important;
        border-color: rgba(var(--danger-rgb), 0.3) !important;
    }

    /* =================================
       PROGRESS & LOADERS
       ================================= */

    .progress-bar {
        background-color: var(--primary-color) !important;
    }

    .spinner-border,
    .loader {
        border-color: var(--primary-light-color) !important;
        border-top-color: var(--primary-color) !important;
    }

    /* =================================
       TABLES
       ================================= */

    /* Table header */
    .table thead th {
        background-color: var(--primary-light-color) !important;
        color: var(--primary-color) !important;
    }

    /* Table row hover */
    .table tbody tr:hover {
        background-color: rgba(var(--primary-rgb), 0.05) !important;
    }

    /* Table striped */
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(var(--primary-rgb), 0.03) !important;
    }

    /* =================================
       PAGINATION
       ================================= */

    .pagination .page-item.active .page-link {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }

    .pagination .page-link {
        color: var(--primary-color) !important;
    }

    .pagination .page-link:hover {
        background-color: var(--primary-light-color) !important;
        border-color: var(--primary-light-color) !important;
    }

    /* =================================
       DROPDOWNS
       ================================= */

    .dropdown-item:hover,
    .dropdown-item:focus {
        background-color: var(--primary-light-color) !important;
        color: var(--primary-color) !important;
    }

    .dropdown-item.active {
        background-color: var(--primary-color) !important;
    }

    /* =================================
       MODALS
       ================================= */

    .modal-header {
        background-color: var(--primary-light-color) !important;
        color: var(--primary-color) !important;
    }

    /* =================================
       CARDS
       ================================= */

    .card-header {
        background-color: var(--primary-light-color) !important;
        border-bottom-color: var(--primary-color) !important;
    }

    /* =================================
       TOOLTIPS & POPOVERS
       ================================= */

    .tooltip-inner {
        background-color: var(--primary-dark-color) !important;
    }

    .tooltip.bs-tooltip-top .arrow::before,
    .tooltip.bs-tooltip-auto[x-placement^="top"] .arrow::before {
        border-top-color: var(--primary-dark-color) !important;
    }

    /* =================================
       ICONS & HIGHLIGHTS
       ================================= */

    .icon-primary,
    .text-primary-emphasis {
        color: var(--primary-color) !important;
    }

    .bg-primary-light {
        background-color: var(--primary-light-color) !important;
    }

    /* Selected items */
    .selected,
    .highlighted {
        background-color: var(--primary-light-color) !important;
        border-left: 3px solid var(--primary-color) !important;
    }

    /* =================================
       KANBAN & DRAG-DROP
       ================================= */

    .kanban-item.dragging {
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 10px rgba(var(--primary-rgb), 0.3) !important;
    }

    .kanban-column.drag-over {
        background-color: var(--primary-light-color) !important;
    }

    /* =================================
       DATETIME PICKER
       ================================= */

    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }

    .flatpickr-day:hover {
        background-color: var(--primary-light-color) !important;
    }

    /* =================================
       STATUS INDICATORS
       ================================= */

    .status-success,
    .status-dot.success {
        background-color: var(--success-color) !important;
    }

    .status-warning,
    .status-dot.warning {
        background-color: var(--warning-color) !important;
    }

    .status-danger,
    .status-dot.danger {
        background-color: var(--danger-color) !important;
    }

    /* =================================
       CHARTS (if using Chart.js)
       ================================= */

    .chart-primary {
        color: var(--primary-color) !important;
    }

    /* =================================
       SCROLLBAR
       ================================= */

    ::-webkit-scrollbar-thumb {
        background-color: var(--primary-light-color) !important;
    }

    ::-webkit-scrollbar-thumb:hover {
        background-color: var(--primary-color) !important;
    }

    /* =================================
       UTILITY CLASSES
       ================================= */

    .border-primary {
        border-color: var(--primary-color) !important;
    }

    .border-success {
        border-color: var(--success-color) !important;
    }

    .border-warning {
        border-color: var(--warning-color) !important;
    }

    .border-danger {
        border-color: var(--danger-color) !important;
    }

    /* =================================
       FOCUS STATES
       ================================= */

    *:focus-visible {
        outline-color: var(--primary-color) !important;
    }

    /* =================================
       SELECTION
       ================================= */

    ::selection {
        background-color: var(--primary-color) !important;
        color: white !important;
    }

    ::-moz-selection {
        background-color: var(--primary-color) !important;
        color: white !important;
    }

    /* =================================
       LOGOS CUSTOMIZADOS - CSS FALLBACK
       ================================= */

    @php
        $themeConfig = app('theme')->getConfig();
    @endphp

    @if($themeConfig->logo_main)
        /* Fallback CSS para logos (pode não funcionar em todos navegadores) */
        #logo-image,
        img[id="logo-image"],
        img[alt="Krayin CRM"],
        img.h-10[src*="logo"] {
            content: url('{{ asset("storage/theme-manager/" . $themeConfig->logo_main) }}') !important;
        }

        /* Background images */
        [style*="logo.svg"],
        [style*="logo.png"] {
            background-image: url('{{ asset("storage/theme-manager/" . $themeConfig->logo_main) }}') !important;
        }
    @endif

    @if($themeConfig->logo_icon)
        /* Logo pequeno/icon */
        img[src*="/cache/logo"],
        img[width="24"][height="24"][src*="logo"] {
            content: url('{{ asset("storage/theme-manager/" . $themeConfig->logo_icon) }}') !important;
        }
    @endif

    @if($themeConfig->logo_light)
        /* Logo claro (dark mode) */
        img[src*="dark-logo"],
        img[src*="light-logo"] {
            content: url('{{ asset("storage/theme-manager/" . $themeConfig->logo_light) }}') !important;
        }
    @endif

    /* =================================
       LOGIN PAGE BACKGROUND
       ================================= */

    @if($themeConfig->login_bg_image)
    /* Background para página de login */
    body,
    .min-h-screen,
    .flex.min-h-screen,
    [class*="login"],
    [class*="auth"] {
        background-image: url('{{ asset("storage/theme-manager/" . $themeConfig->login_bg_image) }}') !important;
        background-size: {{ $themeConfig->login_bg_zoom ?? 100 }}% !important;
        background-position: center center !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important;
    }

    /* Overlay para controlar opacidade */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, {{ (100 - ($themeConfig->login_bg_opacity ?? 50)) / 100 }});
        pointer-events: none;
        z-index: 0;
    }

    /* Garantir que conteúdo fique acima do overlay */
    body > * {
        position: relative;
        z-index: 1;
    }
    @endif

</style>

{{-- JAVASCRIPT PRINCIPAL - Substitui logos via JavaScript (MAIS CONFIÁVEL) --}}
@if($themeConfig->logo_main || $themeConfig->logo_light || $themeConfig->logo_icon || $themeConfig->favicon)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🎨 ThemeManager: Iniciando troca de logos...');

        @if($themeConfig->logo_main)
        // ==========================================
        // LOGO PRINCIPAL (Desktop e Mobile)
        // ==========================================
        var logoMainUrl = '{{ asset("storage/theme-manager/" . $themeConfig->logo_main) }}';
        console.log('📦 Logo principal URL:', logoMainUrl);

        // Método 1: Por ID (logo-image)
        var logoImages = document.querySelectorAll('#logo-image, img[id="logo-image"]');
        console.log('🔍 Elementos com id="logo-image" encontrados:', logoImages.length);
        logoImages.forEach(function(img, index) {
            console.log('  ✓ Substituindo logo #' + (index + 1) + ':', img.src, '→', logoMainUrl);
            img.src = logoMainUrl;
        });

        // Método 2: Por classe h-10 (desktop e mobile)
        var h10Logos = document.querySelectorAll('img.h-10[src*="logo"]');
        console.log('🔍 Logos com classe h-10 encontrados:', h10Logos.length);
        h10Logos.forEach(function(img, index) {
            console.log('  ✓ Substituindo h-10 logo #' + (index + 1) + ':', img.src, '→', logoMainUrl);
            img.src = logoMainUrl;
        });

        // Método 3: Por alt="Krayin CRM"
        var krayinLogos = document.querySelectorAll('img[alt="Krayin CRM"]');
        console.log('🔍 Logos com alt="Krayin CRM" encontrados:', krayinLogos.length);
        krayinLogos.forEach(function(img, index) {
            console.log('  ✓ Substituindo Krayin logo #' + (index + 1) + ':', img.src, '→', logoMainUrl);
            img.src = logoMainUrl;
        });

        // Método 4: Logos com hash do Vite
        var viteLogos = document.querySelectorAll('img[src*="/admin/build/assets/logo-"]');
        console.log('🔍 Logos do Vite encontrados:', viteLogos.length);
        viteLogos.forEach(function(img, index) {
            if (!img.src.includes('dark') && !img.src.includes('mobile')) {
                console.log('  ✓ Substituindo Vite logo #' + (index + 1) + ':', img.src, '→', logoMainUrl);
                img.src = logoMainUrl;
            }
        });
        @endif

        @if($themeConfig->logo_icon)
        // ==========================================
        // LOGO PEQUENO/ICON (cache/logo.png)
        // ==========================================
        var logoIconUrl = '{{ asset("storage/theme-manager/" . $themeConfig->logo_icon) }}';
        console.log('📦 Logo icon URL:', logoIconUrl);

        var smallLogos = document.querySelectorAll('img[src*="/cache/logo"], img[src*="logo.png"][width="24"]');
        console.log('🔍 Logos pequenos encontrados:', smallLogos.length);
        smallLogos.forEach(function(img, index) {
            console.log('  ✓ Substituindo small logo #' + (index + 1) + ':', img.src, '→', logoIconUrl);
            img.src = logoIconUrl;
        });

        // Logo mobile do Vite
        var mobileLogos = document.querySelectorAll('img[src*="mobile-light-logo"], img[src*="mobile-dark-logo"]');
        console.log('🔍 Logos mobile encontrados:', mobileLogos.length);
        mobileLogos.forEach(function(img, index) {
            console.log('  ✓ Substituindo mobile logo #' + (index + 1) + ':', img.src, '→', logoIconUrl);
            img.src = logoIconUrl;
        });
        @endif

        @if($themeConfig->logo_light)
        // ==========================================
        // LOGO CLARO (Dark Mode)
        // ==========================================
        var logoLightUrl = '{{ asset("storage/theme-manager/" . $themeConfig->logo_light) }}';
        console.log('📦 Logo light URL:', logoLightUrl);

        var darkLogos = document.querySelectorAll('img[src*="dark-logo"], img[src*="light-logo"]');
        console.log('🔍 Logos dark mode encontrados:', darkLogos.length);
        darkLogos.forEach(function(img, index) {
            console.log('  ✓ Substituindo dark logo #' + (index + 1) + ':', img.src, '→', logoLightUrl);
            img.src = logoLightUrl;
        });
        @endif

        @if($themeConfig->favicon)
        // ==========================================
        // FAVICON
        // ==========================================
        var faviconUrl = '{{ asset("storage/theme-manager/" . $themeConfig->favicon) }}';
        console.log('📦 Favicon URL:', faviconUrl);

        var favicon = document.querySelector('link[rel="icon"]') || document.querySelector('link[rel="shortcut icon"]');
        if (favicon) {
            console.log('  ✓ Atualizando favicon existente:', favicon.href, '→', faviconUrl);
            favicon.href = faviconUrl;
        } else {
            console.log('  ✓ Criando novo favicon');
            var link = document.createElement('link');
            link.rel = 'icon';
            link.type = 'image/x-icon';
            link.href = faviconUrl;
            document.head.appendChild(link);
        }
        @endif

        @if($themeConfig->login_bg_image)
        // ==========================================
        // LOGIN PAGE BACKGROUND (JavaScript backup)
        // ==========================================
        if (window.location.pathname.includes('login') || window.location.pathname.includes('session')) {
            console.log('🖼️ ThemeManager: Aplicando background de login...');

            var bgUrl = '{{ asset("storage/theme-manager/" . $themeConfig->login_bg_image) }}';
            var bgZoom = {{ $themeConfig->login_bg_zoom ?? 100 }};
            var bgOpacity = {{ ($themeConfig->login_bg_opacity ?? 50) / 100 }};

            // Aplicar no body
            document.body.style.backgroundImage = 'url(' + bgUrl + ')';
            document.body.style.backgroundSize = bgZoom + '%';
            document.body.style.backgroundPosition = 'center';
            document.body.style.backgroundRepeat = 'no-repeat';
            document.body.style.backgroundAttachment = 'fixed';

            // Criar overlay de opacidade
            var overlay = document.createElement('div');
            overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,' + (1 - bgOpacity) + ');pointer-events:none;z-index:0;';
            document.body.insertBefore(overlay, document.body.firstChild);

            // Ajustar z-index do conteúdo
            var mainContent = document.body.children[1];
            if (mainContent) {
                mainContent.style.position = 'relative';
                mainContent.style.zIndex = '1';
            }

            console.log('✅ ThemeManager: Background de login aplicado!', {url: bgUrl, zoom: bgZoom, opacity: bgOpacity});
        }
        @endif

        @if(!$themeConfig->login_show_powered_by)
        // ==========================================
        // ESCONDER "POWERED BY KRAYIN"
        // ==========================================
        console.log('🔍 ThemeManager: Verificando "Powered By"...');

        // Buscar elementos que contenham "Powered by" no texto
        var allDivs = document.querySelectorAll('div, p, span, footer');
        var hiddenCount = 0;

        allDivs.forEach(function(el) {
            // Verificar se contém "Powered by" (case insensitive)
            if (el.textContent && el.textContent.match(/powered\s+by/i)) {
                // Verificar se não é um container pai (para não esconder tudo)
                var isDirectContainer = false;
                var childNodes = Array.from(el.childNodes);

                for (var i = 0; i < childNodes.length; i++) {
                    if (childNodes[i].nodeType === 3) { // Text node
                        var text = childNodes[i].textContent.trim();
                        if (text.match(/powered\s+by/i)) {
                            isDirectContainer = true;
                            break;
                        }
                    }
                }

                if (isDirectContainer) {
                    console.log('  ✓ Escondendo "Powered By":', el.className || el.tagName);
                    el.style.display = 'none';
                    hiddenCount++;
                }
            }
        });

        if (hiddenCount > 0) {
            console.log('✅ ThemeManager: ' + hiddenCount + ' elemento(s) "Powered By" escondido(s)!');
        } else {
            console.log('⚠️ ThemeManager: Nenhum "Powered By" encontrado para esconder.');
        }
        @endif

        console.log('✅ ThemeManager: Logos atualizados com sucesso!');
    });
</script>
@endif

@if($themeConfig->login_card_enabled)
{{-- ==========================================
     LOGIN CARD CUSTOMIZADO
     ========================================== --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Só aplicar na página de login
        if (!window.location.pathname.includes('login') && !window.location.pathname.includes('session')) {
            return;
        }

        console.log('🎨 ThemeManager: Aplicando Login Card customizado...');

        // Configurações do Login Card
        var config = {
            bgImage: '{{ $themeConfig->login_card_bg_image ? asset("storage/theme-manager/" . $themeConfig->login_card_bg_image) : "" }}',
            bgOpacity: {{ $themeConfig->login_card_bg_opacity ?? 62 }},
            overlayColor: '{{ $themeConfig->login_card_overlay_color ?? "rgba(10, 45, 15, 0.78)" }}',
            title: '{{ $themeConfig->login_card_title ?? "Bem-vindo" }}',
            subtitle: '{{ $themeConfig->login_card_subtitle ?? "Acesse sua conta para continuar" }}',
            sparkles: {{ $themeConfig->login_card_sparkles ? 'true' : 'false' }},
            helpLink: {{ $themeConfig->login_card_help_link ? 'true' : 'false' }},
            supportEmail: '{{ $themeConfig->login_card_support_email ?? "suporte@empresa.com.br" }}'
        };

        console.log('📦 Config:', config);

        // Encontrar o card de login (div.box-shadow com rounded-md bg-white)
        var loginCard = document.querySelector('.box-shadow.rounded-md.bg-white, .box-shadow.rounded-md.dark\\:bg-gray-900');

        if (!loginCard) {
            console.log('⚠️ Login card não encontrado');
            return;
        }

        console.log('✓ Login card encontrado:', loginCard);

        // 1. APLICAR BACKGROUND NO CARD (se configurado)
        if (config.bgImage) {
            loginCard.style.backgroundImage = 'url(' + config.bgImage + ')';
            loginCard.style.backgroundSize = 'cover';
            loginCard.style.backgroundPosition = 'center';
            loginCard.style.backgroundRepeat = 'no-repeat';
            loginCard.style.position = 'relative';

            // Criar overlay
            var overlay = document.createElement('div');
            overlay.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;background:' + config.overlayColor + ';border-radius:inherit;pointer-events:none;z-index:0;';
            loginCard.insertBefore(overlay, loginCard.firstChild);

            // Ajustar z-index dos children
            Array.from(loginCard.children).forEach(function(child, index) {
                if (index > 0) { // Pular o overlay
                    child.style.position = 'relative';
                    child.style.zIndex = '1';
                }
            });

            console.log('✓ Background aplicado com overlay');
        }

        // 2. INJETAR TÍTULO E SUBTÍTULO CUSTOMIZADOS
        // Procurar o título "Sign in" dentro do card
        var titleElement = loginCard.querySelector('p.text-xl.font-bold');

        if (titleElement) {
            // Criar container para título + subtítulo
            var headerContainer = document.createElement('div');
            headerContainer.style.cssText = 'text-align:center;margin-bottom:1rem;';

            // Título customizado
            var customTitle = document.createElement('h2');
            customTitle.textContent = config.title;
            customTitle.style.cssText = 'font-size:1.5rem;font-weight:700;color:inherit;margin-bottom:0.5rem;';

            // Subtítulo customizado
            var customSubtitle = document.createElement('p');
            customSubtitle.textContent = config.subtitle;
            customSubtitle.style.cssText = 'font-size:0.875rem;color:rgba(107, 114, 128, 1);';

            headerContainer.appendChild(customTitle);
            headerContainer.appendChild(customSubtitle);

            // Substituir título original
            titleElement.parentNode.replaceChild(headerContainer, titleElement);

            console.log('✓ Título e subtítulo aplicados');
        }

        // 3. EFEITO SPARKLES (opcional)
        if (config.sparkles) {
            var sparklesContainer = document.createElement('div');
            sparklesContainer.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;overflow:hidden;pointer-events:none;z-index:10;border-radius:inherit;';

            // Criar 15 sparkles aleatórios
            for (var i = 0; i < 15; i++) {
                var sparkle = document.createElement('div');
                var size = Math.random() * 4 + 2; // 2-6px
                var left = Math.random() * 100;
                var top = Math.random() * 100;
                var delay = Math.random() * 3;
                var duration = Math.random() * 2 + 2; // 2-4s

                sparkle.style.cssText = 'position:absolute;width:' + size + 'px;height:' + size + 'px;background:rgba(255,255,255,0.8);border-radius:50%;left:' + left + '%;top:' + top + '%;animation:sparkle ' + duration + 's ease-in-out ' + delay + 's infinite;';

                sparklesContainer.appendChild(sparkle);
            }

            // Adicionar animação CSS
            var style = document.createElement('style');
            style.textContent = '@keyframes sparkle { 0%, 100% { opacity: 0; transform: scale(0); } 50% { opacity: 1; transform: scale(1); } }';
            document.head.appendChild(style);

            loginCard.appendChild(sparklesContainer);

            console.log('✓ Sparkles aplicados');
        }

        // 4. LINK DE AJUDA COM EMAIL (opcional)
        if (config.helpLink) {
            // Procurar o container de botões (p-4 com justify-between)
            var buttonContainer = loginCard.querySelector('.flex.items-center.justify-between.p-4');

            if (buttonContainer) {
                // Criar link de ajuda
                var helpLink = document.createElement('div');
                helpLink.style.cssText = 'text-align:center;padding:1rem;border-top:1px solid rgba(229, 231, 235, 1);';

                var helpText = document.createElement('p');
                helpText.style.cssText = 'font-size:0.75rem;color:rgba(107, 114, 128, 1);margin-bottom:0.25rem;';
                helpText.textContent = 'Precisa de ajuda?';

                var emailLink = document.createElement('a');
                emailLink.href = 'mailto:' + config.supportEmail;
                emailLink.textContent = config.supportEmail;
                emailLink.style.cssText = 'font-size:0.75rem;color:var(--primary-color, #1E40AF);text-decoration:underline;';

                helpLink.appendChild(helpText);
                helpLink.appendChild(emailLink);

                // Adicionar após o form
                var form = loginCard.querySelector('form');
                if (form && form.nextSibling) {
                    form.parentNode.insertBefore(helpLink, form.nextSibling);
                } else if (form) {
                    form.parentNode.appendChild(helpLink);
                }

                console.log('✓ Link de ajuda adicionado');
            }
        }

        // 5. INJETAR CÓDIGO CUSTOMIZADO (HTML/CSS/JavaScript)
        @if($themeConfig->login_card_custom_code)
        console.log('📝 Injetando código customizado...');

        var customCodeContainer = document.createElement('div');
        customCodeContainer.innerHTML = {!! json_encode($themeConfig->login_card_custom_code) !!};

        // Extrair e injetar <style> no <head>
        var styles = customCodeContainer.querySelectorAll('style');
        styles.forEach(function(styleEl) {
            var newStyle = document.createElement('style');
            newStyle.textContent = styleEl.textContent;
            document.head.appendChild(newStyle);
            console.log('✓ CSS customizado injetado no <head>');
        });

        // Extrair e adicionar HTML (sem <style> e <script>) ao documento
        var htmlElements = Array.from(customCodeContainer.children).filter(function(el) {
            return el.tagName !== 'STYLE' && el.tagName !== 'SCRIPT';
        });

        htmlElements.forEach(function(el) {
            document.body.appendChild(el);
        });

        // Executar scripts inline (substituindo DOMContentLoaded por execução imediata)
        var scripts = customCodeContainer.querySelectorAll('script');
        scripts.forEach(function(oldScript) {
            var scriptContent = oldScript.textContent.trim();

            // Remover DOMContentLoaded wrapper se existir (métodos múltiplos)
            // Formato 1: document.addEventListener('DOMContentLoaded', function() { ... });
            if (scriptContent.indexOf('DOMContentLoaded') !== -1) {
                console.log('🔧 Removendo wrapper DOMContentLoaded do código customizado...');

                // Extrair apenas o conteúdo dentro da função
                var match = scriptContent.match(/addEventListener\s*\(\s*['"]DOMContentLoaded['"]\s*,\s*function\s*\(\s*\)\s*\{([\s\S]*)\}\s*\)\s*;?\s*$/);

                if (match && match[1]) {
                    scriptContent = match[1].trim();
                    console.log('✓ Wrapper removido, executando conteúdo interno');
                }
            }

            // Executar imediatamente
            try {
                eval(scriptContent);
                console.log('✓ JavaScript customizado executado');
            } catch (e) {
                console.error('❌ Erro ao executar JavaScript customizado:', e);
                console.error('Código:', scriptContent.substring(0, 200) + '...');
            }
        });

        console.log('✓ Código customizado injetado');
        @endif

        console.log('✅ ThemeManager: Login Card customizado aplicado!');
    });
</script>
@endif
