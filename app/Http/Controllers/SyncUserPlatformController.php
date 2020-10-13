<?php

namespace App\Http\Controllers;

use App\SyncUser;
use Illuminate\Http\Request;

class SyncUserPlatformController extends Controller
{
    /**
     * Sync users in all the platforms
     */
    public function syncUserplatform(){
        $user = new SyncUser();
        $user = $user->transferUsers();
        return $user;
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
}
