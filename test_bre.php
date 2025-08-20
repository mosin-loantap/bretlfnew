<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Partner;
use App\Models\Product;
use App\Models\Rule;
use App\Models\Variable;
use App\Services\RuleEvaluationService;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== BRE System Test ===\n\n";

// Display seeded data
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

echo "\nRules with Conditions:\n";
foreach (Rule::with(['conditions', 'actions'])->get() as $rule) {
    echo "- Rule ID: {$rule->rule_id}, Name: {$rule->rule_name}, Product: {$rule->product_id}\n";
    foreach ($rule->conditions as $condition) {
        echo "  Condition: {$condition->variable_name} {$condition->operator} {$condition->value}\n";
    }
    foreach ($rule->actions as $action) {
        echo "  Action: {$action->action_type} - {$action->parameters}\n";
    }
    echo "\n";
}

// Test the rule evaluation service directly
echo "=== Testing Rule Evaluation Service ===\n";

$ruleEvaluationService = new RuleEvaluationService();

// Test Case 1: Should PASS (salary >= 30000 and age >= 21)
echo "\nTest Case 1: Qualifying applicant\n";
$testData1 = [
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

echo "Input: " . json_encode($testData1, JSON_PRETTY_PRINT) . "\n";
$result1 = $ruleEvaluationService->evaluateApplication($testData1);
echo "Result: " . json_encode($result1, JSON_PRETTY_PRINT) . "\n";

// Test Case 2: Should FAIL (salary < 30000)
echo "\nTest Case 2: Low salary applicant\n";
$testData2 = [
    'partner_id' => 1,
    'product_id' => 1,
    'applicant' => [
        'name' => 'Jane Smith',
        'salary' => 25000,
        'age' => 30,
        'requested_amount' => 200000,
        'requested_tenure' => 36
    ]
];

echo "Input: " . json_encode($testData2, JSON_PRETTY_PRINT) . "\n";
$result2 = $ruleEvaluationService->evaluateApplication($testData2);
echo "Result: " . json_encode($result2, JSON_PRETTY_PRINT) . "\n";

// Test Case 3: Should FAIL (age < 21)
echo "\nTest Case 3: Underage applicant\n";
$testData3 = [
    'partner_id' => 1,
    'product_id' => 1,
    'applicant' => [
        'name' => 'Young Person',
        'salary' => 40000,
        'age' => 19,
        'requested_amount' => 150000,
        'requested_tenure' => 12
    ]
];

echo "Input: " . json_encode($testData3, JSON_PRETTY_PRINT) . "\n";
$result3 = $ruleEvaluationService->evaluateApplication($testData3);
echo "Result: " . json_encode($result3, JSON_PRETTY_PRINT) . "\n";

echo "\n=== Test Complete ===\n";
