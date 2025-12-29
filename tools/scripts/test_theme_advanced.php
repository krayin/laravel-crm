<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTES AVANÇADOS DO THEMEMANAGER ===\n\n";

// TESTE 11: Cache
echo "TESTE 11: SISTEMA DE CACHE\n";
echo str_repeat('-', 50)."\n";
try {
    $helper = app('theme');

    // Limpar cache
    $helper->clearCache();
    echo "✓ Cache limpo\n";

    // Primeira chamada (sem cache)
    $start = microtime(true);
    $config1 = $helper->getConfig();
    $time1 = (microtime(true) - $start) * 1000;
    echo '✓ Primeira chamada (sem cache): '.number_format($time1, 2)."ms\n";

    // Segunda chamada (com cache)
    $start = microtime(true);
    $config2 = $helper->getConfig();
    $time2 = (microtime(true) - $start) * 1000;
    echo '✓ Segunda chamada (com cache): '.number_format($time2, 2)."ms\n";

    echo '  - Cache está funcionando: '.($time2 < $time1 ? 'SIM' : 'NAO')."\n";
    echo '  - Velocidade aumentou: '.number_format(($time1 - $time2) / $time1 * 100, 1)."%\n";
} catch (\Exception $e) {
    echo '✗ ERRO: '.$e->getMessage()."\n";
}
echo "\n";

