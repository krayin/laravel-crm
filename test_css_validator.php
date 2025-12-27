<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$v = app(\App\Support\CssValidator::class);

echo "=== CssValidator Tests ===" . PHP_EOL . PHP_EOL;

// Test 1: Valid CSS
$test1 = $v->isValid('#brand-kit-scope .btn{color:red;}');
echo "Test 1 (valid CSS): " . ($test1 ? 'PASS' : 'FAIL') . PHP_EOL;

// Test 2: @import blocked
$test2 = $v->isValid('@import url("x");');
echo "Test 2 (@import blocked): " . (!$test2 ? 'PASS' : 'FAIL') . PHP_EOL;

// Test 3: javascript: blocked
$test3 = $v->isValid('.bg{background:url(javascript:alert(1))}');
echo "Test 3 (javascript: blocked): " . (!$test3 ? 'PASS' : 'FAIL') . PHP_EOL;

// Test 4: data: blocked
$test4 = $v->isValid('.bg{background:url(data:text/html,<script>)}');
echo "Test 4 (data: blocked): " . (!$test4 ? 'PASS' : 'FAIL') . PHP_EOL;

// Test 5: expression blocked
$test5 = $v->isValid('.x{width:expression(alert(1))}');
echo "Test 5 (expression blocked): " . (!$test5 ? 'PASS' : 'FAIL') . PHP_EOL;

// Test 6: sanitize output
$sanitized = $v->sanitize('#brand-kit-scope .btn{color:red;}');
echo "Test 6 (sanitize): " . $sanitized . PHP_EOL;

echo PHP_EOL . "=== All Tests Complete ===" . PHP_EOL;
