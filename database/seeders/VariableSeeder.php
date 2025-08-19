<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Variable;

class VariableSeeder extends Seeder
{
    public function run(): void
    {
        $partner = \App\Models\Partner::first();
        if ($partner) {
            $partnerId = $partner->partner_id;
            Variable::create([
                'variable_id' => uniqid('var_'),
                'partner_id' => $partnerId,
                'variable_name' => 'salary',
                'data_type' => 'number',
                'description' => 'Monthly salary of the applicant',
                'source' => 'application',
            ]);

            Variable::create([
                'variable_id' => uniqid('var_'),
                'partner_id' => $partnerId,
                'variable_name' => 'age',
                'data_type' => 'number',
                'description' => 'Age of the applicant in years',
                'source' => 'application',
            ]);

            Variable::create([
                'variable_id' => uniqid('var_'),
                'partner_id' => $partnerId,
                'variable_name' => 'employment_type',
                'data_type' => 'string',
                'description' => 'Type of employment (Salaried, Self-Employed, Business)',
                'source' => 'application',
            ]);

            Variable::create([
                'variable_id' => uniqid('var_'),
                'partner_id' => $partnerId,
                'variable_name' => 'cibil_score',
                'data_type' => 'number',
                'description' => 'CIBIL/credit score of the applicant',
                'source' => 'application',
            ]);

            Variable::create([
                'variable_id' => uniqid('var_'),
                'partner_id' => $partnerId,
                'variable_name' => 'loan_amount',
                'data_type' => 'number',
                'description' => 'Requested loan amount',
                'source' => 'application',
            ]);
        }
    }
}
