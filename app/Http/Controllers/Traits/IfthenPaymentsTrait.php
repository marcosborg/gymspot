<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;

trait IfthenPaymentsTrait
{
    public function paymentMbway($orderId, $amount, $mobileNumber)
    {
        $mbWayKey = env('MBWAY_KEY');

        $data = [
            'mbWayKey' => $mbWayKey,
            'orderId' => $orderId,
            'amount' => $amount,
            'mobileNumber' => '351#' . $mobileNumber,
            'description' => 'Pagamento GymSpot'
        ];

        $data = json_encode($data);

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://api.ifthenpay.com/spg/payment/mbway',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public function mbwayStatus($requestId)
    {
        $mbWayKey = env('MBWAY_KEY');

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://api.ifthenpay.com/spg/payment/mbway/status?mbWayKey=' . $mbWayKey . '&requestId=' . $requestId,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);
    }

}
