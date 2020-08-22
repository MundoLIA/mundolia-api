<?php

namespace App\Http\Controllers;

use App\LicenseKey;
use Illuminate\Http\Request;

class LicenseKeyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $licenses = LicenseKey::get()->toJson(JSON_PRETTY_PRINT);
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
            'license_id' => 'required',
            'user_id' => 'required',
        ]);

        $license = LicenseKey::create($request->all());

        return response()->json([
            $license,
            "message" => "Nueva llave creada",
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LicenseKey  $licenseKey
     * @param  uuid $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $licenseKey = LicenseKey::find($id);
        return response($licenseKey, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LicenseKey  $licenseKey
     * @return \Illuminate\Http\Response
     */
    public function edit(LicenseKey $licenseKey)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LicenseKey  $licenseKey
     * @param  uuid $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        LicenseKey::updateDataId($id);

        return response()->json([
            "message" => "Se ha actualizado la licencia existosamente",
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LicenseKey  $licenseKey
     * @return \Illuminate\Http\Response
     */
    public function destroy(LicenseKey $licenseKey, $id)
    {
        $licenseKey::destroy($id);

        return response()->json([
            $licenseKey,
            "message" => "Se ha eliminado la licencia",
        ], 200);
    }
}
