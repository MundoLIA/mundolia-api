<?php

use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('schools')->insert([
                    'id' => 'S00000000000000000001',
                    'name' => 'LIA Collage',
               ]);
    }
}
