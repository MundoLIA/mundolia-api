<?php

namespace App\Http\Controllers;

use App\Mail\SendgridMail;
use App\User;
use Illuminate\Http\Request;
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

            $data = $request->input('data');
            $i = 0;
            foreach ($data as $obj) {
                foreach ($obj as $key => $value) {

                    $insertArr[Str::slug($key, '_')] = $value;
                }
                $resp = $obj;
                $resp ['result'] = User::dataUser($insertArr);
                $result [++$i] = $resp;
            }

            return response()->json($result,200);


        } catch (Exception $e) {
            $error["code"] = 'INVALID_DATA';
            $error["message"] = "The field is invalid or the user does not have a password.";
            $errors["domain"] = "global";
            $errors["reason"] = "invalid";

            $error["errors"] = [$errors];

            return response(['error' => $error], 500);
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
