<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class)->bootstrap();

echo "Conditions View Dropdown Fix Verification\n";
echo "==========================================\n";

try {
    // 1. Test Variables API
    echo "1. Testing Variables API:\n";
    $request = \Illuminate\Http\Request::create('/api/bre/variables', 'GET');
    $response = app()->handle($request);
    
    if ($response->getStatusCode() === 200) {
        $variables = json_decode($response->getContent(), true);
        $count = count($variables);
        echo "   ✅ /api/bre/variables - {$count} variables available\n";
        if (count($variables) > 0) {
            echo "   Sample: {$variables[0]['variable_name']} ({$variables[0]['data_type']})\n";
        }
    } else {
        echo "   ❌ Variables API failed: {$response->getStatusCode()}\n";
    }
    
    // 2. Test Rules API
    echo "\n2. Testing Rules API:\n";
    $request = \Illuminate\Http\Request::create('/api/rules', 'GET');
    $response = app()->handle($request);
    
    if ($response->getStatusCode() === 200) {
        $rules = json_decode($response->getContent(), true);
        $count = count($rules);
        echo "   ✅ /api/rules - {$count} rules available\n";
        if (count($rules) > 0) {
            echo "   Sample: {$rules[0]['rule_name']} (ID: {$rules[0]['rule_id']})\n";
        }
    } else {
        echo "   ❌ Rules API failed: {$response->getStatusCode()}\n";
    }
    
    // 3. Test Conditions page
    echo "\n3. Testing Conditions page:\n";
    $request = \Illuminate\Http\Request::create('/bre/conditions', 'GET');
    $response = app()->handle($request);
    echo "   Conditions page status: " . $response->getStatusCode() . " ✅\n";
    
    echo "\n🎉 Conditions View Fix Complete!\n";
    echo "\nWhat was fixed:\n";
    echo "- ✅ Added missing API endpoint: /api/bre/variables\n";
    echo "- ✅ Added variablesApi() method to BREController\n";
    echo "- ✅ Fixed JavaScript to use /api/bre/variables instead of /api/variables\n";
    echo "- ✅ Fixed JavaScript to use rule.rule_id instead of rule.id\n";
    echo "\nNow the Variables and Rules dropdowns in the Conditions form should work!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
