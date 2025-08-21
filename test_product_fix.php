<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class)->bootstrap();

echo "Testing Product Creation Fix\n";
echo "============================\n";

try {
    // Test creating a product with all required fields
    echo "Creating a test product...\n";
    
    $product = \App\Models\Product::create([
        'partner_id' => 1,
        'product_name' => 'Test Personal Loan',
        'product_type' => 'Personal Loan',
        'product_category' => 'unsecured',
        'min_amount' => 50000,
        'max_amount' => 500000,
        'min_tenure' => 12,
        'max_tenure' => 60,
        'interest_rate' => 12.5,
        'created_by' => 1,
        'updated_by' => 1,
    ]);
    
    echo "✅ Product created successfully!\n";
    echo "Product ID: {$product->product_id}\n";
    echo "Product Name: {$product->product_name}\n";
    echo "Product Category: {$product->product_category}\n";
    
    // Clean up test data
    $product->delete();
    echo "✅ Test product cleaned up\n";
    
    echo "\n🎉 Product creation issue fixed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
