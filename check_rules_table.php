<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class)->bootstrap();

echo "Rules Table Structure\n";
echo "====================\n";

try {
    $columns = \DB::select('DESCRIBE rules');
    echo "Current columns in rules table:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    echo "\nChecking for description column:\n";
    $hasDescription = collect($columns)->contains(function($col) {
        return $col->Field === 'description';
    });
    
    if ($hasDescription) {
        echo "✅ Description column exists\n";
    } else {
        echo "❌ Description column is missing\n";
        echo "\nSolution: We need to either:\n";
        echo "1. Remove 'description' from the form and validation\n";
        echo "2. Create a migration to add the 'description' column\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
