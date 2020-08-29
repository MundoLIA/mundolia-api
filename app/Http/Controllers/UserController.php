<?php

namespace App\Http\Controllers;

use App\Mail\SendgridMail;
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

        )->get()->toJson(JSON_PRETTY_PRINT);
        return response($users, 200);
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $dataCreate['name'] = $input['nombre'];
            $dataCreate['second_name'] = $input['segundo_nombre'];
            $dataCreate['last_name'] = $input['apellido_paterno'];
            $dataCreate['grade'] = User::getGrade($input['grado']);
            $dataCreate['role_id'] = User::getRole($input['tipo_usuario'], $input['seccion']);
            $dataCreate['second_last_name'] = $input['apellido_materno'];
            $dataCreate['second_last_name'] = $input['apellido_materno'];
            $dataCreate['email'] = $input['email'];
            $dataCreate['school_id'] = $input['school_id'];

            $password = $dataCreate['password'] = User::createPassword($input['seccion']);

            $firstName = $input['nombre'];
            $lastName = $input['apellido_paterno'];
            $email = $input['email'];
            $secondName = $input['segundo_nombre'];
            $username = Str::slug($firstName . $lastName);

            $reuser = User::where([
                ['username', '=', $username]
            ])->first(['id', 'username', 'second_name', 'email']);

            if ($reuser) {
                if ($reuser['email'] === $email && $reuser['second_name'] === $secondName) {
                    return (["message" => "El usuario ya existe", "username" => $username]);
                } else {
                    $i = 0;
                    while (User::whereUsername($username)->exists()) {
                        $i++;
                        $username = Str::slug($firstName[0] . $lastName . $i);
                    }
                    $dataCreate['username'] = $username;
                }
            } else {
                $dataCreate['username'] = $username;
            }
            $user = User::create($dataCreate);

            $data = ([
                'username' => $user->username,
                'name' => $user->name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'grade' => $user->grade,
                'password' => $password
            ]);

            SendEmail::dispatchNow($data)->onQueue('processing');

            return ('Se ha creado el usuario');

        } catch (Exception $e) {
            return ('Error al crear el usuario');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\User $user
     * @param \Illuminate\Http\Request $request
     * @param uuid $uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $user = User::where('uuid', 'like', '%' . $uuid . '%')->get();
        return $user->toJson(JSON_PRETTY_PRINT);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @param uuid $uuid
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
     * @param \App\User $user
     * @param uuid $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $user = User::where('uuid', 'like', '%' . $uuid . '%')->firstOrFail();
        $user->delete();

        return response()->json([
            $user,
            "message" => "El usuario ha sido eliminado existosamente",
        ], 200);


    }

}
