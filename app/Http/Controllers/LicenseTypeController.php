<?php

namespace App\Http\Controllers;

use App\LicenseType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LicenseTypeController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $licenses = LicenseType::all();
        return $this->successResponse($licenses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $validator = $this->validateTypeLicense();
        if($validator->fails()){
            return $this->errorResponse($validator->messages(), 422);
        }

        $license = LicenseType::create($request->all());

        return $this->successResponse($license);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $licenseType = LicenseType::findOrFail($id);
            return $this->successResponse($licenseType);
        }catch(ModelNotFoundException $e){
            return $this->errorResponse('Tipo de licencia invalido', 422);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        try {
            LicenseType::firstOrFail($id);

            LicenseType::updateDataId($id);
            return $this->successResponse('Se ha actualizado el tipo de licencia', 201);

        }catch(ModelNotFoundException $e){
            return $this->errorResponse('Tipo de lincencia invalido', 422);
        }
    }

    /**
     * Remove the specified resource from Type license.
     */
    public function destroy($id)
    {
        try {
            $type = LicenseType::firstOrFail($id);
            $type::destroy();

            return $this->successResponse('Se ha eliminado correctamente');
        }catch (ModelNotFoundException $e){

        }
    }

    public function validateTypeLicense(){
        $messages = [
            'requiered' => 'Es necesario agregar una descripción del tipo de licencia',
        ];

        return Validator::make(request()->all(), [
            'description_license_type' => 'required',
        ]);
    }
}
