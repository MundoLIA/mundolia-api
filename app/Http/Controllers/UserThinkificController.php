<?php

namespace App\Http\Controllers;

use App\Jobs\SyncUserPlatforms;
use App\SyncUser;
use App\User;
use App\UserThinkific;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserThinkificController extends Controller
{
    public function getUsers(){
        $user = new UserThinkific();
        $user = $user->loadUsers();
        return $user;
    }

    public function storeUser(Request $inputData){

        $user = new UserThinkific();
        $user = $user->createUser($inputData);
        return $user;
    }

    public function editUser(Request $inputData, $userid){

        $user = new UserThinkific();
        $user = $user->updateUser($inputData, $userid);
        return $user;
    }



    public function deleteUser($userid){

        $user = new UserThinkific();
        $user = $user->deleteUserSchooling($userid);
        return $user;

    }

    // Create JWT single sign on Thikinfic
    public function singleSignThinkific(Request $request){
        $user = new UserThinkific();
        $user = $user->singleSignOn($request);
        return $user;
    }


    public function syncUserplatform(){
        $user = new SyncUser();
        $user = $user->transferUsers();
        return $user;
    }

    public function enrollment($id, $data)
    {
        $user = new UserThinkific();
        $user->enrollmentStudent($id, $data);
        return $user;
    }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return csrf_token();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserThinkific  $userThinkific
     * @return \Illuminate\Http\Response
     */
    public function show(UserThinkific $userThinkific)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserThinkific  $userThinkific
     * @return \Illuminate\Http\Response
     */
    public function edit(UserThinkific $userThinkific)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserThinkific  $userThinkific
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserThinkific $userThinkific)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserThinkific  $userThinkific
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserThinkific $userThinkific)
    {
        //
    }
}
