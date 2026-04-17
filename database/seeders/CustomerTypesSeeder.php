<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerType;

class CustomerTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CustomerType::firstOrCreate(['name' => 'retailer']);
        CustomerType::firstOrCreate(['name' => 'admin']);
    }
}
