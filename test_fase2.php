<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Teste SQL direto
echo "=== SQL DIRETO ===\n";
\Illuminate\Support\Facades\DB::table('processos')->where('id', 2)->update(['fase_processual' => 'Julgamento']);
$result = \Illuminate\Support\Facades\DB::table('processos')->where('id', 2)->first();
echo "Fase após update SQL: " . ($result->fase_processual ?? 'NULL') . "\n";

// Teste com Process Observer desabilitado
echo "\n=== SEM OBSERVER ===\n";
\SuiteZap\LawFirm\Models\Processo::unsetEventDispatcher();
$p = \SuiteZap\LawFirm\Models\Processo::find(2);
$p->fase_processual = 'Julgamento';
$p->save();
$p->refresh();
echo "Fase (sem observer): " . ($p->fase_processual ?? 'NULL') . "\n";
