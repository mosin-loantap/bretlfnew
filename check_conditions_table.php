<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class)->bootstrap();

echo "Rule Conditions Table Structure\n";
echo "===============================\n";

try {
    $columns = \DB::select('DESCRIBE rule_conditions');
    echo "Current columns in rule_conditions table:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    echo "\nChecking fillable fields in RuleCondition model:\n";
    $condition = new \App\Models\RuleCondition();
    $fillable = $condition->getFillable();
    foreach ($fillable as $field) {
        echo "- {$field}\n";
    }
    
    echo "\nChecking for missing columns:\n";
    $tableColumns = array_column($columns, 'Field');
    $missingColumns = [];
    
    foreach ($fillable as $field) {
        if (!in_array($field, $tableColumns)) {
            $missingColumns[] = $field;
        }
    }
    
    if (empty($missingColumns)) {
        echo "✅ All fillable fields exist in database table\n";
    } else {
        echo "❌ Missing columns in database table:\n";
        foreach ($missingColumns as $col) {
            echo "   - {$col}\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
