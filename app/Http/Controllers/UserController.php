<?php

namespace App\Http\Controllers;

use App\Mail\SendgridMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Input\Input;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if($user->role_id == 1 || $user->role_id == 2){
            $users = User::where('role_id','<>', 1)
                ->select(
                    'id',
                    'uuid',
                    'username',
                    'name',
                    'second_name',
                    'last_name',
                    'second_last_name',
                    'school_id',
                    'email',
                    'grade',
                    'avatar',
                    'is_active',
                    'verified_email'

                )->get()->toJson(JSON_PRETTY_PRINT);
            return response($users, 200);
        }
        if($user->role_id == 3){
            $users = User::select(
                    'id',
                    'uuid',
                    'username',
                    'name',
                    'second_name',
                    'last_name',
                    'second_last_name',
                    'school_id',
                    'email',
                    'grade',
                    'avatar',
                    'is_active',
                    'verified_email'
                )->where([
                    ['role_id','<>', 1],
                    ['school_id','=', $user-school_id]
                ])->get()->toJson(JSON_PRETTY_PRINT);
            return response($users, 200);
        }

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $input = $request->all();
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

            $reuser = User::where([
                ['username', '=', $username]
            ])->first(['id', 'username', 'second_name', 'email' ]);

            if ($reuser) {
                if ($reuser['email'] === $email && $reuser['second_name'] === $secondName){
                    return ('El usuario ya existe');
                }
                else{
                        $i = 0;
                        while (User::whereUsername($username)->exists()) {
                            $i++;
                            $username = $firstName[0] . $lastName . $i;
                        }

                        $input['username'] = $username;
                    }
            }
            else{

                $input['username'] = $username;
            }
            $user = User::create($input);

            $data = ([
                'username' => $user->username,
                'name' => $user->name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'grade' => $user->grade,
                'password' => $password
            ]);

                Mail::to($user->email)->queue(new SendgridMail($data));

            return ('Se ha creado el usuario');

        }catch (Exception $e){
            return ('Error al crear el usuario');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @param  uuid $uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $user = User::where('uuid','like','%'.$uuid.'%')->get();
        return $user->toJson(JSON_PRETTY_PRINT);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @param  uuid $uuid
     * @return \Illuminate\Http\Response
     */
    public function update($uuid)
    {
        User::updateData($uuid);

        return response()->json([
            "message" => "El usuario ha sido actualizado",
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @param  uuid  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $user = User::where('uuid','like','%'.$uuid.'%')->firstOrFail();
        $user->delete();

        return response()->json([
            $user,
            "message" => "El usuario ha sido eliminado existosamente",
        ], 200);


    }

}
