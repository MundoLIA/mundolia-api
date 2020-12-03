<?php

namespace App\Http\Controllers;

use App\SyncUser;
use App\User;
use App\UserPhpFox;
use Illuminate\Support\Facades\Validator;

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

    public function syncUserCommunity()
    {
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

                $dataFox = ([
                    'email' => $syncUser->email,
                    'full_name' => $syncUser->name . ' ' . $syncUser->last_name,
                    "user_name" => $syncUser->username,
                    "password" => 'ClubLia'
                ]);

                $user = new UserPhpFox();
                $userCommunity = $user->createUser($dataFox);

                if (!empty($userCommunity['data'])) {
                    $affected = User::find($syncUser->id);
                    $affected->active_phpfox = $userCommunity['data']['user_id'];
                    $affected->save();
                    $count[++$i] = (array)["comunidad" => $userCommunity, "id" => $syncUser->id];
                } else {
                    if ($userCommunity["status"] === 'failed') {
                        $count[++$i] = (array)["comunidad" => $userCommunity, "id" => $syncUser->id];
                    }
                }
                //$userCommunity = UserCommunity::create($dataFox);
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
