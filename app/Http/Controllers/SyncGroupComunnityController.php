<?php

namespace App\Http\Controllers;

use App\LikeUserGroup;
use App\School;
use App\SyncGroupComunnity;
use App\SyncModels\GroupUserEnrollment;
use App\User;
use Carbon\Traits\Timestamp;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SyncGroupComunnityController extends ApiController
{
    public function syncSchool(){

        $results = School::where([
            ['is_active' ,'=', '1']
        ])->get();

        $i = 0;



        if($results->isEmpty()){
            return $this->errorResponse('No hay escuelas por sincronizar', 422);
        }else {
            foreach ($results as $obj) {

                $syncSchool = $obj;

                $data = ([
                    'app_id' => 0,
                    'view_id' => 0,
                    'type_id' => 6,
                    "category_id" => 0,
                    "user_id" => 20,
                    "title" => $syncSchool->name,
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

                $group = SyncGroupComunnity::create($data);

                $count[$i++] = $group;

                $lastGroup = SyncGroupComunnity::all()->last();

                $userList = User::where([
                    ['school_id' ,'=' ,$syncSchool->id],
                    ['active_phpfox' ,'!=', 0 ]
                ]);

                $t = 0;

                foreach ($userList as $user){

                    $userId = $user->id;

                    $dataLike = ([
                        "like_id" => 1,
                        "type_id" => "groups",
                        "item_id" => $lastGroup->page_id,
                        "user_id" => $userId,
                        "feed_table" => "feed",
                        "time_stamp" => Carbon::now()->timestamp
                    ]);

                    $dataGroup = ([
                        'user_id' => $userId,
                        'school_id' => $syncSchool->id,
                        'group_id_community' =>  $lastGroup->page_id,
                        'group_id_academy' => 0
                   ]);

                    $groupCreated = GroupUserEnrollment::create($dataGroup);

                    $userLike = LikeUserGroup::create($dataLike);

                    $count[$t++] = [$userLike, $groupCreated];
                }
            }
        }
        return $this->successResponse($count);
    }
}
