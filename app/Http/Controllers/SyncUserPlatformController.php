<?php

namespace App\Http\Controllers;

use App\SyncUser;
use App\User;
use App\UserCommunity;
use Carbon\Carbon;
use Facade\FlareClient\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SyncUserPlatformController extends ApiController
{
    /**
     * Sync users in all the platforms
     */
    public function syncUserplatform(){
        $user = new SyncUser();
        $user = $user->transferUsers();
        return $user;
    }

    public function syncUserCommunity(){
        $inactive = 0;

        $results = User::where([
            ['role_id', '>', 1],
            ['active_thinkific', '=', $inactive]
        ])->orWhere([
            ['role_id', '>', 1],
            ['active_phpfox', '=', $inactive]
        ])->get();

        $i = 0;

        if ($results->isEmpty()) {
            return ['message' => 'No hay usuarios por sincronizar'];
        } else {
            foreach ($results as $obj) {
                $syncUser = $obj;

                if(UserCommunity::where([['email', '=', $syncUser->email]])->exists()){

                    $error['error'] = 'Datos invalidos';
                    $error['message'] = "El correo electronico ya a sido asignado";
                    $count[++$i] = (array)["comunidad" => $error, "id" => $syncUser->id];

                    Log::info(json_encode($error));
                }else {

                    $dataFox = ([
                        'user_group_id' => 2,
                        'email' => $syncUser->email,
                        'full_name' => $syncUser->name . ' ' . $syncUser->last_name,
                        "user_name" => $syncUser->username,
                        'joined' => Carbon::now()->timestamp
                    ]);

                    $userCommunity = UserCommunity::create($dataFox);
                    $lastUser = UserCommunity::all()->last();

                    $syncUser->active_phpfox = $lastUser->user_id;
                    $syncUser->save();
                    $count[++$i] = (array)["comunidad" => $userCommunity, "id" => $syncUser->id];
                }
            }
            return $this->successResponse(["usuarios" => $count]);
        }
    }


    /**
     * Update the specified resource in storage in all platforms.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SyncUser  $syncUser
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUser($id)
    {
        $user = new SyncUser;
        $res = $user->update($id);
        return $res;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SyncUser  $syncUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(SyncUser $syncUser)
    {
        //
    }

    public function validateUserCommunity(){
        $messages = [
            'unique.email' => 'El correo electrónico ya esta asignado',
        ];

        return Validator::make(request()->all(), [
            'email' => 'required|unique'
        ], $messages);
    }
}
