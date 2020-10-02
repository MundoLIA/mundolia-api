<?php

namespace App;

use App\Jobs\DeleteGenericUserJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncUser
{
    protected $key;
    protected $subdomain;

    function __construct() {
        $this->key = Config::get('app.thinkific');
        $this->subdomain = Config::get('app.subdomain_thinkific');
    }

    public function transferUsers(){

        $inactive = 0;

        $results = User::where([
            ['role_id', '>', 1],
            ['active_thinkific' ,'=', $inactive]
        ])->orWhere([
            ['role_id', '>', 1],
            ['active_phpfox' ,'=', $inactive]
        ])->get();

        $i = 0;

        if($results->isEmpty()){
           return ['message' => 'No hay usuarios por sincronizar'];
        }
        else{
            foreach ($results as $obj) {
                $syncUser = $obj;

                $request = Http::withHeaders([
                    'X-Auth-API-Key' => $this->key,
                    'X-Auth-Subdomain' => $this->subdomain,
                    'Content-Type' => 'application/json',
                ])->post('https://api.thinkific.com/api/public/v1/users', [
                    'first_name' =>$syncUser->name,
                    'last_name' => $syncUser->last_name,
                    'email' => $syncUser->email
                ]);

                $dataFox = ([
                    'email' => $syncUser->email,
                    'full_name' => $syncUser->name . ' ' .$syncUser->last_name,
                    "user_name" => $syncUser->username,
                    "password" => '1234567'
                ]);

                $requestFox =  new UserPhpFox();
                $requestFox = $requestFox->createUser($dataFox);

                $inputuser =  $request->json();
                $inputuserFox =  $requestFox;

                if(array_key_exists("id", $inputuser) && !empty($inputuserFox['data']) ){
                    var_dump('Entro 1');

                    $affected = User::find($syncUser->id);
                    $affected->active_thinkific = $inputuser['id'];
                    $affected->active_phpfox = $inputuserFox['data']['user_id'];
                    $affected->save();

                    $count[++$i]= (array) ["schooling" => $inputuser,"comunidad" => $inputuserFox,"id" => $syncUser->id];

                }
                if(array_key_exists("id", $inputuser) && empty($inputuserFox['data'])) {
                    var_dump('Entro 2');

                    $affected = User::find($syncUser->id);
                    $affected->active_thinkific = $inputuser['id'];
                    $affected->save();

                    $count[++$i] = (array)["schooling" => $inputuser, "comunidad" => $inputuserFox, "id" => $syncUser->id];
                }
                if(!array_key_exists("id", $inputuser) && !empty($inputuserFox['data'])){
                    var_dump('Entro 3');

                    $affected = User::find($syncUser->id);
                    $affected->active_phpfox = $inputuserFox['data']['user_id'];
                    $affected->save();

                    $count[++$i]= (array) ["schooling" => $inputuser,"comunidad" => $inputuserFox,"id" => $syncUser->id];
                }
                else{
                    if(!empty($inputuser['errors']) && $inputuserFox["status"] === 'failed'){
                        $count[++$i]= (array) ["schooling" => $inputuser,"comunidad" => $inputuserFox,"id" => $syncUser->id] ;
                    }
                }

            }
        }
        Log::info(json_encode(["usuarios" => $count]));
        return (["usuarios" => $count]);
    }


    public function destroyUser($uuid)
    {
        $user = User::where('uuid', 'like', '%' . $uuid . '%')->firstOrFail();

        $deleteSchooling = DeleteGenericUserJob::dispatch($user->active_thikific, $user->active_phpfox);

        $userLIA = UserLIA::find($user->AppUserId);
        $userLIA->delete();
        $user->delete();

        return response()([
            $user,
            $deleteSchooling,
            "message" => "El usuario ha sido eliminado existosamente",
        ], 200);

    }


}
