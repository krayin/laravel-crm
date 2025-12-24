<?php

require __DIR__ . "/vendor/autoload.php";

$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// ============================================================
// STELIUM v2 - Código corrigido com JavaScript direto
// ============================================================
// Problemas resolvidos:
// 1. Seletores CSS com escape \[ não funcionam via innerHTML
// 2. Pseudo-elementos ::before precisam de position:relative primeiro
// 3. JavaScript aplica estilos diretamente para garantir especificidade
// ============================================================

$customCode = <<<'CODE'
<style id="stelium-styles">
/* STELIUM v2 - Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Philosopher:wght@400;700&display=swap');

/* Animação de entrada */
@keyframes steliumCardIn {
    from { opacity: 0; transform: translateY(16px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

@keyframes steliumSparkle {
    0%, 100% { opacity: 0; transform: scale(0); }
    50% { opacity: 1; transform: scale(1); }
}

/* Card com ID para máxima especificidade */
#stelium-login-card {
    position: relative !important;
    background: rgba(10, 45, 15, 0.95) !important;
    border-radius: 20px !important;
    overflow: hidden !important;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.12), 0 12px 48px rgba(0, 0, 0, 0.08) !important;
    min-width: 380px !important;
    animation: steliumCardIn 0.5s ease forwards !important;
}

/* Background místico via pseudo-elemento */
#stelium-login-card::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background-image: url('https://i.imgur.com/3OgU2w0.png') !important;
    background-size: cover !important;
    background-position: center top !important;
    opacity: 0.15 !important;
    z-index: 0 !important;
    pointer-events: none !important;
    border-radius: inherit !important;
}

/* Todos os filhos devem ficar acima do ::before */
#stelium-login-card > * {
    position: relative !important;
    z-index: 1 !important;
}

/* Header do card (título) */
#stelium-header {
    text-align: center !important;
    padding: 32px 32px 8px 32px !important;
    font-family: 'Philosopher', Georgia, serif !important;
}

#stelium-header .stelium-star {
    font-size: 2rem !important;
    display: block !important;
    margin-bottom: 8px !important;
}

#stelium-header h2 {
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    margin: 0 !important;
    text-shadow: 0 2px 12px rgba(0, 0, 0, 0.3) !important;
}

#stelium-header p {
    color: rgba(255, 255, 255, 0.75) !important;
    font-size: 0.875rem !important;
    margin: 8px 0 0 0 !important;
}

/* Área dos inputs */
#stelium-login-card .border-y {
    border: none !important;
    padding: 16px 32px !important;
    background: transparent !important;
}

#stelium-login-card label {
    font-size: 0.8125rem !important;
    color: rgba(255, 255, 255, 0.9) !important;
}

#stelium-login-card input[type="email"],
#stelium-login-card input[type="password"],
#stelium-login-card input[type="text"] {
    height: 48px !important;
    padding: 0 16px !important;
    color: #1a1816 !important;
    background: #ffffff !important;
    border: 2px solid transparent !important;
    border-radius: 12px !important;
    transition: border-color 0.2s, box-shadow 0.2s !important;
}

#stelium-login-card input:focus {
    border-color: #bd9f57 !important;
    box-shadow: 0 0 0 3px rgba(189, 159, 87, 0.25) !important;
    outline: none !important;
}

/* Área dos botões */
#stelium-login-card .flex.items-center.justify-between {
    padding: 24px 32px 32px 32px !important;
}

#stelium-login-card a {
    color: #d4bb7a !important;
    text-decoration: none !important;
}

#stelium-login-card a:hover {
    color: #e8d49f !important;
    text-decoration: underline !important;
}

#stelium-login-card button.primary-button {
    height: 46px !important;
    color: #0a2d0f !important;
    background: linear-gradient(135deg, #d4bb7a 0%, #bd9f57 100%) !important;
    border: none !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    transition: transform 0.2s, box-shadow 0.2s !important;
}

#stelium-login-card button.primary-button:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(189, 159, 87, 0.4) !important;
}

