<?php

namespace App\Http\Controllers\Traits;

use App\Models\RentedSlot;
use App\Support\LockDateTime;
use Illuminate\Support\Facades\Log;

trait RentAndPassTrait
{
    public function syncRentedSlotKeycode(RentedSlot $rentedSlot): ?array
    {
        $startDateTime = LockDateTime::toUtcMilliseconds($rentedSlot->start_date_time);
        $endDateTime = LockDateTime::toUtcMilliseconds($rentedSlot->end_date_time);

        return $this->sendKeycode($rentedSlot->keypass, $rentedSlot->id, $startDateTime, $endDateTime);
    }

    public function sendKeycode($keypass, $rented_slot_id, $start_date_time, $end_date_time)
    {
        $login = $this->obtainLoginToken();

        if (!is_array($login) || empty($login['access_token'])) {
            Log::error('RentAndPass login failed.', [
                'rented_slot_id' => $rented_slot_id,
                'response' => $login,
            ]);
            return null;
        }

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
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($curlError) {
            Log::error('RentAndPass login cURL error.', [
                'error' => $curlError,
            ]);
            return null;
        }

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
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($curlError) {
            Log::error('RentAndPass keycode sync cURL error.', [
                'rented_slot_id' => $rented_slot_id,
                'error' => $curlError,
                'start_date_time_ms' => $start_date_time,
                'end_date_time_ms' => $end_date_time,
            ]);
            return null;
        }

        $decoded = json_decode($response, true);

        if (!is_array($decoded)) {
            Log::error('RentAndPass keycode sync returned invalid JSON.', [
                'rented_slot_id' => $rented_slot_id,
                'response' => $response,
                'start_date_time_ms' => $start_date_time,
                'end_date_time_ms' => $end_date_time,
            ]);
            return null;
        }

        Log::info('RentAndPass keycode synchronized.', [
            'rented_slot_id' => $rented_slot_id,
            'start_date_time_ms' => $start_date_time,
            'end_date_time_ms' => $end_date_time,
            'response' => $decoded,
        ]);

        return $decoded;

    }
}
