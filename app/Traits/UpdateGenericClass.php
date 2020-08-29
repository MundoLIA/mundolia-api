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

    public static function getGrade($grade){
        $grades["Preescolar - Primer Grado"] = 1;
        $grades["Preescolar - Segundo Grado"] = 2;
        $grades["Preescolar - Tercer Grado"] = 3;
        $grades["Primaria - Primer Grado"] = 1;
        $grades["Primaria - Segundo Grado"] = 2;
        $grades["Primaria - Tercer Grado"] = 2;
        $grades["Primaria - Cuarto Grado"] = 3;
        $grades["Primaria - Quinto Grado"] = 4;
        $grades["Primaria - Sexto Grado"] = 5;
        return $grades[$grade];
    }
    public static function getRole($role, $seccion){
        if($role == "Maestro"){
            $rol = 4;
        }
        if($role == "Administrador Escuela LIA"){
            $rol = 3;
        }
        if($seccion == "Preescolar" && $role == "Alumno"){
            $rol= 9;
        }
        if($seccion == "Primaria" && $role == "Alumno"){
            $rol = 5;
        }
        return $rol;

    }
    public static function createPassword( $seccion){
        if ($seccion == 'Preescolar'){
            $password = Str::random(4);

        }else{
            if ($seccion == 'Primaria'){
                $password = Str::random(6);

            }
        }
        return $password;
    }
    public static function dataUser($input)
    {
        try {

            $dataCreate['name'] = $input['nombre'];
            $dataCreate['second_name'] = $input['segundo_nombre'];
            $dataCreate['last_name'] = $input['apellido_paterno'];
            $dataCreate['grade'] = self::getGrade($input['grado']);
            $dataCreate['role_id'] = self::getRole($input['tipo_usuario'],$input['seccion']);
            $dataCreate['second_last_name'] = $input['apellido_materno'];
            $dataCreate['second_last_name'] = $input['apellido_materno'];
            $dataCreate['email'] = $input['email'];
            $dataCreate['school_id'] = $input['school_id'];

            $password = $dataCreate['password'] = self::createPassword($input['seccion']);

            $firstName = $input['nombre'];
            $lastName = $input['apellido_paterno'];
            $email = $input['email'];
            $secondName = $input['segundo_nombre'];
            $username = Str::slug($firstName . $lastName);

            $reuser = self::where([
                ['username', '=', $username]
            ])->first(['id', 'username', 'second_name', 'email' ]);

            if ($reuser) {
                if ($reuser['email'] === $email && $reuser['second_name'] === $secondName) {
                    return (["message" => "El usuario ya existe", "username" => $username]);
                } else {
                    $i = 0;
                    while (self::whereUsername($username)->exists()) {
                        $i++;
                        $username = Str::slug($firstName[0] . $lastName . $i);
                    }
                    $dataCreate['username'] = $username;
                }
            } else {
                $dataCreate['username'] = $username;
            }
            $user = self::create($dataCreate);

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
            return (["message" => "Usuario creado", "username" => $username]);


        } catch (\mysql_xdevapi\Exception $e) {
            return (["message" => "Error al crear el usuario", "username" => ""]);
        }
    }

}
