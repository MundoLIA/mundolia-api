<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmail;
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
use Validator;


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

            $users = \DB::table('users')
                ->leftJoin('schools', 'users.school_id', '=', 'schools.id')
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
                ->select(
                    'users.id',
                    'users.uuid',
                    'users.username',
                    'users.name',
                    'users.second_name',
                    'users.last_name',
                    'users.second_last_name',
                    'users.school_id',
                    'schools.name as school_name',
                    'roles.name as role_name',
                    'users.role_id',
                    'users.email',
                    'users.grade',
                    'users.avatar',
                    'users.is_active',
                    'users.verified_email')
                ->get()->where('role_id','<>', 1)->toJson(JSON_PRETTY_PRINT);

            return response($users, 200);
        }
        if($user->role_id == 3){
            $users = \DB::table('users')
                ->leftJoin('schools', 'users.school_id', '=', 'schools.id')
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
                ->select(
                    'users.id',
                    'users.uuid',
                    'users.username',
                    'users.name',
                    'users.second_name',
                    'users.last_name',
                    'users.second_last_name',
                    'users.school_id',
                    'schools.name as school_name',
                    'roles.name as role_name',
                    'users.role_id',
                    'users.email',
                    'users.grade',
                    'users.avatar',
                    'users.is_active',
                    'users.verified_email')
                ->get()->where([
                    ['role_id','<>', 1],
                    ['school_id','=', $user-school_id]
                ])->toJson(JSON_PRETTY_PRINT);
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'role_id' => 'required',
                'school_id' => 'required',
                'username' => 'required',
                'last_name' => 'required',
                'grade' => 'required',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                $error["code"] = 'INVALID_DATA';
                $error["message"] = "Información Invalida.";
                $error["errors"] =$validator->errors();

                return response()->json(['error' => $error], 200);
            }

            $user = Auth::user();
            $input = $request->all();

            if($user->role_id == 1 || $user->role_id == 2){
                $dataCreate['role_id'] = $input['role_id'];
                $dataCreate['school_id'] = $input['school_id'];
            }else{
                if ( $input['role_id'] == 4 ||  $input['role_id'] == 5 ||  $input['role_id'] == 9 ){
                    $dataCreate['role_id'] = $input['role_id'];
                }else{
                    $dataCreate['role_id'] = 4;
                }
                $dataCreate['school_id'] = $user->school_id;
            }

            $dataCreate['name'] = $input['name'];
            $dataCreate['username'] = $input['username'];
            $dataCreate['last_name'] = $input['last_name'];
            $dataCreate['grade'] = $input['grade'];
            $dataCreate['email'] = $input['email'];

            $password = $dataCreate['password'] =$input['password'];

            $email = $input['email'];
            $username = $dataCreate['username'];

            $reuser = User::where([
                ['username', '=', $username]
            ])->first(['id', 'username', 'second_name', 'email']);

            if ($reuser) {
                if ($reuser['email'] == $email ) {

                    $error["code"] = 'INVALID_DATA';
                    $error["message"] = "El usuario ya existe.";
                    $errors["username"] = 'El usuario ya existe';

                    $error["errors"] =$errors;
                    return response()->json(['error' => $error], 200);
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

            SendEmail::dispatchNow($data);

            $success['message'] = 'Usuario creado';
            $success['code'] = 200;
            return response()->json($success,200);

        } catch (Exception $e) {
            $error["code"] = 'INVALID_DATA';
            $error["message"] = "Error al crear el usuario";
            $errors["username"] = "Error al crear el usuario.";

            $error["errors"] =[$errors];

            return response()->json(['error' => $error], 500);
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


        $request = request()->all();

        try {
            $validator = Validator::make($request, [
                'name' => 'required',
                'email' => 'required|email',
                'role_id' => 'required',
                'school_id' => 'required',
                'last_name' => 'required',
                'grade' => 'required',
            ]);
            if ($validator->fails()) {
                $error["code"] = 'INVALID_DATA';
                $error["message"] = "Información Invalida.";
                $error["errors"] =$validator->errors();

                return response()->json(['error' => $error], 200);
            }

            $user = Auth::user();
            $input = $request;

            if($user->role_id == 1 || $user->role_id == 2){
                $dataCreate['role_id'] = $input['role_id'];
                $dataCreate['school_id'] = $input['school_id'];
            }else{
                if ( $input['role_id'] == 4 ||  $input['role_id'] == 5 ||  $input['role_id'] == 9 ){
                    $dataCreate['role_id'] = $input['role_id'];
                }else{
                    $dataCreate['role_id'] = 4;
                }
                $dataCreate['school_id'] = $user->school_id;
            }

            $dataCreate['name'] = $input['name'];
            $dataCreate['last_name'] = $input['last_name'];
            $dataCreate['grade'] = $input['grade'];
            $dataCreate['email'] = $input['email'];


            if (array_key_exists('password', $input)) {
               $dataCreate['password'] =$input['password'];
            }

            User::where('uuid','like','%'.$uuid.'%')->firstOrFail()
                ->update($dataCreate);

            $success['message'] = 'Usuario Actualizado';
            $success['code'] = 200;
            return response()->json($success,200);

        } catch (Exception $e) {
            $error["code"] = 'INVALID_DATA';
            $error["message"] = "Error al crear el usuario";
            $errors["username"] = "Error al crear el usuario.";

            $error["errors"] =[$errors];

            return response()->json(['error' => $error], 500);
        }
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
