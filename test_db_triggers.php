<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Verificar triggers na tabela processos
$triggers = \Illuminate\Support\Facades\DB::select("SHOW TRIGGERS LIKE 'processos'");
echo "=== TRIGGERS ===\n";
print_r($triggers);

// Verificar estrutura da coluna
$columns = \Illuminate\Support\Facades\DB::select("SHOW FULL COLUMNS FROM processos WHERE Field = 'fase_processual'");
echo "\n=== COLUMN INFO ===\n";
print_r($columns);

// Testar outros valores
echo "\n=== TESTANDO VALORES ===\n";
$testValues = ['Inicial', 'Julgamento', 'Sentença', 'Julgamento final'];

foreach ($testValues as $val) {
    \Illuminate\Support\Facades\DB::table('processos')->where('id', 2)->update(['fase_processual' => $val]);
    $result = \Illuminate\Support\Facades\DB::table('processos')->where('id', 2)->value('fase_processual');
    echo "Valor '{$val}': " . ($result ?: 'VAZIO') . "\n";
}
