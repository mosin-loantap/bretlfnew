<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RuleCondition;
use App\Models\Variable;
use App\Models\Rule;

class RuleConditionSeeder extends Seeder
{
    public function run(): void
    {
        $rule = Rule::first(); // pick first rule for demo

        if ($rule) {
            // Salary condition
            RuleCondition::create([
                'rule_id'     => $rule->rule_id,
                'variable_name' => 'salary',
                'operator'    => '>=',
                'value'       => '30000',
                'created_by'  => 1,
                'updated_by'  => 1,
            ]);

            // Age condition
            RuleCondition::create([
                'rule_id'     => $rule->rule_id,
                'variable_name' => 'age',
                'operator'    => '>=',
                'value'       => '21',
                'created_by'  => 1,
                'updated_by'  => 1,
            ]);
        }
    }
}
