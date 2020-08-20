<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\License;
use Illuminate\Support\Str;

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
            'school_id' => '8LXJlwzctCqk5HhBb96I1yNYV9QatrYHodgT',
            'license_type_id' =>'1',
            'user_id' => '30925d13-c2f1-4ea9-a71b-4e183270bb2d',
            'studens_limit' => '500',
            'purchase_at' => Carbon::now(),
            'expiration_date' =>  Carbon::now()->add(1, 'year'),
        ]);
    }
}