// TESTE 12: Todos os campos do banco
echo "TESTE 12: CAMPOS DO BANCO DE DADOS\n";
echo str_repeat('-', 50)."\n";
try {
    $config = \Webkul\ThemeManager\Models\ThemeConfig::getInstance();
    $data = $config->toArray();

    $categories = [
        'Ativação'         => ['is_active'],
        'Cores'            => ['color_primary', 'color_primary_dark', 'color_primary_light', 'color_success', 'color_warning', 'color_danger'],
        'Logos'            => ['logo_main', 'logo_light', 'logo_icon', 'favicon'],
        'Login Background' => ['login_bg_image', 'login_bg_zoom', 'login_bg_opacity', 'login_show_powered_by'],
        'Login Card'       => ['login_card_enabled', 'login_card_bg_image', 'login_card_bg_opacity', 'login_card_overlay_color', 'login_card_title', 'login_card_subtitle', 'login_card_sparkles', 'login_card_help_link', 'login_card_support_email'],
        'Empty States'     => ['empty_state_activities', 'empty_state_calls', 'empty_state_emails', 'empty_state_meetings', 'empty_state_notes', 'empty_state_organizations', 'empty_state_persons', 'empty_state_leads', 'empty_state_products'],
    ];

    $totalFields = 0;
    $presentFields = 0;

    foreach ($categories as $category => $fields) {
        echo "  [$category]\n";
        foreach ($fields as $field) {
            $totalFields++;
            $exists = array_key_exists($field, $data);
            $presentFields += $exists ? 1 : 0;
            $value = $exists ? $data[$field] : 'N/A';

            // Truncate long values
            if (is_string($value) && strlen($value) > 40) {
                $value = substr($value, 0, 40).'...';
            } elseif (is_null($value)) {
                $value = 'NULL';
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            echo ($exists ? '    ✓' : '    ✗')." $field: $value\n";
        }
    }

    echo "\n✓ Total de campos: $totalFields\n";
    echo "✓ Campos presentes: $presentFields\n";
    echo '✓ Cobertura: '.number_format($presentFields / $totalFields * 100, 1)."%\n";

} catch (\Exception $e) {
    echo '✗ ERRO: '.$e->getMessage()."\n";
}
echo "\n";

// TESTE 13: Repository
echo "TESTE 13: REPOSITORY\n";
echo str_repeat('-', 50)."\n";
try {
    $repo = app(\Webkul\ThemeManager\Repositories\ThemeConfigRepository::class);
    echo "✓ Repository resolvido via container\n";
    echo '  - Classe: '.get_class($repo)."\n";

    // Verificar métodos públicos
    $methods = get_class_methods($repo);
    $publicMethods = array_filter($methods, function ($m) {
        $reflection = new \ReflectionMethod(\Webkul\ThemeManager\Repositories\ThemeConfigRepository::class, $m);

        return $reflection->isPublic() && ! $reflection->isStatic();
    });

    echo '  - Métodos públicos: '.count($publicMethods)."\n";
    foreach ($publicMethods as $method) {
        if (! in_array($method, ['__construct'])) {
            echo "    • $method\n";
        }
    }
} catch (\Exception $e) {
    echo '✗ ERRO: '.$e->getMessage()."\n";
}
echo "\n";

// TESTE 14: Controller
echo "TESTE 14: CONTROLLER\n";
echo str_repeat('-', 50)."\n";
try {
    $controller = app(\Webkul\ThemeManager\Http\Controllers\ThemeController::class);
    echo "✓ Controller resolvido via container\n";
    echo '  - Classe: '.get_class($controller)."\n";

    $methods = get_class_methods($controller);
    $publicMethods = array_filter($methods, function ($m) {
        $reflection = new \ReflectionMethod(\Webkul\ThemeManager\Http\Controllers\ThemeController::class, $m);

        return $reflection->isPublic() && ! $reflection->isStatic() && ! in_array($m, ['__construct', 'getMiddleware', 'callAction']);
    });

    echo '  - Métodos de ação: '.count($publicMethods)."\n";
    foreach ($publicMethods as $method) {
        echo "    • $method()\n";
    }
} catch (\Exception $e) {
    echo '✗ ERRO: '.$e->getMessage()."\n";
}
echo "\n";

// TESTE 15: Arquivos de tradução
echo "TESTE 15: ARQUIVOS DE TRADUCAO\n";
echo str_repeat('-', 50)."\n";
try {
    $langPath = __DIR__.'/packages/Webkul/ThemeManager/Resources/lang';
    $languages = ['en', 'pt_BR'];

    foreach ($languages as $lang) {
        $file = "$langPath/$lang/app.php";
        if (file_exists($file)) {
            $translations = include $file;
            $count = count($translations, COUNT_RECURSIVE) - count($translations);
            echo "✓ [$lang] arquivo existe\n";
            echo "  - Total de chaves: $count\n";
            echo '  - Seções: '.implode(', ', array_keys($translations))."\n";
        } else {
            echo "✗ [$lang] arquivo NÃO existe\n";
        }
    }
} catch (\Exception $e) {
    echo '✗ ERRO: '.$e->getMessage()."\n";
}
echo "\n";

// TESTE 16: Estrutura de diretórios
echo "TESTE 16: ESTRUTURA DE DIRETORIOS\n";
echo str_repeat('-', 50)."\n";
try {
    $basePath = __DIR__.'/packages/Webkul/ThemeManager';

    $requiredDirs = [
        'src',
        'src/Providers',
        'src/Http',
        'src/Http/Controllers',
        'src/Http/Middleware',
        'src/Models',
        'src/Repositories',
        'src/Helpers',
        'src/Config',
        'src/Routes',
        'src/Contracts',
        'Resources',
        'Resources/views',
        'Resources/lang',
        'Database',
        'Database/Migrations',
    ];

    $existingDirs = 0;
    foreach ($requiredDirs as $dir) {
        $fullPath = "$basePath/$dir";
        $exists = is_dir($fullPath);
        $existingDirs += $exists ? 1 : 0;
        echo ($exists ? '  ✓' : '  ✗')." $dir\n";
    }

    echo "\n✓ Diretórios existentes: $existingDirs/".count($requiredDirs)."\n";
    echo '✓ Cobertura: '.number_format($existingDirs / count($requiredDirs) * 100, 1)."%\n";

} catch (\Exception $e) {
    echo '✗ ERRO: '.$e->getMessage()."\n";
}
echo "\n";

// TESTE 17: Migration
echo "TESTE 17: MIGRATION\n";
echo str_repeat('-', 50)."\n";
try {
    $migrationPath = __DIR__.'/packages/Webkul/ThemeManager/Database/Migrations';
    $files = glob("$migrationPath/*.php");

    echo '✓ Migrations encontradas: '.count($files)."\n";
    foreach ($files as $file) {
        echo '  - '.basename($file)."\n";
    }

    // Verificar se a tabela existe
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('theme_configs');
    echo "\n✓ Tabela 'theme_configs' existe: ".($tableExists ? 'SIM' : 'NAO')."\n";

    if ($tableExists) {
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('theme_configs');
        echo '✓ Total de colunas: '.count($columns)."\n";
    }

} catch (\Exception $e) {
    echo '✗ ERRO: '.$e->getMessage()."\n";
}
echo "\n";

// TESTE 18: Concord/Module
echo "TESTE 18: CONCORD MODULE\n";
echo str_repeat('-', 50)."\n";
try {
    $concordConfig = config('concord.modules');
    $themeModule = null;

    foreach ($concordConfig as $module) {
        if (strpos($module, 'ThemeManager') !== false) {
            $themeModule = $module;
            break;
        }
    }

    if ($themeModule) {
        echo "✓ Módulo registrado no Concord\n";
        echo "  - Provider: $themeModule\n";
    } else {
        echo "✗ Módulo NÃO registrado no Concord\n";
    }

    // Verificar se ModuleServiceProvider existe
    $moduleProvider = 'Webkul\ThemeManager\Providers\ModuleServiceProvider';
    if (class_exists($moduleProvider)) {
        echo "✓ ModuleServiceProvider existe\n";

        $provider = new $moduleProvider(app());
        $reflection = new \ReflectionClass($provider);
        echo '  - Classe base: '.$reflection->getParentClass()->getName()."\n";
    }

} catch (\Exception $e) {
    echo '✗ ERRO: '.$e->getMessage()."\n";
}
echo "\n";

echo "=== FIM DOS TESTES AVANÇADOS ===\n";
