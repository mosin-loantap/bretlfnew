<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class)->bootstrap();

echo "Testing Rule Creation Issues\n";
echo "============================\n";

try {
    // Simulate the form data being sent
    $formData = [
        'partner_id' => '1',
        'product_id' => '1',
        'rule_name' => 'Test Rule',
        'rule_type' => 'eligibility',
        'priority' => '10',
        'description' => 'Test description',
        'is_active' => '1'
        // Notice: missing effective_from, effective_to, status
    ];

    echo "1. Form Data Being Sent:\n";
    foreach ($formData as $key => $value) {
        echo "   - {$key}: {$value}\n";
    }

    echo "\n2. Controller Validation Rules:\n";
    echo "   - partner_id: required|exists:partners,partner_id\n";
    echo "   - product_id: required|exists:products,product_id\n";
    echo "   - rule_name: required|string|max:255\n";
    echo "   - rule_type: required|string|max:255\n";
    echo "   - priority: required|integer|min:1\n";
    echo "   - effective_from: required|date\n";
    echo "   - effective_to: nullable|date|after:effective_from\n";
    echo "   - status: boolean\n";

    echo "\n3. Issues Found:\n";
    echo "   ❌ Missing 'effective_from' field (required)\n";
    echo "   ❌ Missing 'effective_to' field (optional)\n";
    echo "   ❌ Form sends 'is_active' but controller expects 'status'\n";
    echo "   ❌ Form includes 'description' but controller doesn't validate it\n";

    // Check if we can find the partners and products
    $partner = \App\Models\Partner::find(1);
    $product = \App\Models\Product::find(1);
    
    echo "\n4. Data Validation:\n";
    echo "   - Partner 1 exists: " . ($partner ? "✅" : "❌") . "\n";
    echo "   - Product 1 exists: " . ($product ? "✅" : "❌") . "\n";
    
    if ($partner) {
        echo "   - Partner name: {$partner->nbfc_name}\n";
    }
    if ($product) {
        echo "   - Product name: {$product->product_name}\n";
    }

    echo "\n5. Rule Model Fillable Fields:\n";
    $rule = new \App\Models\Rule();
    $fillable = $rule->getFillable();
    foreach ($fillable as $field) {
        echo "   - {$field}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
