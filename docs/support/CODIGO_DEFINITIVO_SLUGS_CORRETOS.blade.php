@pushOnce('scripts')
<script>
(function() {
    // Theme data for selector with colors
    window.themeData = {};
    
    @foreach($availableThemes as $index => $theme)
        window.themeData['{{ $theme["slug"] }}'] = {
            slug: '{{ $theme["slug"] }}',
            name: {!! json_encode($theme['name'] ?? 'Unnamed Theme') !!},
            colors: {!! json_encode($theme['colors'] ?? [
                'primary' => '#1E40AF',
                'primary_dark' => '#1E3A8A',
                'primary_light' => '#3B82F6',
                'success' => '#10B981',
                'warning' => '#F59E0B',
                'danger' => '#EF4444'
            ]) !!}
        };
        console.log('✅ Added theme {{ $theme["slug"] }}:', window.themeData['{{ $theme["slug"] }}']);
    @endforeach

    console.log('✅ ThemeData initialized:', window.themeData);
    
    // Debug: show all theme colors with visual indicators
    console.log('=== THEME COLORS VERIFICATION ===');
    Object.keys(window.themeData).forEach(function(key) {
        const colors = window.themeData[key].colors;
        console.log('%c ' + key + ': ' + colors.primary + ' ', 
            'background: ' + colors.primary + '; color: white; padding: 2px 5px; border-radius: 3px;'
        );
    });
    console.log('=================================');

    // Toggle login card options
    window.toggleLoginCardOptions = function() {
        const checkbox = document.getElementById('login_card_enabled');
        const options = document.getElementById('login-card-options');
        if (checkbox && options) {
            options.style.display = checkbox.checked ? 'grid' : 'none';
        }
    };

    // Update overlay color preview
    window.updateOverlayColorPreview = function() {
        const input = document.getElementById('login_card_overlay_color');
        const preview = document.getElementById('overlay_color_preview');
        if (input && preview) {
            preview.style.backgroundColor = input.value;
        }
    };

    // Theme selector
    window.selectTheme = function(slug) {
        console.log('🎯 selectTheme called with:', slug);
        
        // Remove selection from all cards
        document.querySelectorAll('.theme-card').forEach(function(card) {
            card.classList.remove('border-blue-500', 'ring-2', 'ring-blue-200', 'dark:border-blue-400', 'dark:ring-blue-800', 'selected');
            card.classList.add('border-gray-200', 'dark:border-gray-700');
            
            const check = card.querySelector('.theme-check');
            if (check) {
                check.classList.add('hidden');
                check.classList.remove('block');
            }
            
            const radio = card.querySelector('input[type="radio"]');
            if (radio) radio.checked = false;
        });

        // Select clicked card
        const card = document.querySelector('[data-theme="' + slug + '"]');
        if (card) {
            card.classList.remove('border-gray-200', 'dark:border-gray-700');
            card.classList.add('border-blue-500', 'ring-2', 'ring-blue-200', 'dark:border-blue-400', 'dark:ring-blue-800', 'selected');
            
            const check = card.querySelector('.theme-check');
            if (check) {
                check.classList.remove('hidden');
                check.classList.add('block');
            }
            
            const radio = card.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        }

        window.updateColorPreview(slug);
    };

    // Update color preview
    window.updateColorPreview = function(slug) {
        console.log('🎨 updateColorPreview called with:', slug);
        
        if (!window.themeData || !window.themeData[slug]) {
            console.error('❌ Theme not found in themeData:', slug);
            console.log('Available themes:', Object.keys(window.themeData));
            return;
        }

        const theme = window.themeData[slug];
        console.log('✅ Theme found:', theme);
        
        const preview = document.getElementById('theme-preview');
        const nameEl = document.getElementById('theme-preview-name');

        if (!preview || !nameEl) {
            console.error('❌ Preview elements not found');
            return;
        }

        nameEl.textContent = theme.name;

        const colors = theme.colors || {};
        console.log('🎨 Setting colors:', colors);
        
        const els = {
            primary: document.getElementById('preview-primary'),
            primary_dark: document.getElementById('preview-primary-dark'),
            primary_light: document.getElementById('preview-primary-light'),
            success: document.getElementById('preview-success'),
            warning: document.getElementById('preview-warning'),
            danger: document.getElementById('preview-danger')
        };

        if (els.primary) els.primary.style.backgroundColor = colors.primary || '#1E40AF';
        if (els.primary_dark) els.primary_dark.style.backgroundColor = colors.primary_dark || '#1E3A8A';
        if (els.primary_light) els.primary_light.style.backgroundColor = colors.primary_light || '#3B82F6';
        if (els.success) els.success.style.backgroundColor = colors.success || '#10B981';
        if (els.warning) els.warning.style.backgroundColor = colors.warning || '#F59E0B';
        if (els.danger) els.danger.style.backgroundColor = colors.danger || '#EF4444';

        preview.classList.remove('hidden');
        console.log('✅ Preview updated successfully');
    };

    // Init on load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 DOMContentLoaded fired');
        console.log('📊 themeData on load:', window.themeData);
        
        toggleLoginCardOptions();

        const overlayInput = document.getElementById('login_card_overlay_color');
        if (overlayInput) {
            overlayInput.addEventListener('input', updateOverlayColorPreview);
            overlayInput.addEventListener('change', updateOverlayColorPreview);
        }

        // Sync color pickers
        document.querySelectorAll('input[type="color"]').forEach(function(picker) {
            const name = picker.name;
            const textInputs = document.querySelectorAll('input[type="text"][name="' + name + '"]');
            
            picker.addEventListener('input', function() {
                textInputs.forEach(function(txt) {
                    txt.value = picker.value.toUpperCase();
                });
            });

            textInputs.forEach(function(txt) {
                txt.addEventListener('input', function() {
                    if (txt.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                        picker.value = txt.value;
                    }
                });
            });
        });

        // Init preview for selected theme
        const selected = document.querySelector('.theme-card.selected');
        if (selected) {
            const slug = selected.getAttribute('data-theme');
            if (slug) {
                console.log('📌 Initially selected theme:', slug);
                updateColorPreview(slug);
            }
        } else {
            const checkedRadio = document.querySelector('.theme-card input[type="radio"]:checked');
            if (checkedRadio) {
                const card = checkedRadio.closest('.theme-card');
                if (card) {
                    const slug = card.getAttribute('data-theme');
                    console.log('📌 Initially selected theme (from radio):', slug);
                    if (slug) updateColorPreview(slug);
                }
            }
        }
    });
})();
</script>
@endPushOnce
