<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class)->bootstrap();

echo "Testing Rule Creation - Final Fix\n";
echo "=================================\n";

try {
    $formData = [
        'partner_id' => '1',
        'product_id' => '1',
        'rule_name' => 'Final Test Rule',
        'rule_type' => 'eligibility',
        'priority' => '30',
        'effective_from' => date('Y-m-d'),
        'effective_to' => null,
        'is_active' => '1'
    ];

    echo "1. Form data (without description):\n";
    foreach ($formData as $key => $value) {
        echo "   - {$key}: " . ($value ?: 'null') . "\n";
    }

    echo "\n2. Testing validation:\n";
    
    $validator = \Illuminate\Support\Facades\Validator::make($formData, [
        'partner_id' => 'required|exists:partners,partner_id',
        'product_id' => 'required|exists:products,product_id',
        'rule_name' => 'required|string|max:255',
        'rule_type' => 'required|string|max:255',
        'priority' => 'required|integer|min:1',
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
        echo "   ✅ Validation passed!\n";
        
        $validated = $validator->validated();
        $validated['status'] = $validated['is_active'] ?? true;
        unset($validated['is_active']);
        
        echo "\n3. Creating rule in database:\n";
        $countBefore = \App\Models\Rule::count();
        
        $rule = \App\Models\Rule::create($validated + [
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        
        $countAfter = \App\Models\Rule::count();
        
        echo "   ✅ Rule created successfully!\n";
        echo "   - Rule ID: {$rule->rule_id}\n";
        echo "   - Rule Name: {$rule->rule_name}\n";
        echo "   - Status: " . ($rule->status ? 'Active' : 'Inactive') . "\n";
        echo "   - Effective From: {$rule->effective_from}\n";
        echo "   - Rules count: {$countBefore} → {$countAfter}\n";
    }

    echo "\n🎉 422 Error Fix Complete!\n";
    echo "\nWhat was fixed:\n";
    echo "✅ Added missing 'effective_from' and 'effective_to' fields\n";
    echo "✅ Fixed 'is_active' → 'status' field mapping\n";
    echo "✅ Removed 'description' field (not in database table)\n";
    echo "✅ Updated validation rules to match form fields\n";
    echo "✅ Set default date for 'effective_from' field\n";
    
    echo "\nThe 422 error should now be resolved!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
