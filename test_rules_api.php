<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class)->bootstrap();

echo "Testing Rules API for Conditions\n";
echo "================================\n";

try {
    // Test the API endpoint
    echo "Testing /api/rules endpoint...\n";
    $request = \Illuminate\Http\Request::create('/api/rules', 'GET');
    $response = app()->handle($request);
    
    echo "API Status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() === 200) {
        $apiRules = json_decode($response->getContent(), true);
        echo "API Rules count: " . count($apiRules) . "\n";
        if (count($apiRules) > 0) {
            $firstRule = $apiRules[0];
            echo "Sample rule fields:\n";
            echo "  - rule_id: " . ($firstRule['rule_id'] ?? 'Missing') . "\n";
            echo "  - rule_name: " . ($firstRule['rule_name'] ?? 'Missing') . "\n";
            echo "  - All fields: " . implode(', ', array_keys($firstRule)) . "\n";
        }
    } else {
        echo "API Error: " . $response->getContent() . "\n";
    }
    
    echo "\n✅ Rules API test completed!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
