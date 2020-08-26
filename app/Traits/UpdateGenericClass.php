<?php

namespace App\Traits;

use App\Mail\SendgridMail;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

trait UpdateGenericClass{

    public static function updateData($uuid)
    {
        return self::where('uuid','like','%'.$uuid.'%')->firstOrFail()
            ->update(request()->all());
    }

    public static function updateDataId($id)
    {
        return self::where('id','like','%'.$id.'%')->firstOrFail()
            ->update(request()->all());
    }

    public static function dataUser($input)
    {
        try {

            $firstName = $input['nombre'];
            $lastName = $input['apellido_paterno'];

            $input['name'] = $input['nombre'];
            $input['second_name'] = $input['segundo_nombre'];
            $input['last_name'] = $input['apellido_paterno'];
            $input['grade'] = $input['grado'];

            if ($input['seccion'] ==  'Preescolar'){

                $input['role_id'] = 9;

                $password = Str::random(4);
                $input['password'] = $password;
            }else{
                if ($input['seccion'] ==  'Primaria'){

                    $input['role_id'] = 5;
                    $password = Str::random(6);
                    $input['password'] = $password;
                }
            }

            $input['second_last_name'] = $input['apellido_materno'];
            $input['email'] = $input['email'];
            $input['grade'] = $input['grado'];
            $input['second_last_name'] = $input['apellido_materno'];

            $email = $input['email'];
            $secondName = $input['second_name'];
            $username = Str::slug($firstName . $lastName);

            $reuser = self::where([
                ['username', '=', $username]
            ])->first(['id', 'username', 'second_name', 'email' ]);

            if ($reuser) {

                if ($reuser['email'] === $email && $reuser['second_name'] === $secondName) {
                    return ('El usuario ya existe');
                } else {
                    $i = 0;
                    while (self::whereUsername($username)->exists()) {
                        $i++;
                        $username = $firstName[0] . $lastName . $i;
                    }

                    $input['username'] = $username;
                }
            } else {

                $input['username'] = $username;
            }
            $user = self::create($input);

            $data = ([
                'username' => $user->username,
                'name' => $user->name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'grade' => $user->grade,
                'password' => $password
            ]);
            Mail::to('dylan.lievano.cuevas@gmail.com')->queue(new SendgridMail($data));

//            if( env('MAIL_CONFIG', 'dev') == 'prod') {
//                Mail::to($user->email)->queue(new SendgridMail($data));
//            }else{
//                Mail::to(env('MAIL_CONFIG', 'dylan.lievano.cuevas@gmail.com'))->queue(new SendgridMail($data));
//            }

            return ('Usuario creado');

        } catch (\mysql_xdevapi\Exception $e) {
            return ('Error al crear el usuario');
        }
    }

}
