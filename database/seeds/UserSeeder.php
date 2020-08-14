<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('users')->insert([
            'id' => '00000000000000000001',
            'name' => 'Luis',
            'second_name' => 'Antonio',
            'last_name' => 'Cruz',
            'second_last_name' => 'Macias',
            'email' => 'antonio2120@gmail.com',
            'id_school' => '',
            'grade' => 1,
            'avatar' => '',
            'password' => "123456",
            'active' => true,
            'verified_email' => true,
            'remember_token' => '',


       ]);
    }
}
