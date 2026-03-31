<?php

namespace App\Http\Controllers\Traits;

use App\Models\RentedSlot;
use App\Support\LockDateTime;
use Illuminate\Support\Facades\Log;

trait RentAndPassTrait
{
    public function syncRentedSlotKeycode(RentedSlot $rentedSlot): ?array
    {
        $login = $this->obtainLoginToken();

        if (!is_array($login) || empty($login['access_token'])) {
            Log::error('RentAndPass login failed.', [
                'rented_slot_id' => $rentedSlot->id,
                'response' => $login,
            ]);
            return null;
        }

        $token = $login['access_token'];

        $this->deleteExistingRentedSlotPasswords($token, $rentedSlot);

        $startDateTime = LockDateTime::toLockUnixTimestamp($rentedSlot->start_date_time);
        $endDateTime = LockDateTime::toLockUnixTimestamp($rentedSlot->end_date_time);

        return $this->generateCustomCodeAndSendIt(
            $token,
            $rentedSlot->keypass,
            $rentedSlot->id,
            $startDateTime,
            $endDateTime
        );
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

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.rentandpass.com/api/signin/token?clientId=' . $clientId . '&clientSecret=' . $clientSecret . '&username=' . $username . '&password=' . $password . '#!/Signin/Signin_token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
            ],
        ]);

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

    private function deleteExistingRentedSlotPasswords(string $token, RentedSlot $rentedSlot): void
    {
        $passwords = $this->getAllPasswords($token);
        if (empty($passwords)) {
            return;
        }

        $targetName = $this->passwordNameForRentedSlot($rentedSlot->id);

        foreach ($passwords as $password) {
            if (($password['keyboardPwdName'] ?? null) !== $targetName) {
                continue;
            }

            if (!empty($password['keyboardPwdId'])) {
                $this->deletePassword($token, (string) $password['keyboardPwdId']);
            }
        }
    }

    private function getAllPasswords(string $token): array
    {
        $clientId = env('RENT_AND_PASS_CLIENT_ID');
        $lockid = env('RENT_AND_PASS_LOCK_ID');

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.rentandpass.com/api/lock/passwords?clientId=' . $clientId . '&token=' . $token . '&ID=' . $lockid,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($curlError) {
            Log::error('RentAndPass password list cURL error.', [
                'error' => $curlError,
            ]);
            return [];
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded) || !isset($decoded['list']) || !is_array($decoded['list'])) {
            Log::error('RentAndPass password list returned invalid JSON.', [
                'response' => $response,
            ]);
            return [];
        }

        return $decoded['list'];
    }

    private function deletePassword(string $token, string $passId): ?array
    {
        $clientId = env('RENT_AND_PASS_CLIENT_ID');
        $lockid = env('RENT_AND_PASS_LOCK_ID');

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.rentandpass.com/api/password',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_POSTFIELDS => http_build_query([
                'clientId' => $clientId,
                'token' => $token,
                'ID' => $lockid,
                'passID' => $passId,
                'type' => 2,
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($curlError) {
            Log::error('RentAndPass delete password cURL error.', [
                'pass_id' => $passId,
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

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.rentandpass.com/api/password?clientId=' . $clientId . '&token=' . $token . '&ID=' . $lockid . '&password=' . $keypass . '&name=' . $this->passwordNameForRentedSlot($rented_slot_id) . '&startDate=' . $start_date_time . '000&endDate=' . $end_date_time . '000&type=2&reference=' . $this->referenceForRentedSlot($rented_slot_id),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($curlError) {
            Log::error('RentAndPass keycode sync cURL error.', [
                'rented_slot_id' => $rented_slot_id,
                'error' => $curlError,
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
            ]);
            return null;
        }

        $decoded = json_decode($response, true);

        if (!is_array($decoded)) {
            Log::error('RentAndPass keycode sync returned invalid JSON.', [
                'rented_slot_id' => $rented_slot_id,
                'response' => $response,
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
            ]);
            return null;
        }

        Log::info('RentAndPass keycode synchronized.', [
            'rented_slot_id' => $rented_slot_id,
            'start_date_time' => $start_date_time,
            'end_date_time' => $end_date_time,
            'response' => $decoded,
        ]);

        return $decoded;
    }

    private function passwordNameForRentedSlot($rentedSlotId): string
    {
        return 'Gymspot-' . $rentedSlotId;
    }

    private function referenceForRentedSlot($rentedSlotId): string
    {
        return 'gymspot-' . $rentedSlotId;
    }
}
