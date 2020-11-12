<?php

namespace App\Http\Controllers;

use App\License;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LicenseController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $licenses = License::all();
        return  $this->successResponse($licenses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $validator = $this->validateLicense();
        if($validator->fails()){
            return $this->errorResponse($validator->messages(), 422);
        }

        $license = License::create($request->all());

        return $this->successResponse($license,'Se ha creado una nueva licencia', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\License  $license
     * @param  uuid $id
     */
    public function show($id)
    {
        try {
            $licenseType = License::find($id);
            return response($licenseType, 200);
        } catch (ModelNotFoundException $e) {
            $e->getMessage();
            return $this->errorResponse($e,404);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        try {
            License::firstOrFail($id);

            License::updateDataId($id);
            return $this->successResponse('Se ha actualizado la licencia', 201);
        }catch (ModelNotFoundException $e){
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\License  $license
     * @param  uuid $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(License $license, $id)
    {
        try {
            License::firstOrFail($id);

        $license::destroy($id);

        return response()->json([
            $license,
            "message" => "Se ha eliminado la licencia",
        ], 200);
        }catch (ModelNotFoundException $e){
            return $this->errorResponse('No existe la licencia buscada',404);

        }

    }

    public function validateLicense(){
        $messages = [
            'titular:required' => 'El campo titular es requerido.',
            'email:required' => 'El correo electronico es requerido.',
            'license_type_id:required' => 'Es necesario selecionar un tipo de licencia',
        ];

        return Validator::make(request()->all(), [
            'titular' => 'required',
            'email_admin' => 'required',
            'license_type_id' =>'required',
            'studens_limit' => 'required',
        ]);
    }
}
