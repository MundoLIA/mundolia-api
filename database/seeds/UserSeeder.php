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
        factory(App\User::class, 15)->create();

        Role::create([
            'name' => 'Administrador',
            'slug' => 'admin',
            'description' => 'Administrador del sistema',
            'this_order' => 1,
            'special' => 'all-access'
        ]);

        Role::create([
            'name' => 'Ventas',
            'slug' => 'ventas',
            'description' => 'Ventas',
            'this_order' => 2,
        ]);

        Role::create([
            'name' => 'AdminEscuela',
            'slug' => 'admin_escuela',
            'description' => 'Administador de la escuela',
            'this_order' => 3,
        ]);

        Role::create([
            'name' => 'Maestro',
            'slug' => 'maestro',
            'description' => 'Maestro de grupo',
            'this_order' => 4,
        ]);

        Role::create([
            'name' => 'Alumno',
            'slug' => 'alumno',
            'description' => 'Alumno',
            'this_order' => 5,
        ]);

        Role::create([
            'name' => 'Padre',
            'slug' => 'padre',
            'description' => 'Padre del Alumno',
            'this_order' => 6,
        ]);

        Role::create([
            'name' => 'Practicante',
            'slug' => 'practicante',
            'description' => 'Practicante',
            'this_order' => 7,
        ]);

        Role::create([
            'name' => 'Gratuito',
            'slug' => 'gratuito',
            'description' => 'Gratuito',
            'this_order' => 8,
        ]);

        Role::create([
            'name' => 'Preescolar',
            'slug' => 'preescolar',
            'description' => 'Preescolar',
            'this_order' => 9,
        ]);

        Role::create([
            'name' => 'Gratuito Preescolar',
            'slug' => 'gratuito_preescolar',
            'description' => 'Gratuito',
            'this_order' => 10,
        ]);

        Role::create([
            'name' => 'Gratuito Primaria',
            'slug' => 'gratuito_primaria',
            'description' => 'Gratuito Primaria',
            'this_order' => 11,
        ]);

        Role::create([
            'name' => 'Gratuito Maestro',
            'slug' => 'gratuito_maestro',
            'description' => 'Gratuito Maestro',
            'this_order' => 12,
        ]);






    }
}
