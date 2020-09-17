<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'Admin',
            'id' => 1,
            'slug' => 'admin',
            'description' => 'Administrador del sistema',
            'this_order' => 1,
            'special' => 'all-access'
        ]);

        Role::create([
            'name' => 'Ventas',
            'id' => 2,
            'slug' => 'ventas',
            'description' => 'Ventas',
            'this_order' => 2,
        ]);

        Role::create([
            'id' => 3,
            'name' => 'Admin Escuela',
            'slug' => 'admin_escuela',
            'description' => 'Administador de la escuela',
            'this_order' => 3,
        ]);

        Role::create([
            'id' => 4,
            'name' => 'Maestro',
            'slug' => 'maestro',
            'description' => 'Maestro de grupo',
            'this_order' => 4,
        ]);

        Role::create([
            'id' => 5,
            'name' => 'Alumno',
            'slug' => 'alumno',
            'description' => 'Alumno',
            'this_order' => 5,
        ]);

        Role::create([
            'id' => 6,
            'name' => 'Padre',
            'slug' => 'padre',
            'description' => 'Padre del Alumno',
            'this_order' => 6,
        ]);


        Role::create([
            'id' => 13,
            'name' => 'Preescolar',
            'slug' => 'preescolar',
            'description' => 'Preescolar',
            'this_order' => 9,
        ]);

        App\User::create([
            'username' => 'admin',
            'name' => 'Administrador',
            'second_name' => '',
            'last_name' => 'System',
            'second_last_name' => '',
            'email' => 'lcruz@arkusnexus.com',
            'avatar' => '',
            'password' => "Admin123456",
            'verified_email' => true,
            'role_id' => 1
        ]);

        //factory(App\User::class, 15)->create();

    }
}
