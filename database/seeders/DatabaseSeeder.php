<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


       $this->call([
           PartnerSeeder::class,
           ProductSeeder::class,
           VariableSeeder::class,
           RuleSeeder::class,
           RuleConditionSeeder::class,
           ActionSeeder::class,
           ApplicationSeeder::class,
       ]);

        // Uncomment the following line to seed RuleConditionSeeder
        // $this->call(RuleConditionSeeder::class);
    }
}
