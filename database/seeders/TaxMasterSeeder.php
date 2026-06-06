<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TaxMaster;

class TaxMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxes = [
            ['tax_name' => 'GST 5%', 'tax_percentage' => 5.00],
            ['tax_name' => 'GST 12%', 'tax_percentage' => 12.00],
            ['tax_name' => 'GST 18%', 'tax_percentage' => 18.00],
        ];

        foreach ($taxes as $tax) {
            TaxMaster::updateOrCreate(
                ['tax_percentage' => $tax['tax_percentage']],
                ['tax_name' => $tax['tax_name']]
            );
        }
    }
}
