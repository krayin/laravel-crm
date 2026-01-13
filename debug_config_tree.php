<?php
// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $items = system_config()->getItems();
    echo "Total Items: " . count($items) . "\n";
    foreach ($items as $item) {
        echo "Key: [" . $item->getKey() . "]\n";
        echo "  Name: " . $item->getName() . "\n";

        $children = $item->getChildren();
        echo "  Children: " . count($children) . "\n";
        foreach ($children as $child) {
            echo "    - Child Key: [" . $child->getKey() . "]\n";
            echo "      Child Name: " . $child->getName() . "\n";

            $fields = $child->getFields();
            echo "      Fields: " . count($fields) . "\n";

            $grandChildren = $child->getChildren();
            echo "      GrandChildren: " . count($grandChildren) . "\n";
            foreach ($grandChildren as $grandChild) {
                echo "        + GC Key: [" . $grandChild->getKey() . "]\n";
                echo "          GC Name: " . $grandChild->getName() . "\n";
            }
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
