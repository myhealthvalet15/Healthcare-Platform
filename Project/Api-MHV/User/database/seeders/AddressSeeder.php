<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Example data
        $addresses = [
            [
                'address_name' => '123 Main St',
                'address_type' => 'Residential',
                'area_id' => 1,
                'city_id' => 1,
                'state_id' => 1,
                'country_id' => 1,
                'active_status' => 1,
            ],
            [
                'address_name' => '456 Elm St',
                'address_type' => 'Commercial',
                'area_id' => 2,
                'city_id' => 1,
                'state_id' => 1,
                'country_id' => 1,
                'active_status' => 1,
            ],
        ];

        DB::table('addresses')->insert($addresses);
    }
}
