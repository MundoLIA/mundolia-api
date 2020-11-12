<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserThinkific
{
    protected $key;
    protected $subdomain;

    function __construct() {
        $this->key = Config::get('app.thinkific');
        $this->subdomain = Config::get('app.subdomain_thinkific');
    }

    public function loadUsers(){
        $response = Http::withHeaders([
            'X-Auth-API-Key' => $this->key,
            'X-Auth-Subdomain' => $this->subdomain,
            'Content-Type' => 'application/json'
        ])->get('https://api.thinkific.com/api/public/v1/users')->json();

        return $response;
    }

    public function createUser($inputData){

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
    }

    public function updateUser($inputData, $userid){

        $request = Http::withHeaders([
            'X-Auth-API-Key' => $this->key,
            'X-Auth-Subdomain' => $this->subdomain,
            'Content-Type' => 'application/json',
        ])->put('https://api.thinkific.com/api/public/v1/users/'. $userid, [
            'first_name' => $inputData['name'],
            'last_name' => $inputData["last_name"],
            'email' => $inputData["email"],
        ]);

        return $request;
    }

    public function deleteUserSchooling($userid){

        $request = Http::withHeaders([
            'X-Auth-API-Key' => $this->key,
            'X-Auth-Subdomain' => $this->subdomain,
            'Content-Type' => 'application/json',
        ])->delete('https://api.thinkific.com/api/public/v1/users/'. $userid);

        return $request;
    }

    // Create JWT single sign on Thikinfic
    public function singleSignOn($user){

        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

        // Create token payload as a JSON string
        $payload = json_encode([
            'first_name' =>$user->first_name,
            "last_name" => $user->last_name,
            "email" => $user->email,
            "iat"=> time(),
            "timezone" => "America/Los_Angeles"
        ]);

        // Encode Header to Base64Url String
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        // Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        // Create Signature Hash
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->key, true);

        // Encode Signature to Base64Url String
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        // Create JWT
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        $baseUrl = "https://dylan-s-school-2159.thinkific.com/api/sso/v2/sso/jwt?jwt=";
        $returnTo = urlencode("https://dylan-s-school-2159.thinkific.com");
        $errorUrl = urlencode("https://dylan-s-school-2159.thinkific.com");
        $url = $baseUrl . $jwt . "&return_to=" . $returnTo . "&error_url=" . $errorUrl;

        echo $url;
    }

    //Group request list, creation, update, delete.
    public function listSchool(){

        $request = Http::withHeaders([
            'X-Auth-API-Key' => $this->key,
            'X-Auth-Subdomain' => $this->subdomain,
            'Content-Type' => 'application/json',
        ])->get('https://api.thinkific.com/api/public/v1/groups/');

        return $request;
    }

    public function createSchool($data){

        $request = Http::withHeaders([
            'X-Auth-API-Key' => $this->key,
            'X-Auth-Subdomain' => $this->subdomain,
            'Content-Type' => 'application/json',
        ])->post('https://api.thinkific.com/api/public/v1/groups/', [
            'name' => $data["name"],
        ]);

        return $request;
    }

    public function enrollmentStudent($bundleId, $data){

        $activeDay = Carbon::now();
        $expiryDate = '';

        var_dump($activeDay);
        die();
        $request = Http::withHeaders([
            'X-Auth-API-Key' => $this->key,
            'X-Auth-Subdomain' => $this->subdomain,
            'Content-Type' => 'application/json',
        ])->post('https://api.thinkific.com/api/public/v1/bundles/'. $bundleId .'/enrollments', [
            'user_id   ' => $data["id"],
            'activated_at' => $activeDay,
            'expiry_date' => $expiryDate
        ]);

        return $request;
    }


}
