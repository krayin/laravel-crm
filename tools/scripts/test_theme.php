<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE COMPLETO DO THEMEMANAGER ===\n\n";

// TESTE 1: Banco de Dados
echo "TESTE 1: BANCO DE DADOS\n";
echo str_repeat("-", 50) . "\n";
try {
    $config = \Webkul\ThemeManager\Models\ThemeConfig::getInstance();
    echo "✓ Model carregado com sucesso\n";
    echo "  - ID: " . $config->id . "\n";
    echo "  - Ativo: " . ($config->is_active ? 'SIM' : 'NAO') . "\n";
    echo "  - Cor Primary: " . $config->color_primary . "\n";
    echo "  - Total de campos fillable: " . count($config->getFillable()) . "\n";
    echo "  - Criado em: " . $config->created_at . "\n";
} catch (\Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
}
echo "\n";

// TESTE 2: Helper e Singleton
echo "TESTE 2: HELPER E SINGLETON\n";
echo str_repeat("-", 50) . "\n";
try {
    $helper = app('theme');
    echo "✓ Helper 'theme' resolvido com sucesso\n";
    echo "  - Classe: " . get_class($helper) . "\n";
    echo "  - isActive(): " . ($helper->isActive() ? 'true' : 'false') . "\n";

    $config2 = $helper->getConfig();
    echo "✓ getConfig() executado\n";
    echo "  - Retornou objeto: " . (is_object($config2) ? 'SIM' : 'NAO') . "\n";
} catch (\Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
}
echo "\n";

// TESTE 3: CSS Variables
echo "TESTE 3: CSS VARIABLES\n";
echo str_repeat("-", 50) . "\n";
try {
    $css = $helper->getCssVariables();
    echo "✓ getCssVariables() executado\n";
    echo "  - Tamanho do CSS: " . strlen($css) . " bytes\n";
    echo "  - Contém :root: " . (strpos($css, ':root') !== false ? 'SIM' : 'NAO') . "\n";
    echo "  - Contém --primary-color: " . (strpos($css, '--primary-color') !== false ? 'SIM' : 'NAO') . "\n";
    echo "  - Primeiros 100 chars: " . substr($css, 0, 100) . "...\n";
} catch (\Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
}
echo "\n";

// TESTE 4: Rotas
echo "TESTE 4: ROTAS REGISTRADAS\n";
echo str_repeat("-", 50) . "\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $themeRoutes = [];
    foreach ($routes as $route) {
        if (strpos($route->getName(), 'admin.settings.theme') !== false) {
            $themeRoutes[] = $route;
        }
    }
    echo "✓ Rotas do ThemeManager encontradas: " . count($themeRoutes) . "\n";
    foreach ($themeRoutes as $route) {
        echo "  - " . implode('|', $route->methods()) . " " . $route->uri() . " => " . $route->getName() . "\n";
    }
} catch (\Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
}
echo "\n";

// TESTE 5: Menu Configuration
echo "TESTE 5: CONFIGURACAO DO MENU\n";
echo str_repeat("-", 50) . "\n";
try {
    $menuConfig = config('menu.admin');
    $themeMenu = null;
    foreach ($menuConfig as $item) {
        if (isset($item['key']) && strpos($item['key'], 'theme') !== false) {
            $themeMenu = $item;
            break;
        }
    }
    if ($themeMenu) {
        echo "✓ Menu do ThemeManager encontrado\n";
        echo "  - Key: " . $themeMenu['key'] . "\n";
        echo "  - Name: " . $themeMenu['name'] . "\n";
        echo "  - Route: " . $themeMenu['route'] . "\n";
        echo "  - Sort: " . $themeMenu['sort'] . "\n";
        echo "  - Icon: " . $themeMenu['icon-class'] . "\n";
    } else {
        echo "✗ Menu do ThemeManager NÃO encontrado\n";
    }
} catch (\Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
}
echo "\n";

// TESTE 6: Traduções
echo "TESTE 6: TRADUCOES\n";
echo str_repeat("-", 50) . "\n";
try {
    $langs = ['en', 'pt_BR'];
    foreach ($langs as $lang) {
        app()->setLocale($lang);
        $trans = trans('theme-manager::app.menu.theme');
        echo "✓ Tradução [$lang]: $trans\n";
    }
} catch (\Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
}
echo "\n";

// TESTE 7: Views
echo "TESTE 7: VIEWS REGISTRADAS\n";
echo str_repeat("-", 50) . "\n";
try {
    $viewPaths = [
        'theme-manager::admin.settings.theme.index',
        'theme-manager::components.theme-styles',
        'admin::sessions.login', // Override
    ];

    foreach ($viewPaths as $view) {
        $exists = \Illuminate\Support\Facades\View::exists($view);
        echo ($exists ? "✓" : "✗") . " View '$view': " . ($exists ? "EXISTE" : "NÃO EXISTE") . "\n";
    }
} catch (\Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
}
echo "\n";

// TESTE 8: Service Providers
echo "TESTE 8: SERVICE PROVIDERS\n";
echo str_repeat("-", 50) . "\n";
try {
    $providers = config('app.providers');
    $themeProviders = array_filter($providers, function($p) {
        return strpos($p, 'ThemeManager') !== false;
    });
    echo "✓ ThemeManager Providers encontrados: " . count($themeProviders) . "\n";
    foreach ($themeProviders as $provider) {
        echo "  - $provider\n";
    }
} catch (\Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
}
echo "\n";

// TESTE 9: Middleware
echo "TESTE 9: MIDDLEWARE\n";
echo str_repeat("-", 50) . "\n";
try {
    $router = app('router');
    $middlewareGroups = $router->getMiddlewareGroups();

    if (isset($middlewareGroups['web'])) {
        $hasThemeMiddleware = false;
        foreach ($middlewareGroups['web'] as $middleware) {
            if (strpos($middleware, 'ThemeMiddleware') !== false) {
                $hasThemeMiddleware = true;
                echo "✓ ThemeMiddleware encontrado no grupo 'web'\n";
                echo "  - Classe: $middleware\n";
                break;
            }
        }
        if (!$hasThemeMiddleware) {
            echo "✗ ThemeMiddleware NÃO encontrado no grupo 'web'\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
}
echo "\n";

// TESTE 10: Composer Autoload
echo "TESTE 10: COMPOSER AUTOLOAD\n";
echo str_repeat("-", 50) . "\n";
try {
    $composerJson = json_decode(file_get_contents(__DIR__.'/composer.json'), true);
    if (isset($composerJson['autoload']['psr-4']['Webkul\\ThemeManager\\'])) {
        echo "✓ PSR-4 autoload configurado\n";
        echo "  - Namespace: Webkul\\ThemeManager\\\n";
        echo "  - Path: " . $composerJson['autoload']['psr-4']['Webkul\\ThemeManager\\'] . "\n";
    } else {
        echo "✗ PSR-4 autoload NÃO configurado\n";
    }
} catch (\Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== FIM DOS TESTES ===\n";
