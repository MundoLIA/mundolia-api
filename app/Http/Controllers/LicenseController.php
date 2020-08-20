<?php

namespace App\Http\Controllers;

use App\License;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $licenses = License::get()->toJson(JSON_PRETTY_PRINT);
        return response($licenses, 200);
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
            'titular' => 'required',
            'email_admin' => 'required',
            'school_id' => 'required',
            'license_type_id' =>'required',
            'user_id' => 'required',
            'studens_limit' => 'required',
        ]);

        $license = License::create($request->all());

        return response()->json([
            $license,
            "message" => "Nueva licencia creada existosamente",
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\License  $license
     * @param  uuid $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $licenseType = License::find($id);
        return response($licenseType, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\License  $license
     * @param  uuid $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\License  $license
     * @param uuid $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        License::updateDataId($id);

        return response()->json([
            "message" => "Se ha actualizado la licencia existosamente",
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\License  $license
     * @param  uuid $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(License $license, $id)
    {
        $license::destroy($id);

        return response()->json([
            $license,
            "message" => "Se ha eliminado la licencia",
        ], 200);
    }
}
