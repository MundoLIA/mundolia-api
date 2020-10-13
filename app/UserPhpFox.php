<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class UserPhpFox
{
    protected $key;
    protected $secret;
    protected $url;
    protected $username;
    protected $password;

    function __construct() {
        $this->key = Config::get('app.phpfox');
        $this->secret = Config::get('app.secret_phpfox');
        $this->url = Config::get('app.url_phpfox');
        $this->username = Config::get('app.username_comunidad');
        $this->password = Config::get('app.pass_comunidad');
    }

    public function getCredentialToken(){
        $response = Http::post($this->url .'/restful_api/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->key,
            'client_secret' => $this->secret,
        ])->json();

        return $response;
    }

    public function getAuthorization(){
        $response = Http::post($this->url .'/restful_api/token', [
                'grant_type' => 'password',
                'username' => $this->username,
                'password' => $this->password,
                'client_id' => $this->key,
                'client_secret' => $this->secret,
        ])->json();

        return $response;
    }


    public function createUser($data){

        $token = self::getCredentialToken();

        $response = Http::withToken($token['access_token'])->asForm()->post($this->url . '/restful_api/user', [
            'val[email]' => $data['email'],
            'val[full_name]' => $data['full_name'],
            'val[user_name]' => $data['user_name'],
            'val[password]' => '1234567'
        ]);

        return $response->json();
    }

    public function deleteUserCommunity($userId){

        $token = self::getAuthorization();

        $response = Http::withToken($token['access_token'])->delete($this->url . '/restful_api/user/' . $userId);

        return $response->json();
    }

    public function updateUser($inputData,$userId){

        $token = self::getAuthorization();

        $response = Http::withToken($token['access_token'])->asForm()->put($this->url . '/restful_api/user/' . $userId, [
            'val[email]' => $inputData['email'],
            'val[full_name]' => $inputData['name'].' '.$inputData['last_name'],
            'val[password]' => $inputData['name'],
        ]);

        return $response->json();
    }
}
