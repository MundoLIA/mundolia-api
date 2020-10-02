<?php

namespace App\Http\Controllers;

use App\UserPhpFox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class UserPhpFoxController extends Controller
{
    public function getToken(){
        $token = new UserPhpFox();
        $token = $token->getAuthorization();
        return $token;
    }

    public function storeUser(Request $inputData){
        $user = new UserPhpFox();
        $user = $user->createUser($inputData);
        return $user;
    }

    public function destroy($userid)
    {
        $userdelete = new UserPhpFox();
        $userdelete = $userdelete->deleteUserCommunity($userid);
        return $userdelete;
    }

}
