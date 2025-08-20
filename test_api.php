<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Partner;
use App\Models\Product;
use App\Models\Rule;
use App\Models\Variable;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== BRE Demo Data Check ===\n\n";

echo "Partners:\n";
foreach (Partner::all() as $partner) {
    echo "- ID: {$partner->partner_id}, Name: {$partner->nbfc_name}\n";
}

echo "\nProducts:\n";
foreach (Product::all() as $product) {
    echo "- ID: {$product->product_id}, Name: {$product->product_name}\n";
}

echo "\nVariables:\n";
foreach (Variable::all() as $variable) {
    echo "- ID: {$variable->variable_id}, Name: {$variable->variable_name}, Type: {$variable->data_type}\n";
}

echo "\nRules:\n";
foreach (Rule::with(['conditions', 'actions'])->get() as $rule) {
    echo "- ID: {$rule->rule_id}, Name: {$rule->rule_name}, Product: {$rule->product_id}\n";
    echo "  Conditions: " . $rule->conditions->count() . "\n";
    echo "  Actions: " . $rule->actions->count() . "\n";
}

echo "\n=== API Test ===\n";

// Test data for API
$testData = [
    'partner_id' => 1,
    'product_id' => 1,
    'applicant' => [
        'name' => 'John Doe',
        'salary' => 35000,
        'age' => 28,
        'requested_amount' => 300000,
        'requested_tenure' => 24
    ]
];

echo "Test Request Data:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n";

// Make API call
$url = 'http://127.0.0.1:8000/api/evaluate';
$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($testData)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo "\nAPI Response:\n";
if ($result !== false) {
    $response = json_decode($result, true);
    echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Error: Could not connect to API\n";
}
