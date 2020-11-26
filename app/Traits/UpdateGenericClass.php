<?php

namespace App\Traits;

use App\Jobs\SendEmail;
use App\Jobs\UserGenericRegister;
use App\Mail\SendgridMail;
use App\User;
use App\UserLIA;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
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

    public static function getGrade($grade, $role, $seccion){
        $grades["PREESCOLAR - PRIMER GRADO"] = 1;
        $grades["PREESCOLAR - SEGUNDO GRADO"] = 2;
        $grades["PREESCOLAR - TERCER GRADO"] = 3;
        $grades["PRIMARIA - PRIMER GRADO"] = 1;
        $grades["PRIMARIA - SEGUNDO GRADO"] = 2;
        $grades["PRIMARIA - TERCER GRADO"] = 3;
        $grades["PRIMARIA - CUARTO GRADO"] = 4;
        $grades["PRIMARIA - QUINTO GRADO"] = 5;
        $grades["PRIMARIA - SEXTO GRADO"] = 6;

        if (array_key_exists($grade, $grades)) {
            $role_result = $grades[$grade];
        }else{
            return 0;
        }
        if($seccion == "PREESCOLAR" && $role == "ALUMNO" && $role_result > 3){
            return 0;
        }
        return $role_result;
    }
    public static function getRole($role, $seccion){
        if($role == "MAESTRO"){
            $rol = 4;
        }
        if($role == "ADMINISTRADOR ESCUELA LIA"){
            $rol = 3;
        }
        if($seccion == "PREESCOLAR" && $role == "ALUMNO"){
            $rol= 13;
        }
        if($seccion == "PRIMARIA" && $role == "ALUMNO"){
            $rol = 5;
        }
        return $rol;

    }
    public static function createPassword( $seccion){
        if ($seccion == 'PREESCOLAR'){
            $password = Str::random(4);

        }else{
            if ($seccion == 'PRIMARIA'){
                $password = Str::random(6);

            }
        }
        return $password;
    }
    public static function dataUser($input, $school_id, $passwordSource = null)
    {
        try {
            $user = Auth::user();

            $role_id= self::getRole($input['tipo_usuario'],$input['seccion']);
            if($user->role_id == 1 || $user->role_id == 2){
                $dataCreate['school_id'] = $school_id;
                $dataCreate['role_id'] = $role_id;
            }else{
                if ( $role_id == 4 || $role_id == 5 ||  $role_id == 13 ){
                    $dataCreate['role_id'] = $role_id;
                }else{
                    $dataCreate['role_id'] = 4;
                }
                $dataCreate['school_id'] = $user->school_id;
            }
            $dataCreate['name'] = $input['nombre'].' '.$input['segundo_nombre'];
            $dataCreate['last_name'] = $input['apellido_paterno'].' '.$input['apellido_materno'];
            $dataCreate['grade'] = self::getGrade($input['grado'], $input['tipo_usuario'],$input['seccion']);
            $dataCreate['email'] = $input['email'];

            $password  = $passwordSource ? $passwordSource : self::createPassword($input['seccion']);
            $passwordEncode = bcrypt($password);
            $passwordEncode = str_replace("$2y$", "$2a$", $passwordEncode);
            $dataCreate['password'] = $passwordEncode;

            $firstName = $input['nombre'];
            $lastName = $input['apellido_paterno'];
            $email = $input['email'];
            $username = Str::slug($firstName . $lastName);

            $reuser = \DB::select('Select
                            users.id,
                            users.username,
                            users.name,
                            users.last_name,
                            users.email
                            FROM users
                            WHERE users.email = "'.$email.'" and username = "'.$username.'"
                            LIMIT 1');

            if ($reuser) {
                    return (["message" => "El usuario ya existe", "username" => $username]);
            } else {
                $i = 0;
                while (self::whereUsername($username)->exists()) {
                    $i++;
                    $username = Str::slug($firstName[0] . $lastName . $i);
                }
                $dataCreate['username'] = $username;
            }

            $now = new DateTime();

            $dataLIA = ([
                'AppUser' =>  $dataCreate['username'],
                'Names' =>  $dataCreate['name'],
                'LastNames' => $dataCreate['last_name'],
                'Email' =>  $dataCreate['email'],
                'Grade' =>  $dataCreate['grade'],
                'Password' => $dataCreate['password'],
                'RoleId' =>  $dataCreate['role_id'],
                'IsActive' => 1,
                'SchoolId' => $dataCreate['school_id'],
                'SchoolGroupKey'=> 140232,
                'MemberSince'=> $now,
                'CreatorId' => 68,
                'EditorId' => 68,
                'Avatar' => null,
            ]);
            if(Config::get('app.sync_lia')){
                $userLIA = UserLIA::create($dataLIA);
                $dataCreate['AppUserId'] = $userLIA->AppUserId;
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

            $dataThink = ([
                'first_name' => $user->username,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'password' => $password
            ]);

            $dataFox = ([
                'email' => $user->email,
                'full_name' => $user->name . $user->last_name,
                'password' => $password,
                'gender' => "1",
                "user_name" => $user->username
            ]);

            if(Config::get('app.sync_thinkific')) {
                UserGenericRegister::dispatch($dataThink, $dataFox);
            }

            if(Config::get('app.send_email')) {
                SendEmail::dispatchNow($data);
            }

            return (["message" => "Usuario creado", "username" => $username]);


        } catch (\mysql_xdevapi\Exception $e) {
            return (["message" => "Error al crear el usuario", "username" => ""]);
        }
    }

}
