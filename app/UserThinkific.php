<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserThinkific
{
    public function loadUsers(){

        $response = Http::withHeaders([
            'X-Auth-API-Key' => 'beba502b9c8590be264d106b18f49e6e',
            'X-Auth-Subdomain' => 'dylan-s-school-2159',
            'Content-Type' => 'application/json'
        ])->get('https://api.thinkific.com/api/public/v1/users')->json();

        return $response;
    }

    public function createUser($inputData){
        $request = Http::withHeaders([
            'X-Auth-API-Key' => 'beba502b9c8590be264d106b18f49e6e',
            'X-Auth-Subdomain' => 'dylan-s-school-2159',
            'Content-Type' => 'application/json',
        ])->post('https://api.thinkific.com/api/public/v1/users', [
            'first_name' => $inputData["first_name"],
            'last_name' => $inputData["last_name"],
            'email' => $inputData["email"],
            'password' => $inputData["password"]
        ]);

        return $request->json();
    }

    public function updateUser(Request $inputData, $userid){
        $data = $inputData->all();

        $request = Http::withHeaders([
            'X-Auth-API-Key' => 'beba502b9c8590be264d106b18f49e6e',
            'X-Auth-Subdomain' => 'dylan-s-school-2159',
            'Content-Type' => 'application/json',
        ])->put('https://api.thinkific.com/api/public/v1/users/'. $userid, [
            'first_name' => $data["first_name"],
            'last_name' => $data["last_name"],
            'email' => $data["email"],
            'company' => $data["company"]
        ]);

        return $request;
    }

    public function deleteUser($userid){

        $request = Http::withHeaders([
            'X-Auth-API-Key' => 'beba502b9c8590be264d106b18f49e6e',
            'X-Auth-Subdomain' => 'dylan-s-school-2159',
            'Content-Type' => 'application/json',
        ])->delete('https://api.thinkific.com/api/public/v1/users/'. $userid);

        return $request;
    }

    // Create JWT single sign on Thikinfic
    public function singleSignOn(Request $request){

        $header = json_encode(['typ' => 'JWT', 'alg' => ' ']);

        // Create token payload as a JSON string
        $payload = json_encode([
            'first_name' => $request->first_name,
            "last_name" => $request->last_name,
            "email" => "dylan.lievano.cuevas@gmail.com",
            "iat"=> time(),
            "external_id" => "thinkific@thinkific.com",
            "bio" => "Mostly harmless",
            "company" => "Thinkific",
            "timezone" => "America/Los_Angeles"
        ]);

        // Encode Header to Base64Url String
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        // Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'clublia!', true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        $baseUrl = "https://dylan-s-school-2159.thinkific.com/api/sso/v2/sso/jwt?jwt=";
        $returnTo = urlencode("https://dylan-s-school-2159.thinkific.com");
        $errorUrl = urlencode("https://dylan-s-school-2159.thinkific.com");
        $url = $baseUrl . $jwt . "&return_to=" . $returnTo . "&error_url=" . $errorUrl;

        echo $url;
    }
}
