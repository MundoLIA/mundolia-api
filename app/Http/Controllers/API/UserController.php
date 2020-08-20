<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Exception;
use Validator;
class UserController extends Controller
{
    public $successStatus = 200;
    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){
        try {
            if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
                $user = Auth::user();
                $success['access_token'] = $user->createToken('MyApp')->accessToken;

                $dataUser['displayName'] = $user->name;
                $dataUser['email'] = $user->email;
                $dataUser['photoURL'] = $user->email;
                $dataUser['role'] = ['user'];
                $dataUser['uuid'] = $user->id;

                $success['user'] = (['data' =>$dataUser]);


                return response()->json($success, $this->successStatus);
            } else {
                $error["code"] = 'INVALID_PASSWORD';
                $error["message"] = "The password is invalid or the user does not have a password.";

                $errors["message"] = "The password is invalid or the user does not have a password.";
                $errors["domain"] = "global";
                $errors["reason"] = "invalid";

                $error["errors"] =[$errors];

                return response()->json(['error' => $error], 400);
            }
        }catch (Exception $e){
            $error["code"] = 'INVALID_PASSWORD';
            $error["message"] = "The password is invalid or the user does not have a password.";

            $errors["message"] = "The password is invalid or the user does not have a password.";
            $errors["domain"] = "global";
            $errors["reason"] = "invalid";

            $error["errors"] =[$errors];

            return response()->json(['error' => $error], 500);
        }
    }
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            $error["code"] = 'INVALID_DATA';
            $error["message"] = "The field is invalid or the user does not have a password.";

            $errors["message"] = ['error'=>$validator->errors()];
            $errors["domain"] = "global";
            $errors["reason"] = "invalid";

            $error["errors"] =[$errors];

            return response()->json(['error' => $error], 401);
        }

        try{
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);

            $success['access_token'] = $user->createToken('MyApp')->accessToken;

            $dataUser['displayName'] = $user->name;
            $dataUser['email'] = $user->email;
            $dataUser['photoURL'] = $user->email;
            $dataUser['role'] = ['user'];
            $dataUser['uuid'] = $user->id;

            $success['user'] = (['data' =>$dataUser]);


            return response()->json($success, $this->successStatus);

        }catch (Exception $e){
            $error["code"] = 'INVALID_DATA';
            $error["message"] = "The field is invalid or the user does not have a password.";

            $errors["message"] = "The fields is invalid or the user does not have a password.";
            $errors["domain"] = "global";
            $errors["reason"] = "invalid";

            $error["errors"] =[$errors];

            return response()->json(['error' => $error], 500);
        }

    }
    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function accessToken()
    {
        try{
            $user = Auth::user();

            $success['access_token'] = $user->createToken('MyApp')->accessToken;

            $dataUser['displayName'] = $user->name;
            $dataUser['email'] = $user->email;
            $dataUser['photoURL'] = $user->email;
            $dataUser['role'] = ['user'];
            $dataUser['uuid'] = $user->id;

            $success['user'] = (['data' =>$dataUser]);
            return response()->json($success, $this->successStatus);
        }catch (Exception $e){
            $error["code"] = 'INVALID_TOKEN';
            $error["message"] = "The token is invalid.";

            $errors["message"] = "The token is invalid.";
            $errors["domain"] = "global";
            $errors["reason"] = "invalid";

            $error["errors"] =[$errors];

            return response()->json(['error' => $error], 500);

        }

    }
}
