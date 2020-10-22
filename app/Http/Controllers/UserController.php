<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmail;
use App\Jobs\UserGenericRegister;
use App\Mail\SendgridMail;
use App\UserLIA;
use App\UserThinkific;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Input\Input;
use Validator;
use function MongoDB\BSON\toJSON;


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
            $request = request()->all();
            $filter = [];
            $i = -1;
            $filter[++$i] = ['users.role_id', '<>', 1];
            if (array_key_exists('school_id', $request) && $request['school_id'] != null) {
                $filter[++$i] = array('users.school_id', '=',$request['school_id']);
            }
            if (array_key_exists('grade', $request) && $request['grade'] != null) {
                $filter[++$i] = array('users.grade', '=',$request['grade']);
            }
            if (array_key_exists('role_id', $request) && $request['role_id'] != null) {
                $filter[++$i] = array('users.role_id', '=',$request['role_id']);
            }
            if($user->role_id > 2){
                $filter[++$i] = array('users.school_id', '=', $user->school_id);
            }


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
                ->where($filter)->get();
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
                if ( $input['role_id'] == 4 ||  $input['role_id'] == 5 ||  $input['role_id'] == 13 ){
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

            $password  = $input['password'];
            $passwordEncode = bcrypt($password);
            $passwordEncode = str_replace("$2y$", "$2a$", $passwordEncode);
            $dataCreate['password'] = $passwordEncode;

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

            //$userLIA = UserLIA::create($dataLIA);

           $dataCreate['AppUserId'] = 239042;

            $user = User::create($dataCreate);

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
                'full_name' => $user->name .' '. $user->last_name,
                'password' => $password,
                'gender' => "1",
                "user_name" => $user->username
            ]);

            UserGenericRegister::dispatch($dataThink, $dataFox);
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
                if ( $input['role_id'] == 4 ||  $input['role_id'] == 5 ||  $input['role_id'] == 13 ){
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

                $password  = $input['password'];
                $passwordEncode = bcrypt($password);
                $passwordEncode = str_replace("$2y$", "$2a$", $passwordEncode);
                $dataCreate['password'] = $passwordEncode;

                $dataLIA = ([
                    'Names' =>  $dataCreate['name'],
                    'LastNames' => $dataCreate['last_name'],
                    'Email' =>  $dataCreate['email'],
                    'Grade' =>  $dataCreate['grade'],
                    'Password' => $dataCreate['password'],
                    'RoleId' =>  $dataCreate['role_id'],
                    'SchoolId' => $dataCreate['school_id']
                ]);
            }else{
                $dataLIA = ([
                    'Names' =>  $dataCreate['name'],
                    'LastNames' => $dataCreate['last_name'],
                    'Email' =>  $dataCreate['email'],
                    'Grade' =>  $dataCreate['grade'],
                    'RoleId' =>  $dataCreate['role_id'],
                    'SchoolId' => $dataCreate['school_id']
                ]);
            }

            $user = User::where('uuid', 'like', '%' . $uuid . '%')->firstOrFail();
            UserLIA::where('AppUserId','=',$user->AppUserId)->firstOrFail()
                ->update($dataLIA);

            User::where('uuid','like','%'.$uuid.'%')->firstOrFail()
                ->update($dataCreate);

            $success['message'] = 'Usuario Actualizado';
            $success['code'] = 200;
            return response()->json($success,200);

        } catch (Exception $e) {
            $error["code"] = 'INVALID_DATA';
            $error["message"] = "Error al actualizar el usuario";
            $errors["username"] = "Error al actualizar el usuario.";

            $error["errors"] =[$errors];

            return response()->json(['error' => $error], 500);
        }
    }

    public function updateGroup()
    {
        $request = request()->all();
        $dataUpdate = null;
        try {
            $validator = Validator::make($request, [
                'users' => 'required'
            ]);
            if ($validator->fails()) {
                $error["code"] = 'INVALID_DATA';
                $error["message"] = "Información Invalida.";
                $error["errors"] =$validator->errors();
                return response()->json(['error' => $error], 200);
            }

            $user = Auth::user();
            $input = $request;

            if (array_key_exists('role_id', $input)) {
                if($user->role_id == 1 || $user->role_id == 2){
                    $dataUpdate['role_id'] = $input['role_id'];
                }else{
                    if ( $input['role_id'] == 4 ||  $input['role_id'] == 5 ||  $input['role_id'] == 13 ){
                        $dataUpdate['role_id'] = $input['role_id'];
                    }else{
                        $dataUpdate['role_id'] = 4;
                    }
                }
                $dataLIA['RoleId'] = $dataUpdate['role_id'];
            }

            if (array_key_exists('school_id', $input)) {
                if($user->role_id == 1 || $user->role_id == 2){
                    $dataUpdate['school_id'] = $input['school_id'];
                }else{
                    $dataUpdate['school_id'] = $user->school_id;
                }
                $dataLIA['SchoolId'] = $dataUpdate['school_id'];
            }

            if (array_key_exists('grade', $input)) {
                $dataUpdate['grade'] = $input['grade'];
                $dataLIA['Grade'] = $dataUpdate['grade'];
            }

            if (array_key_exists('password', $input)) {
                $password  = $input['password'];
                $passwordEncode = bcrypt($password);
                $passwordEncode = str_replace("$2y$", "$2a$", $passwordEncode);
                $dataUpdate['password'] = $passwordEncode;
                $dataLIA['Password'] = $dataUpdate['password'];
            }
            $users = \DB::table('users')
                ->select(
                    'AppUserId',
                    'id'
                   )
                ->get()->whereIn('id', $input['users'])->toArray();

            foreach ($users as $obj) {
                if($obj->AppUserId){
                    $appUsersIds[] = $obj->AppUserId;
                }

            }
            if($dataUpdate){
                $dataUpdateResult = \DB::table('users')->whereIn('id', $input['users'])->update($dataUpdate);
                //$dataLIAResult    = \DB::connection('sqlsrv')->table('users')->whereIn('AppUserId', $appUsersIds)->update($dataLIA);

                $success['message'] = $dataUpdateResult.' usuario(s) actualizado(s)';
                $success['code'] = 200;
            }else{
                $success['message'] = '0 usuarios actualizados';
                $success['code'] = 200;
            }


            return response()->json($success,200);

        } catch (Exception $e) {
            $error["code"] = 'INVALID_DATA';
            $error["message"] = "Error al actualizar los usuarios";
            $errors["username"] = "Error al actualizar los usuarios.";
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
        //$userLIA = UserLIA::find($user->AppUserId);
        //$userLIA->delete();
        $user->delete();

        //$deleteSchooling = new UserThinkific();
        $deleteSchooling = (new \App\UserThinkific)->deleteUser($user->active_thikific);

        return response()([
            $user,
            $deleteSchooling,
            "message" => "El usuario ha sido eliminado existosamente",
        ], 200);

    }

}
