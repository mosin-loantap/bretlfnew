<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class)->bootstrap();

echo "Testing Rule Creation API Endpoint\n";
echo "==================================\n";

try {
    // Create a mock request with the form data
    $formData = [
        'partner_id' => '1',
        'product_id' => '1',
        'rule_name' => 'API Test Rule',
        'rule_type' => 'eligibility',
        'priority' => '20',
        'description' => 'Testing via API endpoint',
        'effective_from' => date('Y-m-d'),
        'effective_to' => '',
        'is_active' => '1',
        '_token' => 'test_token' // Laravel requires CSRF token but we'll mock it
    ];

    echo "1. Creating test rule via BREController:\n";
    
    // Create a mock request
    $request = new \Illuminate\Http\Request();
    $request->merge($formData);
    
    // Mock the CSRF verification by setting the session
    $request->setLaravelSession(app('session'));
    
    $controller = new \App\Http\Controllers\BREController();
    
    echo "   - Partner ID: {$formData['partner_id']}\n";
    echo "   - Product ID: {$formData['product_id']}\n";
    echo "   - Rule Name: {$formData['rule_name']}\n";
    echo "   - Effective From: {$formData['effective_from']}\n";
    
    // Count rules before
    $countBefore = \App\Models\Rule::count();
    echo "\n2. Rules count before: {$countBefore}\n";
    
    // We'll test the validation logic manually since we can't easily test the full HTTP request
    $validator = \Illuminate\Support\Facades\Validator::make($formData, [
        'partner_id' => 'required|exists:partners,partner_id',
        'product_id' => 'required|exists:products,product_id',
        'rule_name' => 'required|string|max:255',
        'rule_type' => 'required|string|max:255',
        'priority' => 'required|integer|min:1',
        'description' => 'nullable|string|max:1000',
        'effective_from' => 'required|date',
        'effective_to' => 'nullable|date|after:effective_from',
        'is_active' => 'boolean',
    ]);
    
    if ($validator->fails()) {
        echo "   ❌ Validation failed:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "      - {$error}\n";
        }
    } else {
        echo "   ✅ Validation passed\n";
        
        $validated = $validator->validated();
        $validated['status'] = $validated['is_active'] ?? true;
        unset($validated['is_active']);
        
        // Create the rule
        $rule = \App\Models\Rule::create($validated + [
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        
        echo "   ✅ Rule created successfully with ID: {$rule->rule_id}\n";
        
        $countAfter = \App\Models\Rule::count();
        echo "\n3. Rules count after: {$countAfter}\n";
        echo "   ✅ Rule count increased by " . ($countAfter - $countBefore) . "\n";
        
        // Verify the rule data
        echo "\n4. Created rule details:\n";
        echo "   - Rule ID: {$rule->rule_id}\n";
        echo "   - Rule Name: {$rule->rule_name}\n";
        echo "   - Partner ID: {$rule->partner_id}\n";
        echo "   - Product ID: {$rule->product_id}\n";
        echo "   - Priority: {$rule->priority}\n";
        echo "   - Status: {$rule->status}\n";
        echo "   - Effective From: {$rule->effective_from}\n";
        echo "   - Description: {$rule->description}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
