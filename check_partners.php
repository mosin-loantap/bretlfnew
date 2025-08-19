<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/database/database.sqlite',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$partners = $capsule->table('partners')->select('partner_id', 'nbfc_name')->get();

echo "Partners Table:\n";
foreach ($partners as $partner) {
    echo "ID: {$partner->partner_id}, Name: {$partner->nbfc_name}\n";
}

$products = $capsule->table('products')->select('product_id', 'partner_id', 'product_name')->get();

echo "\nProducts Table:\n";
foreach ($products as $product) {
    echo "ID: {$product->product_id}, Partner ID: {$product->partner_id}, Name: {$product->product_name}\n";
}
