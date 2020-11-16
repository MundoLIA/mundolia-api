<?php

namespace App\Http\Controllers;

use App\School;
use App\SchoolLIA;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ApiController;

class SchoolController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schools = School::all();
        return $this->successResponse($schools);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $validator = $this->validateSchool();
        if($validator->fails()){
            return $this->errorResponse($validator->messages(), 422);
        }

        $schoolName = $request->name;
        $schoolDescription = $request->description;
        $schoolEditor = 68;
        $schoolCreator = 68;

        $dataLia = ([
            'School' => $schoolName,
            'Description' => $schoolDescription,
            'CreatorId' => $schoolCreator,
            'EditorId' => $schoolEditor
        ]);

        $schoolLia = SchoolLIA::create($dataLia);

        $schoolId = $schoolLia->SchoolId;

        $data = ([
           'id' => $schoolId,
           'name' => $schoolName,
           'description' => $schoolDescription
        ]);

        $school = School::create($data);
        $schoolArray[] = array(['Sistema Lia',$schoolLia], ['Sistema de licencias', $school]);

        return $this->successResponse($schoolArray,'Se ha creado la escuela con exito', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\School  $school
     * @param  int $id
     */
    public function show($id)
    {
        try {
        $school = School::findOrFail($id);
        return $this->successResponse($school);
        }catch (ModelNotFoundException $e){
            return $this->errorResponse('Tipo de licencia invalido', 422);
        }

    }


    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        try {
            $school = School::findOrFail($id);
            $schoolLia = SchoolLIA::findOrFail($id);

            $schoolName = $request->name;
            $schoolDescription = $request->description;

            $dataLia = ([
                'School' => $schoolName,
                'Description' => $schoolDescription,
                'IsActive' => $request->is_active,
            ]);

            $schoolLiaUpt = $schoolLia->update($dataLia);;
            $schoolUpt = School::updateDataId($id);
            $schoolArray[] = array($schoolLiaUpt, $schoolUpt);

            return $this->successResponse($schoolArray, 'La escuela ha sido actualizada', 200);

        }catch (ModelNotFoundException $e){
            return $this->errorResponse('No hay elementos que coincidan', 404);
        }
    }

    /**
     * Remove the specified resource from storage.

     */
    public function destroy($id)
    {
        try {
            $dataLia = SchoolLIA::findOrFail($id);
            $data = School::findOrFail($id);


            $schoolLIA = SchoolLIA::destroy($id);
            $school = School::destroy($id);
            $schoolArray[] = array($schoolLIA, $school);

            return $this->successResponse($schoolArray, 'Se ha eliminado la escuela con exito');
        }catch (ModelNotFoundException $e){
            return $this->errorResponse('No hay elementos que coincidan',404);
        }
    }

    public function validateSchool(){
        $messages = [
            'required' => 'El campo :nombre es requirido.',
        ];

        return Validator::make(request()->all(), [
                'name' => 'required|max:255',
                'description' => 'string|max:255',
            ], $messages);
    }
}
