<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;

trait RentAndPassTrait
{
    public function sendKeycode($keypass, $rented_slot_id, $start_date_time, $end_date_time)
    {
        $login = $this->obtainLoginToken();

        $token = $login['access_token'];

        return $this->generateCustomCodeAndSendIt($token, $keypass, $rented_slot_id, $start_date_time, $end_date_time);
    }

    private function obtainLoginToken()
    {

        $clientId = env('RENT_AND_PASS_CLIENT_ID');
        $clientSecret = env('RENT_AND_PASS_CLIENT_SECRET');
        $username = env('RENT_AND_PASS_USERNAME');
        $password = env('RENT_AND_PASS_PASSWORD');

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://api.rentandpass.com/api/signin/token?clientId=' . $clientId . '&clientSecret=' . $clientSecret . '&username=' . $username . '&password=' . $password . '#!/Signin/Signin_token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json'
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);

    }

    private function generateCustomCodeAndSendIt($token, $keypass, $rented_slot_id, $start_date_time, $end_date_time)
    {

        $clientId = env('RENT_AND_PASS_CLIENT_ID');
        $lockid = env('RENT_AND_PASS_LOCK_ID');

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://api.rentandpass.com/api/password?clientId=' . $clientId . '&token=' . $token . '&ID=' . $lockid . '&password=' . $keypass . '&name=Gymspot-' . $rented_slot_id . '&startDate=' . $start_date_time . '000&endDate=' . $end_date_time . '000&type=2&reference=gymspot-1',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded',
                    'Accept: application/json'
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);

    }
}
