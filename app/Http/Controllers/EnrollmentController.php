<?php

namespace App\Http\Controllers;

use App\Enrollment;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Util\Json;

class EnrollmentController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $enrollment = Enrollment::all();
        return $this->successResponse($enrollment);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = $this->validateEnrollment();
            if($validator->fails()){
                return $this->errorResponse($validator->messages(), 422);
            }

            $enrollment = Enrollment::create($request->all());
            return $this->successResponse($enrollment, "Se ha matriculado al usuario exitosamente", 201);
        }catch (QueryException $exception){
            return $this->errorResponse('Verifica que los datos sean correctos', 422 );
        }catch (Exception $e){
            return $this->errorResponse('No se ha matriculado al usuario debido a un problema', 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function show(Enrollment $enrollment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function edit(Enrollment $enrollment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            Enrollment::findOrFail($id);
            $enrollDlt = Enrollment::destroy($id);

            return $this->successResponse($enrollDlt, "Se ha eliminado el grado exitosamente", 200);
        }catch (ModelNotFoundException $e){
            return $this->errorResponse('Grado inválido: No hay elementos que coincidan', 422);
        }
    }

    public function validateEnrollment(){
        $messages = [
            'user_id.required' => 'Es necesario ingresar un usuario',
            'period_id.required' => 'El campo periodo es requerido',
            'school_id.required' => 'El campo escuela es requerido',
            'license_id.required' => 'El campo licencia es requerido',
            'license_key_id.required' => 'El campo llave es requerido',
            'role_id.required' => 'El campo rol es requerido',
            'grade_id.required' => 'El campo grado es requerido',
        ];

        return Validator::make(request()->all(), [
            'user_id' => 'required',
            'period_id' => 'required',
            'school_id' => 'required',
            'license_id' => 'required',
            'license_key_id' => 'required',
            'role_id' => 'required',
            'grade_id' => 'required',
        ], $messages);
    }
}
