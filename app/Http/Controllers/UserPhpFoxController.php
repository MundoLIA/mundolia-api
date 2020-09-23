<?php

namespace App\Http\Controllers;

use App\User;
use App\UserPhpFox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;

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

    public function singleSignPhpFox(){
        try {
            $user = Auth::user();
            $phpfox = new UserPhpFox();
            $url = $phpfox->singleSignOn($user);


            return response( $url,200);

        } catch (Exception $e) {
            return ('Error Login user.');
        }

    }

}
