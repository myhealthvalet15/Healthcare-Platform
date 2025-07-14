<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterCorporate;
use App\Models\CorporateAddress;

class MasterCorporateSeeder extends Seeder
{
    public function run()
    {
        // Sample data for MasterCorporate
        $corporateData = [
            [
                'corporate_id' => 'C12345',
                'location_id' => 1,
                'corporate_num' => 'C12345',
                'corporate_name' => 'Sample Corporate 1',
                'displayname' => 'Sample Corp 1',
                'registrationno' => 'REG12345',
                'specializations' => 'Consulting',
                'prof_image' => 'http://example.com/image1.jpg',
                'description' => 'Description of Sample Corporate 1.',
                'specialities' => 'Specialties 1',
                'created_by' => 'admin',
                'created_role' => 'admin',
                'age' => 10,
                'color' => 'blue',
                'isactive' => true,
                'created_on_date' => now(),
            ],
            [
                'corporate_id' => 'C12346',
                'location_id' => 2,
                'corporate_num' => 'C12346',
                'corporate_name' => 'Sample Corporate 2',
                'displayname' => 'Sample Corp 2',
                'registrationno' => 'REG12346',
                'specializations' => 'Consulting',
                'prof_image' => 'http://example.com/image2.jpg',
                'description' => 'Description of Sample Corporate 2.',
                'specialities' => 'Specialties 2',
                'created_by' => 'admin',
                'created_role' => 'admin',
                'age' => 5,
                'color' => 'green',
                'isactive' => true,
                'created_on_date' => now(),
            ],
        ];

        foreach ($corporateData as $data) {
            $corporate = MasterCorporate::create($data);

           
        }
    }
}
