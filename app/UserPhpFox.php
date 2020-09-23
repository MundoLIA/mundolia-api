<?php

namespace App;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class UserPhpFox
{
    protected $key;
    protected $secret;
    protected $url;

    function __construct() {
        $this->key = Config::get('app.phpfox');
        $this->secret = Config::get('app.secret_phpfox');
        $this->url = Config::get('app.url_phpfox');
    }

    public function getAuthorization(){
        $response = Http::post($this->url, [
                'grant_type' => 'client_credentials',
                'client_id' => $this->key,
                'client_secret' => $this->secret,
        ])->json();

        return $response;
    }

   public function createUser($inputData){

       $token =  self::getAuthorization();

        $request = Http::withHeaders([
            'X-Auth-API-Key' => $this->key,
            'X-Auth-Subdomain' => $this->subdomain,
            'Content-Type' => 'application/json',
        ])->post('https://api.thinkific.com/api/public/v1/users', [
            'first_name' => $inputData["first_name"],
            'last_name' => $inputData["last_name"],
            'email' => $inputData["email"],
            'password' => $inputData["password"]
        ]);

        return $request->json();
    }*/
    //
}
