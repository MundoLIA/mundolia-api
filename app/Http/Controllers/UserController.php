<?php

namespace App\Http\Controllers;

use App\Mail\SendgridMail;
use Illuminate\Support\Facades\Mail;
use App\User;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
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
        $users = User::select(
            'id',
            'uuid',
            'name',
            'second_name',
            'last_name',
            'second_last_name',
            'email',
            'id_school',
            'grade',
            'avatar',
            'active',
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'grade' => 'required'
        ]);

        $data = ([
            'uuid' => $request->get('uuid'),
            'username' => $request->get('username'),
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'grade' => $request->get('grade'),
            'password' => $request->get('password')
        ]);

        $user = User::create($request->all());

        Mail::to('dylan.lievano.cuevas@gmail.com')->queue(new SendgridMail($data));

        return response()->json([
            $user,
            "message" => "Se ha registrado un nuevo usuario",
        ], 201);
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
    public function update(Request $request, $uuid)
    {

        $updateData = $request->validate([
            'userdata.name' => 'required|max:255',
            'userdata.uuid' => 'required|max:255',
            'userdata.last_name' => 'required|max:255',
            'userdata.email' => 'required|max:255',
            'userdata.grade' => 'required|max:255'
        ]);

        User::whereId($uuid)->update($updateData);

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
