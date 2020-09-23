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

    function __construct()
    {
        $this->key = Config::get('app.phpfox');
        $this->secret = Config::get('app.secret_phpfox');
        $this->url = Config::get('app.url_phpfox');
    }

    public function getAuthorization()
    {
        include 'token_phpfox.php';
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
                file_put_contents('token_phpfox.php', $var);
                return $token['access_token'];
            } else {
                return false;
            }
        }
        return $token_phpfox;
    }

    public function createUser($inputData)
    {

        $token = self::getAuthorization();

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

    public function singleSignOn($user)
    {
        $token = self::getAuthorization();

        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

        // Create token payload as a JSON string
        $payload =  self::encrypt($user->email);

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
