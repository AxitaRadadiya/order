<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerType;
use App\Models\Customer;

class CustomerTypeBackfillSeeder extends Seeder
{
    public function run(): void
    {
        $retailer = CustomerType::firstOrCreate(['name' => 'retailer']);

        Customer::whereNull('customer_type_id')->update(['customer_type_id' => $retailer->id]);
    }
}
