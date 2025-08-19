<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Partner;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        Partner::create([
            'nbfc_name' => 'Loantap',
            'registration_number' => 'ABC123',
            'rbi_license_type' => 'NBFC',
            'date_of_incorporation' => '2015-08-14',
            'business_limit' => 1000000000,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Partner::create([
            'nbfc_name' => 'Bajaj Finserv',
            'registration_number' => 'XYZ456',
            'rbi_license_type' => 'NBFC',
            'date_of_incorporation' => '2008-05-20',
            'business_limit' => 5000000000,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