/* Ícone de visibilidade da senha */
#stelium-login-card .icon-eye-hide,
#stelium-login-card .icon-eye {
    color: #666 !important;
}

/* Sparkles */
.stelium-sparkle {
    position: absolute !important;
    background: rgba(255, 255, 255, 0.8) !important;
    border-radius: 50% !important;
    pointer-events: none !important;
    z-index: 10 !important;
}

/* Esconder powered by */
.text-sm.font-normal {
    display: none !important;
}
</style>

<script>
(function() {
    console.log('🌟 Stelium v2: Iniciando...');

    // 1. APLICAR BACKGROUND NA PÁGINA
    document.body.style.background = '#f0eeeb';
    var pageContainer = document.querySelector('.flex.h-\\[100vh\\]');
    if (pageContainer) {
        pageContainer.style.background = '#f0eeeb';
    }

    // 2. ENCONTRAR E MARCAR O CARD
    var card = document.querySelector('.box-shadow.rounded-md');
    if (!card) {
        console.error('❌ Stelium: Card não encontrado');
        return;
    }

    // Adicionar ID para especificidade máxima
    card.id = 'stelium-login-card';

    // Remover classes que podem conflitar
    card.classList.remove('bg-white', 'dark:bg-gray-900');

    console.log('✓ Card encontrado e marcado com ID');

    // 3. SUBSTITUIR TÍTULO
    var originalTitle = card.querySelector('p.text-xl.font-bold');
    if (originalTitle) {
        // Criar novo header
        var header = document.createElement('div');
        header.id = 'stelium-header';
        header.innerHTML = '<span class="stelium-star">✨</span><h2>Bem-vindo</h2><p>Acesse sua conta para continuar</p>';

        // Substituir
        originalTitle.parentNode.replaceChild(header, originalTitle);
        console.log('✓ Título substituído');
    }

    // 4. ADICIONAR SPARKLES
    for (var i = 0; i < 12; i++) {
        var sparkle = document.createElement('div');
        sparkle.className = 'stelium-sparkle';
        var size = Math.random() * 4 + 2;
        var left = Math.random() * 100;
        var top = Math.random() * 100;
        var delay = Math.random() * 3;
        var duration = Math.random() * 2 + 2;

        sparkle.style.cssText = 'width:' + size + 'px;height:' + size + 'px;left:' + left + '%;top:' + top + '%;animation:steliumSparkle ' + duration + 's ease-in-out ' + delay + 's infinite;';
        card.appendChild(sparkle);
    }
    console.log('✓ Sparkles adicionados');

    // 5. ESCONDER POWERED BY
    var poweredBy = document.querySelector('.text-sm.font-normal');
    if (poweredBy) {
        poweredBy.style.display = 'none';
        console.log('✓ Powered by escondido');
    }

    console.log('✅ Stelium v2: Aplicado com sucesso!');
})();
</script>
CODE;

// Atualizar no banco
$config = \Webkul\ThemeManager\Models\ThemeConfig::firstOrCreate(["id" => 1]);
$config->login_card_custom_code = $customCode;
$config->save();

echo "============================================================\n";
echo "  STELIUM v2 - Custom Code Atualizado\n";
echo "============================================================\n";
echo "\n";
echo "✅ Código salvo com sucesso!\n";
echo "📏 Tamanho: " . strlen($customCode) . " caracteres\n";
echo "\n";
echo "🔧 Correções aplicadas:\n";
echo "   1. ID no card para máxima especificidade CSS\n";
echo "   2. JavaScript aplica estilos diretamente\n";
echo "   3. Remoção de classes conflitantes (bg-white)\n";
echo "   4. Pseudo-elemento ::before com position correto\n";
echo "\n";
echo "🧪 Próximo passo:\n";
echo "   1. Limpe o cache: php artisan cache:clear\n";
echo "   2. Recarregue: http://127.0.0.1:8000/admin/login\n";
echo "   3. Abra o Console (F12) para ver logs\n";
echo "\n";
