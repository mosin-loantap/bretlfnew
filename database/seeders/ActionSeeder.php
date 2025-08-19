<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Action;
use App\Models\Rule;

class ActionSeeder extends Seeder
{
    public function run(): void
    {
        $rule = Rule::first();
        if ($rule) {
            Action::create([
                'rule_id' => $rule->rule_id,
                'partner_id' => $rule->partner_id,
                'action_type' => 'Approve',
                'parameters' => json_encode(['message' => 'Eligible for loan']),
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            Action::create([
                'rule_id' => $rule->rule_id,
                'partner_id' => $rule->partner_id,
                'action_type' => 'Reject',
                'parameters' => json_encode(['message' => 'Not eligible due to low salary']),
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
