<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Str;

trait OpenaiApi
{

    public function createThreadAndRun($message)
    {

        $curl = curl_init();

        $data = [
            "assistant_id" => env('OPENAI_ASSISTANT_ID'),
            "thread" => [
                "messages" => [
                    [
                        "role" => "user",
                        "content" => $message
                    ]
                ]
            ]
        ];

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://api.openai.com/v1/threads/runs',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'OpenAI-Beta: assistants=v2',
                    'Authorization: Bearer ' . env('OPENAI_API_KEY'),
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        $result = json_decode($response, true);

        $thread_id = $result['thread_id'];
        $run_id = $result['id'];

        return $this->retrieveRun($thread_id, $run_id);

    }

    private function retrieveRun($thread_id, $run_id)
    {
        $status = '';
        $maxAttempts = 10; // Número máximo de tentativas para evitar loops infinitos
        $attempts = 0;

        do {
            $curl = curl_init();

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $thread_id . '/runs/' . $run_id,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'OpenAI-Beta: assistants=v2',
                        'Authorization: Bearer ' . env('OPENAI_API_KEY'),
                    ),
                )
            );

            $response = curl_exec($curl);

            curl_close($curl);

            $result = json_decode($response, true);
            $status = $result['status'];

            $attempts++;
            sleep(1); // Espera 1 segundo antes da próxima tentativa (ajuste conforme necessário)
        } while ($status !== 'completed' && $attempts < $maxAttempts);

        return $this->listMessages($thread_id);

    }

    private function listMessages($thread_id)
    {

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $thread_id . '/messages',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'OpenAI-Beta: assistants=v2',
                    'Authorization: Bearer ' . env('OPENAI_API_KEY'),
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);

    }

    public function createMessage($thread_id, $message)
    {

        $data = [
            "role" => "user",
            "content" => $message
        ];

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $thread_id . '/messages',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'OpenAI-Beta: assistants=v2',
                    'Authorization: Bearer ' . env('OPENAI_API_KEY'),
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        return $this->createRun($thread_id);

    }

    private function createRun($thread_id)
    {

        $data = [
            "assistant_id" => env('OPENAI_ASSISTANT_ID')
        ];

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://api.openai.com/v1/threads/' . $thread_id . '/runs',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'OpenAI-Beta: assistants=v2',
                    'Authorization: Bearer ' . env('OPENAI_API_KEY'),
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        $run = json_decode($response, true);
        $run_id = $run['id'];

        return $this->retrieveRun($thread_id, $run_id);

    }

}
