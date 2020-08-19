<?php

namespace App\Http\Controllers;

use App\LicenseType;
use Illuminate\Http\Request;

class LicenseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $licenses = LicenseType::get()->toJson(JSON_PRETTY_PRINT);
        return response($licenses, 200);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'description_license_type' => 'required',
        ]);

        $licenses = LicenseType::create($request->all());

        return response()->json([
            $licenses,
            "message" => "Nuevo tipo de licensia creada existosamente",
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LicenseType  $licenseType
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $licenseType = LicenseType::find($id);
        return response($licenseType, 200);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LicenseType  $licenseType
     * @return \Illuminate\Http\Response
     */
    public function edit(LicenseType $licenseType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LicenseType  $licenseType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $licenseType= LicenseType::findOrFail($id);
        $licenseType->update($request->all());

        return response()->json([
            $licenseType,
            "message" => "Se ha actualizado existosamente",
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LicenseType  $licenseType
     * @return \Illuminate\Http\Response
     */
    public function destroy(LicenseType $licenseType, $id)
    {
        $licenseType::destroy($id);

        return response()->json(null, 204);
    }
}
