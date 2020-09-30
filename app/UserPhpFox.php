<?php

namespace App;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class UserPhpFox
{
    protected $key;
    protected $secret;
    protected $url;
    protected $token;

    function __construct() {
        $this->key = Config::get('app.phpfox');
        $this->secret = Config::get('app.secret_phpfox');
        $this->url = Config::get('app.url_phpfox');
    }

    public function getAuthorization()
    {
        include public_path('token_phpfox.php');
        $validateToken = Http::withToken($token_phpfox)->get($this->url . "/restful_api/user");
        if (!$validateToken->ok()) {
            $response = Http::post($this->url . "/restful_api/token", [
                'grant_type' => 'client_credentials',
                'client_id' => $this->key,
                'client_secret' => $this->secret,
            ]);
            if ($response->ok()) {
                $token = json_decode($response, true);
                $val = $token['access_token'];
                $var_str = var_export($val, true);
                $var = "<?php\n\n\$token_phpfox = $var_str;\n\n?>";
                file_put_contents(public_path('token_phpfox.php'), $var);
                return $token['access_token'];
            } else {
                return false;
            }
        }
        return $token_phpfox;
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

    public function singleSignOn($user)
    {
        $token = self::getAuthorization();

        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

        // Create token payload as a JSON string
        $payload =  self::encrypt($user->active_phpfox);

        // Encode Header to Base64Url String
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        // Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        // Create Signature Hash
        $tokenEncrypt = self::encrypt($token);

        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($tokenEncrypt));


        // Create JWT
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        $baseUrl =  $this->url."/s-s-o/";
        $url = $baseUrl . $jwt;

        echo $url;
    }
    public function encrypt($val){
        $ciphering = "AES-128-CTR";
        $options = 0;
        $encryption_iv = '1987635498325191';
        $encryption_key = "KjiUyhasp";
        $encryption = openssl_encrypt($val, $ciphering, $encryption_key, $options, $encryption_iv);
        return $encryption;
    }
}
