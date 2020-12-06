<?php

namespace App\Http\Controllers;

use App\PhpFox_user_activity;
use App\PhpFox_user_count;
use App\PhpFox_user_field;
use App\PhpFox_user_space;
use App\SyncUser;
use App\User;
use App\UserCommunity;
use App\UserPhpFox;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class SyncUserPlatformController extends ApiController
{
    /**
     * Sync users in all the platforms
     */
    public function syncUserplatform()
    {
        $user = new SyncUser();
        $user = $user->transferUsers();
        return $user;
    }

    public function syncUserCommunity(Request $request)
    {
        $input = request()->all();

        $inactive = 0;

        $results = User::where([
            ['role_id', '>', 1],
            ['active_phpfox', '=', $inactive]
        ])->offset($input["offset"])->limit($input["limit"])->get();

        $i = 0;

        if ($results->isEmpty()) {
            return ['message' => 'No hay usuarios por sincronizar'];
        } else {
            foreach ($results as $obj) {
                $syncUser = $obj;



                $dataFox = ([
                    'email' => $syncUser->email,
                    'user_group_id' => 2,
                    'full_name' => $syncUser->name . ' ' . $syncUser->last_name,
                    "user_name" => $syncUser->username,
                    "joined" => Carbon::now()->timestamp
                ]);

                if (UserCommunity::where([['email', '=', $syncUser->email]])->exists()) {
                    $count[++$i] = (array)["message" => 'El correo electronico ya esta asignado', "id" => $syncUser->id, "email"=> $syncUser->email];
                } else {
                    //$user = new UserPhpFox();
                    //$userCommunity = $user->createUser($dataFox);
                    $userCommunity = UserCommunity::create($dataFox)->toArray();

                    $userCommunityId = ['user_id' => $userCommunity['id']];
                    PhpFox_user_activity::create($userCommunityId);
                    PhpFox_user_field::create($userCommunityId);
                    PhpFox_user_space::create($userCommunityId);
                    PhpFox_user_count::create($userCommunityId);

                    if (!empty($userCommunity)) {
                        $affected = User::find($syncUser->id);
                        $affected->active_phpfox = $userCommunity["id"];
                        $affected->save();
                        $count[++$i] = (array)["comunidad" => $userCommunity, "id" => $syncUser->id];
                    } else {
                        if ($userCommunity["status"] === 'failed') {
                            $count[++$i] = (array)["comunidad" => $userCommunity, "id" => $syncUser->id];
                        }
                    }
                }

            }
            return $this->successResponse(["usuarios" => $count]);
        }
    }

    /**
     * Update the specified resource in storage in all platforms.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\SyncUser $syncUser
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
     * @param \App\SyncUser $syncUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(SyncUser $syncUser)
    {
        //
    }

    public function validateUserCommunity()
    {
        $messages = [
            'unique.email' => 'El correo electrónico ya esta asignado',
        ];

        return Validator::make(request()->all(), [
            'email' => 'required|unique'
        ], $messages);
    }
}
