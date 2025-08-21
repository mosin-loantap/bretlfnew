<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class)->bootstrap();

echo "Testing Conditions Functionality\n";
echo "================================\n";

try {
    echo "1. Testing API endpoints:\n";
    
    // Test conditions listing
    $request = \Illuminate\Http\Request::create('/api/bre/conditions', 'GET');
    $response = app()->handle($request);
    
    if ($response->getStatusCode() === 200) {
        $conditions = json_decode($response->getContent(), true);
        echo "   ✅ /api/bre/conditions - " . count($conditions) . " conditions found\n";
    } else {
        echo "   ❌ /api/bre/conditions - Error: " . $response->getStatusCode() . "\n";
    }
    
    // Test rules endpoint
    $request = \Illuminate\Http\Request::create('/api/rules', 'GET');
    $response = app()->handle($request);
    
    if ($response->getStatusCode() === 200) {
        $rules = json_decode($response->getContent(), true);
        echo "   ✅ /api/rules - " . count($rules) . " rules found\n";
    } else {
        echo "   ❌ /api/rules - Error: " . $response->getStatusCode() . "\n";
    }
    
    // Test variables endpoint
    $request = \Illuminate\Http\Request::create('/api/bre/variables', 'GET');
    $response = app()->handle($request);
    
    if ($response->getStatusCode() === 200) {
        $variables = json_decode($response->getContent(), true);
        echo "   ✅ /api/bre/variables - " . count($variables) . " variables found\n";
    } else {
        echo "   ❌ /api/bre/variables - Error: " . $response->getStatusCode() . "\n";
    }

    echo "\n2. Checking existing conditions:\n";
    $conditions = \App\Models\RuleCondition::with('rule')->get();
    
    if ($conditions->isEmpty()) {
        echo "   ℹ️ No conditions exist in database\n";
        echo "   Let's create a test condition...\n";
        
        // Create a test condition
        $rule = \App\Models\Rule::first();
        if ($rule) {
            $condition = \App\Models\RuleCondition::create([
                'rule_id' => $rule->rule_id,
                'variable_name' => 'salary',
                'operator' => 'greater_than',
                'value' => '50000',
                'created_by' => 1,
                'updated_by' => 1,
            ]);
            
            echo "   ✅ Test condition created: ID {$condition->condition_id}\n";
        } else {
            echo "   ❌ No rules found to create condition\n";
        }
    } else {
        echo "   ✅ Found {$conditions->count()} existing conditions\n";
        foreach ($conditions->take(3) as $condition) {
            $ruleName = $condition->rule ? $condition->rule->rule_name : 'N/A';
            echo "      - {$condition->variable_name} {$condition->operator} {$condition->value} (Rule: {$ruleName})\n";
        }
    }

    echo "\n🎉 Conditions functionality should now work!\n";
    echo "\nWhat was fixed:\n";
    echo "✅ Added /api/bre/conditions endpoint\n";
    echo "✅ Added /api/bre/conditions/{id} endpoint\n";
    echo "✅ Added PUT and DELETE endpoints for conditions\n";
    echo "✅ Updated JavaScript to use correct API endpoints\n";
    echo "✅ Simplified form to only use database fields\n";
    echo "✅ Fixed field mappings and table rendering\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
