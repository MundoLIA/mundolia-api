<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiaSchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if($user->role_id == 1 || $user->role_id == 2) {
            $schools = \DB::connection('sqlsrv')
                ->select('Select SchoolId as id, SchoolId,School,Description,IsActive,CurrentUsers FROM dbo.Schools ORDER BY School');
        }else{
            $schools = \DB::connection('sqlsrv')
                ->select('Select SchoolId as id, SchoolId,School,Description,IsActive,CurrentUsers
                            FROM dbo.Schools
                            WHERE SchoolId = '. $user->school_id.'
                            ORDER BY School');
        }

        return response()->json($schools, 200);
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

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\School  $school
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\School  $school
     * @return \Illuminate\Http\Response
     */
    public function edit(School $school)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\School  $school
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(School $school, $id)
    {

    }
}
