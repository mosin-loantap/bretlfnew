<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rule;
use App\Models\Product;
use App\Models\Partner;

class RuleSeeder extends Seeder
{
    public function run(): void
    {
        $partner = Partner::first();
        $product = Product::first();
        if ($partner && $product) {
            Rule::create([
                'partner_id' => $partner->partner_id,
                'product_id' => $product->product_id,
                'rule_name' => 'Check Salary Eligibility',
                'rule_type' => 'Eligibility',
                'priority' => 1,
                'total_marks' => 100,
                'effective_from' => '2025-01-01',
                'effective_to' => null,
                'is_active' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
