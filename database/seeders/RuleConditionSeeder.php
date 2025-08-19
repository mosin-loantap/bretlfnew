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
    $salaryVar = Variable::where('variable_name', 'salary')->first();
    $ageVar = Variable::where('variable_name', 'age')->first();

        if ($rule && $salaryVar) {
            RuleCondition::create([
                'rule_id'     => $rule->rule_id,
                'variable_id' => $salaryVar->variable_id,
                'operator'    => '>=',
                'value'       => '30000',
                'created_by'  => 1,
                'updated_by'  => 1,
            ]);
        }

        if ($rule && $ageVar) {
            RuleCondition::create([
                'rule_id'     => $rule->rule_id,
                'variable_id' => $ageVar->variable_id,
                'operator'    => '>=',
                'value'       => '21',
                'created_by'  => 1,
                'updated_by'  => 1,
            ]);
        }
    }
}
