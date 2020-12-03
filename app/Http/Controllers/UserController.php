<?php

namespace App\Http\Controllers;

use App\Jobs\DeleteGenericUserJob;
use App\Jobs\SendEmail;
use App\Jobs\UserGenericRegister;
use App\License;
use App\LicenseKey;
use App\School;
use App\UserCommunity;
use App\UserLIA;
use App\UserPhpFox;
use App\UserThinkific;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use \Illuminate\Support\Facades\Validator;


class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
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
            return $this->successResponse($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */

    public function store(Request $request)
    {
        try {

            $validator = $this->validateUser();
            if($validator->fails()){
                return $this->errorResponse($validator->messages(), 422);
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
            $passwordBcrypt = bcrypt($password);

            $passwordEncode = str_replace("$2y$", "$2a$", $passwordBcrypt);
            $dataCreate['password'] = $passwordEncode;

            $email = $input['email'];
            $username = $dataCreate['username'];

            $reuser = User::where([
                ['username', '=', $username]
            ])->first(['id', 'username', 'second_name', 'email']);

            if ($reuser) {
                if ($reuser['email'] == $email ) {
                    return $this->errorResponse('El usuario ya existe.', 422);
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
            if(Config::get('app.sync_lia')) {
                $userLIA = UserLIA::create($dataLIA);
                $dataCreate['AppUserId'] = $userLIA->AppUserId;
            }
            $user = User::create($dataCreate);

            $dataEmail = ([
                'username' => $user->username,
                'name' => $user->name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'grade' => $user->grade,
                'password' => $password
            ]);

            /*$dataThink = ([
                'first_name' => $user->username,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'password' => $password
            ]);*/


            $dataFox = ([
                'email' => $user->email,
                'full_name' => $user->name .' '. $user->last_name,
                "user_name" => $user->username,
                'password' => $password,
            ]);
//            if(Config::get('app.sync_thinkific')) {
//                UserGenericRegister::dispatch($dataThink, $dataFox);
//            }

            //$userCommunity = UserCommunity::create($dataFox);

            //$lastUserGroup = UserCommunity::all()->last();

            $userFox = new UserPhpFox();
            $userFox = $userFox->createUser($dataFox);

            $lastUserGroup = UserCommunity::all()->last();
            $user->active_phpfox = $lastUserGroup->user_id;
            $user->save();

            if(Config::get('app.send_email')) {
                SendEmail::dispatchNow($dataEmail);
            }


            $success['message'] = 'Usuario creado';
            $success['data'] = $userFox;
            return $this->successResponse($success,200);

        } catch (ModelNotFoundException $e) {
            $error["code"] = 'INVALID_DATA';
            $error["message"] = "Error al crear el usuario";
            $errors["username"] = "Error al crear el usuario.";

            $error["errors"] =[$errors];

            return $this->errorResponse(['error' => $error], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param uuid $uuid
     * @return JsonResponse
     */
    public function show($uuid)
    {

        $user = User::where('uuid', 'like', '%' . $uuid . '%')->get();
        return $this->successResponse($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param uuid $uuid
     * @return JsonResponse
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

            if(Config::get('app.sync_lia')) {
                UserLIA::where('AppUserId','=',$user->AppUserId)->firstOrFail()->update($dataLIA);
            }

            User::where('uuid','like','%'.$uuid.'%')->firstOrFail()->update($dataCreate);

            $success['message'] = 'Usuario Actualizado';
            $success['code'] = 200;
            return $this->successResponse($success,200);

        } catch (ModelNotFoundException $exception) {
            $error["code"] = '500';
            $error["message"] = "Error al actualizar el usuario";

            return $this->errorResponse(['error' => $error], 500);
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
                return $this->errorResponse(['error' => $error], 422);
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
            $users = \DB::table('users')->whereIn('uuid', $input['users'])->get()->toArray();

            foreach ($users as $obj) {
                if($obj->AppUserId){
                    $appUsersIds[] = $obj->AppUserId;
                }
            }
            if($dataUpdate){
                $dataUpdateResult = \DB::table('users')->whereIn('uuid', $input['users'])->update($dataUpdate);

                if(Config::get('app.sync_lia')) {
                    $dataLIAResult = \DB::connection('sqlsrv')->table('dbo.AppUsers')->whereIn('AppUserId', $appUsersIds)->update($dataLIA);
                }

                $success['message'] = $dataUpdateResult.' usuario(s) actualizado(s)';
                $success['code'] = 200;
            }else{
                $success['message'] = '0 usuarios actualizados';
                $success['code'] = 200;
            }
            return $this->successResponse($success,200);

        } catch (ModelNotFoundException $exception) {
            $error["code"] = '500';
            $error["message"] = "Error al actualizar los usuarios";

            return $this->errorResponse(['error' => $error], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     * @param uuid $uuid
     * @return JsonResponse
     */
    public function destroy($uuid)
    {
        try {

            $user = User::where('uuid', 'like', '%' . $uuid . '%')->firstOrFail();

            if ($user->role_id == 10) {
                \DB::table('users')->where('tutor_id', [$user->id])->update(['tutor_id' => null]);
            }

            if(Config::get('app.sync_lia')){
                $userLIA = UserLIA::find($user->AppUserId);
                $userLIA->delete();
            }

            $user->delete();

            if(Config::get('app.sync_thinkific')){
                $deleteSchooling = new UserThinkific();
                $deleteSchooling = (new \App\UserThinkific)->deleteUser($user->active_thikific);
            }

            $success['message'] = 'El usuario ha sido eliminado existosamente';
            $success['code'] = 200;
            return $this->successResponse($success,200);
        } catch (Exception  $exception) {
            $error["code"] = '500';
            $error["message"] = "Error al eliminar el usuario";
            $error["getMessage"] = $exception->getMessage();

            return $this->errorResponse(['error' => $error], 500);
        }
    }

    public function assignLicense(Request $limit){
        try{
            //listamos todos los usuarios
            $listUser = self::index()->getOriginalContent();

            $i = 0;

            foreach ($listUser as $obj => $user) {

                $schoolId = $user->school_id;
                $userUuid = $user->uuid;
                $roleId = $user->role_id;
                //Preguntamos si tiene el usuario cuenta con una llave
                if(LicenseKey::where([['user_id', '=', $userUuid]])->exists()) {
                    $count[$i++] = [
                        'message' => 'El usuario ya tiene una llave asignada',
                        'code' => 201
                    ];
                }else{
                    if ($roleId != 1 ){ //Aqui tienen que ir las demas condiciones de acuerdo al rol
                        $school = new School();
                        $school = $school->show($schoolId)->getOriginalContent();
                        if(License::where([['school_id', '=', $schoolId]])->exists()) {

                            $licenseId = License::where([['school_id', '=', $schoolId]])->first();

                            $dataKey = [
                                'user_id' => $userUuid,
                                'license_id' => $licenseId->id
                            ];

                            $licenseKey = LicenseKey::create($dataKey);

                        }else{
                            $dataLicense = [
                                'titular' => $school->name,
                                'email_admin' => 'dlievano@arkusnexus.com',
                                'school_id' => $schoolId,
                                'license_type_id' => 1,
                                'user_id' => $userUuid,
                                'studens_limit' => $limit["students_limit"],
                            ];

                            $license = License::create($dataLicense);
                            $license->save();

                            $dataKey = [
                                'user_id' => $userUuid,
                                'license_id' => $license->id
                            ];

                            $licenseKey = LicenseKey::create($dataKey);
                        }

                    }
                    $count[$i++] = [
                        'message' => 'El se a asignado una llave al usuario',
                        'data' => $licenseKey,
                        'code' => 201
                    ];
                }
            }
            return [
                'data' => $count
            ];
        }catch (\Exception $exception){
            return $exception;
        }

    }

    public function validateUser(){
        $messages = [
            'name.required' => 'El campo nombre es requerido.',
            'email.required' => 'El correo electrónico es requerido.',
            'role_id.required' => 'Es necesario seleccionar un tipo de rol',
            'school_id.required' => 'Es necesario seleccionar una escuela',
            'username.required' => 'El campo nombre de usuario es requerido',
            'last_name.required' => 'El campo apellido paterno es requerido',
            'grade.required' => 'Selecciona un grado',
            'password.required' => 'El campo contraseña es necesario',
        ];

        return Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'role_id' => 'required',
            'school_id' => 'required',
            'username' => 'required',
            'last_name' => 'required',
            'grade' => 'required',
            'password' => 'required',
        ], $messages);
    }
}
