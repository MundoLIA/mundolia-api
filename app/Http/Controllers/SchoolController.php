<?php

namespace App\Http\Controllers;

use App\School;
use App\SchoolLIA;
use App\SyncGroupComunnity;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

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
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validator = $this->validateSchool();
        if($validator->fails()){
            return $this->errorResponse($validator->messages(), 422);
        }
        
        $schoolName = $request->School;
        $schoolDescription = $request->Description;
        $schoolActive = $request->IsActive;
        $schoolEditor = 68;
        $schoolCreator = 68;

        $dataLia = ([
            'School' => $schoolName,
            'Description' => $schoolDescription,
            'IsActive' => $schoolActive,
            'CreatorId' => $schoolCreator,
            'EditorId' => $schoolEditor
        ]);

        $schoolLia = SchoolLIA::create($dataLia);
        $schoolId = $schoolLia->SchoolId;

        $dataGroup = ([
            'app_id' => 0,
            'view_id' => 0,
            'type_id' => 6,
            "category_id" => 0,
            "user_id" => 20,
            "title" => $schoolName,
            "reg_method" => 1,
            "landing_page" => null,
            "time_stamp" => Carbon::now()->timestamp,
            "image_path" => null,
            "is_featured" => 0,
            "is_sponsor" => 0,
            "image_server_id" => 0,
            "total_like" => 1,
            "total_dislike" => 0,
            "total_comment" => 0,
            "privacy" => 0,
            "designer_style_id" => 0,
            "cover_photo_id" => null,
            "cover_photo_position" => null,
            "location_latitude" => null,
            "location_longitude" => null,
            "location_name" => null,
            "use_timeline" => 0,
            "item_type" => 1
        ]);

        $group = SyncGroupComunnity::create($dataGroup);

        $data = ([
           'id' => $schoolId,
           'name' => $schoolName,
           'description' => $schoolDescription,
           'is_active' => $schoolActive
        ]);

        $school = School::create($data);
        $schoolArray[] = array(['Sistema Lia',$schoolLia], ['Sistema de licencias', $school], ['Comunidad', $group]);

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
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request, $id)
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
     * @return JsonResponse
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
            'required.name' => 'El campo :nombre es requirido.',
        ];

        return Validator::make(request()->all(), [
                'School' => 'required|max:255',
                'Description' => 'string|max:255',
            ], $messages);
    }
}
