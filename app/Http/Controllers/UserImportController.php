<?php

namespace App\Http\Controllers;

use App\Mail\SendgridMail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;

class UserImportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $school_id =  $user->school_id;
            $password = null;
            $input = request()->all();

            if($user->role_id == 1 || $user->role_id == 2){
                $school_id = $request->input('school_id');
            }
            if (array_key_exists('password', $input)) {
                $password  = $input['password'];
            }
            $data = $request->input('data');
            $i = -1;
            foreach ($data as $obj) {
                foreach ($obj as $key => $value) {

                    $insertArr[Str::slug($key, '_')] = $value;
                }

                $resp = $obj;
                $respCreate = User::dataUser($insertArr, $school_id, $password);
                $resp ['result'] = $respCreate["message"];
                $resp ['username'] = $respCreate["username"];
                $result [++$i] = (array) $resp;
            }

            return response((array) $result,200);

        } catch (Exception $e) {

            return ('Error al crear el usuario');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */

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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
