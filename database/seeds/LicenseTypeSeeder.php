<?php

use App\LicenseType;
use Illuminate\Database\Seeder;

class LicenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LicenseType::create([
            'description_license_type' => 'General',
        ]);
    }
}
