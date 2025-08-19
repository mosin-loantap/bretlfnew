<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Partner;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $partner = Partner::first();
        if ($partner) {
            Product::create([
                'partner_id' => $partner->partner_id,
                'product_name' => 'Personal Loan',
                'product_type' => 'Loan',
                'product_category' => 'unsecured',
                'min_amount' => 50000,
                'max_amount' => 500000,
                'min_tenure' => 12,
                'max_tenure' => 60,
                'interest_rate' => 14.5,
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            Product::create([
                'partner_id' => $partner->partner_id,
                'product_name' => 'Business Loan',
                'product_type' => 'Loan',
                'product_category' => 'unsecured',
                'min_amount' => 100000,
                'max_amount' => 2000000,
                'min_tenure' => 12,
                'max_tenure' => 84,
                'interest_rate' => 16.0,
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
