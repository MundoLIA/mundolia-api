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
        $response = Http::post($this->url .'/restful_api/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->key,
                'client_secret' => $this->secret,
        ])->json();

        return $response;
    }

    public function createUser($data){

        $token = self::getAuthorization();

        $response = Http::withToken($token['access_token'])->asForm()->post($this->url . '/restful_api/user', [
            'val[email]' => $data['email'],
            'val[full_name]' => $data['full_name'],
            'val[user_name]' => $data['user_name'],
            'val[password]' => '1234567'
        ]);

        return $response->json();
    }

    public function deleteUserCommunity($data){

        $token = self::getAuthorization();

        $response = Http::withToken($token['access_token'])->asForm()->delete($this->url . '/restful_api/user/', [
            'val[id]' => $data['id'],
        ]);

        return $response->json();
    }

}
