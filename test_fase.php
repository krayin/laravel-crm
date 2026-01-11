<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$processo = \SuiteZap\LawFirm\Models\Processo::find(2);

echo "=== BEFORE ===\n";
echo "ID: " . $processo->id . "\n";
echo "Fase Processual: " . ($processo->fase_processual ?? 'NULL') . "\n";

$processo->fase_processual = 'Julgamento';
$saved = $processo->save();

echo "\n=== SAVE RESULT ===\n";
echo "Saved: " . ($saved ? 'YES' : 'NO') . "\n";

$processo->refresh();

echo "\n=== AFTER REFRESH ===\n";
echo "Fase Processual: " . ($processo->fase_processual ?? 'NULL') . "\n";

// Test directly
$directQuery = \Illuminate\Support\Facades\DB::table('processos')->where('id', 2)->first();
echo "\n===DIRECT DB QUERY ===\n";
echo "Fase Processual (direct): " . ($directQuery->fase_processual ?? 'NULL') . "\n";
