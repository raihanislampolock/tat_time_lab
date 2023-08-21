<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class MasterServiceList
{
    public function getPhleboServiceMasterFromApi($serviceCode="service")
    {
        $retrun_response = "data not found";
        $request_method = 'get';
        $param = ['code' => $serviceCode,'token'=>config('application.ept_api_token')];
        $url = config('application.base_url_ept_api') . config('application.service_master_ept_api');
        $response = makeHttpRequest($request_method, $param, $url, ['token' => config('application.ept_api_token')], 'form_params');
        if (array_key_exists('success', $response)) {
            if ($response['success'] == 'true' || $response['success'] == true) {
                try {
                    $retrun_response= $response;
                } catch (Exception $ex) {
                    $retrun_response = $response;
                    Log::error(__FILE__ . '|| Line ' . __LINE__ . ' ||' . $ex->getMessage() . ' || ' . $ex->getCode());
                }
            } else {
                $retrun_response = $response;
                Log::error(__FILE__ . '|| Line ' . __LINE__ . ' || doctor profile not found || ' . json_encode($response));
            }
        } else {
            $retrun_response = $response;
            Log::error(__FILE__ . '|| Line ' . __LINE__ . ' || user not found || ' . json_encode($response));
        }

        return $retrun_response;
    }
    public function getPhleboServiceMasterFromApi2(){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://ept.praavahealth.com/API/PatientPortal/ServiceMasterApp?token=03e62234b7238ca3eab782f30b9dfa94&code=service",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
//            CURLOPT_HTTPHEADER => array(
//                'auth: 811d5252b43ede3da0686aa828ff2e12'
//            ),
        ));
        try {
            $response = curl_exec($curl);
            return $response;
        }catch (\Exception $exception){
            Log::info(json_encode($exception));
        }
    }
 }