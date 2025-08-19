<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;
use App\Models\Partner;
use App\Models\Product;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $partner = Partner::first();
        $product = Product::first();

        if ($partner && $product) {
            Application::create([
                'partner_id' => $partner->partner_id,
                'product_id' => $product->product_id,
                'customer_name' => 'Ravi Kumar',
                'requested_amount' => 200000,
                'requested_tenure' => 36,
                'status' => 'pending',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            Application::create([
                'partner_id' => $partner->partner_id,
                'product_id' => $product->product_id,
                'customer_name' => 'Priya Sharma',
                'requested_amount' => 400000,
                'requested_tenure' => 48,
                'status' => 'pending',
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
