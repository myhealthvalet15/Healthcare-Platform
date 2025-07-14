<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\V1Models\DrugTemplate;
use Faker\Factory as Faker;

class DrugTemplateSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Create 50 dummy records
        for ($i = 1; $i <= 50; $i++) {
            DrugTemplate::create([
                // Ensure drug_template_id is unique
                'drug_template_id'   => $i,
                'drug_name'          => $faker->word,
                'drug_type'          => $faker->numberBetween(1, 5),
                'drug_manufacturer'  => $faker->company,
                'drug_ingredient'    => $faker->sentence,
                'corporate_id'       => $faker->numberBetween(1, 10),
                'location_id'        => $faker->numberBetween(1, 10),
                'ohc'                => $faker->lexify('OHC ???'),
                'master_pharmacy_id' => $faker->numberBetween(1, 10),
                'drug_strength'      => $faker->randomElement(['100mg', '200mg', '250mg']),
                'restock_alert_count'=> $faker->numberBetween(1, 20),
                'crd'                => $faker->date(), // or use a specific format if needed
                'schedule'           => $faker->randomElement(['Schedule H', 'Schedule X', 'OTC']),
                'id_no'              => $faker->numberBetween(1000, 9999),
                'hsn_code'           => $faker->bothify('HSN###'),
                'amount_per_strip'   => $faker->randomFloat(2, 1, 100),
                'unit_issue'         => $faker->randomElement(['Box', 'Strip', 'Tablet']),
                'tablet_in_strip'    => $faker->numberBetween(1, 50),
                'amount_per_tab'     => $faker->randomFloat(2, 0.1, 10),
                'discount'           => $faker->randomFloat(2, 0, 50),
                'sgst'               => $faker->randomFloat(2, 0, 10),
                'cgst'               => $faker->randomFloat(2, 0, 10),
                'igst'               => $faker->randomFloat(2, 0, 10),
                'bill_status'        => $faker->randomElement(['Paid', 'Pending']),
                'created_by'         => $faker->name,
                // The "created_on" field will be set automatically by the database's default CURRENT_TIMESTAMP.
            ]);
        }
    }
}
