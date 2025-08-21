<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class)->bootstrap();

echo "Testing Rule Creation Fix\n";
echo "========================\n";

try {
    // Simulate the corrected form data being sent
    $formData = [
        'partner_id' => '1',
        'product_id' => '1',
        'rule_name' => 'Test Rule Fix',
        'rule_type' => 'eligibility',
        'priority' => '15',
        'description' => 'Test description for rule fix',
        'effective_from' => date('Y-m-d'),
        'effective_to' => '',
        'is_active' => '1'
    ];

    echo "1. Corrected Form Data:\n";
    foreach ($formData as $key => $value) {
        echo "   - {$key}: " . ($value ?: 'null') . "\n";
    }

    // Test validation manually
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

    echo "\n2. Validation Result:\n";
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
        
        echo "\n3. Data ready for database:\n";
        foreach ($validated as $key => $value) {
            echo "   - {$key}: " . ($value ?: 'null') . "\n";
        }
    }

    // Check if Rule model can accept the data
    echo "\n4. Rule Model Check:\n";
    $rule = new \App\Models\Rule();
    $fillable = $rule->getFillable();
    $missingFields = [];
    $extraFields = [];
    
    foreach (array_keys($validated ?? $formData) as $field) {
        if ($field === 'is_active') continue; // We convert this to status
        if (!in_array($field, $fillable) && !in_array($field, ['created_by', 'updated_by'])) {
            $extraFields[] = $field;
        }
    }
    
    if (empty($extraFields)) {
        echo "   ✅ All fields are fillable in Rule model\n";
    } else {
        echo "   ❌ Extra fields not in fillable: " . implode(', ', $extraFields) . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
