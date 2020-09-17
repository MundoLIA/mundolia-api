<?php

namespace App\Http\Controllers;

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
