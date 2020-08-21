<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\License;

class LicenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        License::create([
            'titular' => 'Colegio Lia',
            'email_admin' => 'test@email.com',
            'school_id' => '029cb405-9f65-461a-9019-ad4fd4d2c27a',
            'license_type_id' =>'1',
            'user_id' => 'c1a22696-0769-4bce-bdf9-7ab796cda251',
            'studens_limit' => '500',
            'purchase_at' => Carbon::now(),
            'expiration_date' =>  Carbon::now()->add(1, 'year'),
        ]);
    }
}
