<?php

namespace App\Admin\Controllers;

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\AuthController as BaseAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Exception;

class AuthController extends BaseAuthController
{


    /**
     * Handle a login request.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function postLogin(Request $request)
    {

        $this->loginValidator($request->all())->validate();
        $credentials = $request->only([$this->username(), 'password']);
        //$credentials = $request->only([$this->username()]);
        $remember = $request->get('remember', false);

        if ($this->guard()->attempt($credentials, $remember)) {
            return $this->sendLoginResponse($request);
        }

        return back()->withInput()->withErrors([
            $this->username() => $this->getFailedLoginMessage(),
        ]);
    }

    /**
     * Get a validator for an incoming login request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function loginValidator(array $data)
    {
        //dd($data);
        $response = $this->checkHISLoginApi($data);
        // dd($response);
        return Validator::make($data, [
            $this->username() => 'required',
            'password' => 'required',
        ]);
    }

    public function checkHISLoginApi(array $data)
    {
        try {
            $request_method = 'post';
            $param = ['userName' => strtolower($data['username']), 'password' => $data['password']];
            $url = config('application.his_login_api_endpoint', 'http://praava.icthealth.com/live/portal/createUserCredential');
            $response = makeHttpRequest($request_method, $param, $url, [], 'form_params');
            Log::info(__FILE__ . '|| Line ' . __LINE__ . ' ||' . json_encode($response));
            if (array_key_exists('status', $response)) {
                if ($response['status'] == 'success') {
                    try {
                        $userModel = config('admin.database.users_model');
                        $user = $userModel::where('username', '=', $response['userName'])->first();
                        if ($user) {
                            $user->password = bcrypt($data['password']);
                        } else {
                            $user = new $userModel();
                            $user->username = $response['userName'];
                            $user->password = bcrypt($data['password']);
                            $user->name = $response['userFullName'];
                            $user->save();
                            $user->roles()->attach(config('roll_id', 200));
                        }
                        $user->name = $response['userFullName'];
                        $user->save();
                    } catch (Exception $ex) {
                        Log::error(__FILE__ . '|| Line ' . __LINE__ . ' ||' . $ex->getMessage() . ' || ' . $ex->getCode());
                    }
                } else {
                    Log::error(__FILE__ . '|| Line ' . __LINE__ . ' || user not found || ' . json_encode($response));
                }
            } else {
                Log::error(__FILE__ . '|| Line ' . __LINE__ . ' || user not found || ' . json_encode($response));
            }
        } catch (Exception $ex) {
            Log::error(__FILE__ . '|| Line ' . __LINE__ . ' ||' . $ex->getMessage() . ' || ' . $ex->getCode());
        }
    }
}
