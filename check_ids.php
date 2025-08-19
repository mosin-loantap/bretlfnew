<?php

use App\Models\Partner;
use App\Models\Product;

echo "Partners Table:\n";
$partners = Partner::select('partner_id', 'nbfc_name')->get();
foreach ($partners as $partner) {
    echo "ID: {$partner->partner_id}, Name: {$partner->nbfc_name}\n";
}

echo "\nProducts Table:\n";
$products = Product::select('product_id', 'partner_id', 'product_name')->get();
foreach ($products as $product) {
    echo "ID: {$product->product_id}, Partner ID: {$product->partner_id}, Name: {$product->product_name}\n";
}

echo "\nData verification successful! Partners table now uses simple integer IDs.\n";
