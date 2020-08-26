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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $input = $request->all();
            $password = $input['password'];
            $firstName = $input['name'];
            $lastName = $input['last_name'];
            $email = $input['email'];
            $username= Str::slug($firstName . $lastName);

            $reuser = User::where([
                ['username','=', $username]
            ])->first(['id', 'username', 'email']);

            if ($reuser) {

                if ($reuser['email'] === $email){
                    return response()->json(['Usuario en base de datos encontrado']);
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
            if( env('MAIL_CONFIG', 'dev') == 'dev') {
                Mail::to(env('MAIL_CONFIG', 'dylan.lievano.cuevas@gmail.com'))->queue(new SendgridMail($data));
            }else{
                Mail::to($user->email)->queue(new SendgridMail($data));
            }

            return response()->json([
                $user,
                "message" => "Se ha registrado correctamente",
            ], 201);
        }catch (Exception $e){
            $error["code"] = 'INVALID_DATA';
            $error["message"] = "The field is invalid or the user does not have a password.";
            $errors["domain"] = "global";
            $errors["reason"] = "invalid";

            $error["errors"] =[$errors];

            return response()->json(['error' => $error], 500);
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
            "message" => "El estudiante ha sido eliminado existosamente",
        ], 200);


    }

}
