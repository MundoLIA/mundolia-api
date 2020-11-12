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

        $dataLia = ([
            'School' => $schoolName,
            'Description' => $schoolDescription
        ]);

        $schoolLia = SchoolLIA::create($dataLia);

        $schoolId = $schoolLia->SchoolId;

        $data = ([
           'id' => $schoolId,
           'name' => $schoolName,
           'description' => $schoolDescription
        ]);

        $school = School::create($data);
        $schoolArray[] = array($schoolLia, $school);

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
        $school = School::find($id);
        return $this->successResponse($school);
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
     */
    public function update(Request $request,$id)
    {
        try {
            School::findOrFail($id);

            $schoolName = $request->name;
            $schoolDescription = $request->description;

            $dataLia = ([
                'School' => $schoolName,
                'Description' => $schoolDescription,
                'IsActive' => $request->is_active,
            ]);

            $schoolLia = SchoolLIA::where('SchoolId','like','%'.$id.'%')->firstOrFail()->update($dataLia);;
            $school = School::updateDataId($id);
            $schoolArray[] = array($schoolLia, $school);

            return $this->successResponse($schoolArray, 'La escuela ha sido actualizada', 200);

        }catch (ModelNotFoundException $e){
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    /**
     * Remove the specified resource from storage.

     */
    public function destroy($id)
    {
        try {
            $data = SchoolLIA::findOrFail($id);

            if(User::where('school_id', $data->school_id)->get()){
                return $this->errorResponse('La escuela tiene estudiantes relacionados', 401);
            }

            $schoolLIA = SchoolLIA::destroy($id);
            $school = School::destroy($id);

            return $this->successResponse($data, 'A sido eliminada con exito');
        }catch (ModelNotFoundException $e){
            return $this->errorResponse($e->getMessage(),404);
        }
    }

    public function validateSchool(){
        $messages = [
            'required' => 'El campo :nombre es requirido.',
        ];

        return Validator::make(request()->all(),
            [
                'name' => 'required|max:255',
                'description' => 'string|max:255',
            ], $messages);
    }
}
