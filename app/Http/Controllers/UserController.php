<?php

namespace App\Http\Controllers;

use App\Mail\SendgridMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\User;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;
use Ramsey\Uuid\Uuid;


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
            'name',
            'second_name',
            'last_name',
            'second_last_name',
            'school_id',
            'school_name',
            'email',
            'grade',
            'avatar',
            'is_active',
            'verified_email',
            'updated_at',
            'created_at'
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
        $request->validate([
            'username' => 'required|unique:users',
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|max:255',
            'grade' => 'required|max:1',
        ]);

        try{
            $input = $request->all();
            $password = $input['password'];
            $input['password'] = Hash::make($input['password']);
            $input['password'] = str_replace("$2y$", "$2a$", $input['password']);
            $user = User::create($input);

            $data = ([
                'username' => $user->username,
                'name' => $user->name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'grade' => $user->grade,
                'password' => $password
            ]);

            Mail::to('antonio2120@gmail.com')->queue(new SendgridMail($data));

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
