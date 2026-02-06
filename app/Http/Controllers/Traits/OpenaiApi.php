<?php

namespace App\Http\Controllers\Traits;

trait OpenaiApi
{
    private const OPENAI_BETA_HEADER = 'OpenAI-Beta: assistants=v2';
    private const OPENAI_TIMEOUT_SECONDS = 30;

    public function createThreadAndRun($message)
    {
        $data = [
            "assistant_id" => env('OPENAI_ASSISTANT_ID'),
            "thread" => [
                "messages" => [
                    [
                        "role" => "user",
                        "content" => $message,
                    ],
                ],
            ],
        ];

        $result = $this->openaiRequest(
            'POST',
            'https://api.openai.com/v1/threads/runs',
            $data,
            ['Content-Type: application/json'],
        );

        if (!$result['ok']) {
            return $this->openaiErrorPayload($result);
        }

        $body = $result['body'];
        $thread_id = $body['thread_id'] ?? null;
        $run_id = $body['id'] ?? null;
        if (!$thread_id || !$run_id) {
            return $this->openaiErrorPayload([
                'ok' => false,
                'http_code' => $result['http_code'],
                'body' => $body,
                'curl_error' => $result['curl_error'],
                'error' => [
                    'code' => 'unexpected_response',
                    'message' => 'OpenAI response did not include thread_id/run id.',
                ],
            ]);
        }

        return $this->retrieveRun($thread_id, $run_id);
    }

    private function retrieveRun($thread_id, $run_id)
    {
        $status = '';
        $maxAttempts = 30; // Número máximo de tentativas para evitar loops infinitos
        $attempts = 0;
        $lastError = null;

        do {
            $result = $this->openaiRequest(
                'GET',
                'https://api.openai.com/v1/threads/' . $thread_id . '/runs/' . $run_id,
            );

            if (!$result['ok']) {
                $payload = $this->openaiErrorPayload($result);
                $payload['_gymspot'] = ($payload['_gymspot'] ?? []) + [
                    'thread_id' => $thread_id,
                    'run_id' => $run_id,
                    'run_status' => $status ?: null,
                ];
                return $payload;
            }

            $body = $result['body'];
            $status = $body['status'] ?? '';
            $lastError = $body['last_error'] ?? null;

            $attempts++;
            sleep(1); // Espera 1 segundo antes da próxima tentativa
        } while (!in_array($status, ['completed', 'failed', 'cancelled', 'expired', 'requires_action'], true) && $attempts < $maxAttempts);

        $messages = $this->listMessages($thread_id);
        if (is_array($messages)) {
            $messages['_gymspot'] = [
                'thread_id' => $thread_id,
                'run_id' => $run_id,
                'run_status' => $status ?: null,
                'run_last_error' => $lastError,
                'poll_attempts' => $attempts,
            ];
        }

        return $messages;
    }

    private function listMessages($thread_id)
    {
        $result = $this->openaiRequest(
            'GET',
            'https://api.openai.com/v1/threads/' . $thread_id . '/messages',
            null,
            ['Content-Type: application/json'],
        );

        if (!$result['ok']) {
            $payload = $this->openaiErrorPayload($result);
            $payload['_gymspot'] = ($payload['_gymspot'] ?? []) + [
                'thread_id' => $thread_id,
            ];
            return $payload;
        }

        return $result['body'];
    }

    public function createMessage($thread_id, $message)
    {
        $data = [
            "role" => "user",
            "content" => $message,
        ];

        $result = $this->openaiRequest(
            'POST',
            'https://api.openai.com/v1/threads/' . $thread_id . '/messages',
            $data,
            ['Content-Type: application/json'],
        );

        if (!$result['ok']) {
            $payload = $this->openaiErrorPayload($result);
            $payload['_gymspot'] = ($payload['_gymspot'] ?? []) + [
                'thread_id' => $thread_id,
            ];
            return $payload;
        }

        return $this->createRun($thread_id);
    }

    private function createRun($thread_id)
    {
        $data = [
            "assistant_id" => env('OPENAI_ASSISTANT_ID'),
        ];

        $result = $this->openaiRequest(
            'POST',
            'https://api.openai.com/v1/threads/' . $thread_id . '/runs',
            $data,
            ['Content-Type: application/json'],
        );

        if (!$result['ok']) {
            $payload = $this->openaiErrorPayload($result);
            $payload['_gymspot'] = ($payload['_gymspot'] ?? []) + [
                'thread_id' => $thread_id,
            ];
            return $payload;
        }

        $run = $result['body'];
        $run_id = $run['id'] ?? null;
        if (!$run_id) {
            $payload = $this->openaiErrorPayload([
                'ok' => false,
                'http_code' => $result['http_code'],
                'body' => $run,
                'curl_error' => $result['curl_error'],
                'error' => [
                    'code' => 'unexpected_response',
                    'message' => 'OpenAI response did not include run id.',
                ],
            ]);
            $payload['_gymspot'] = ($payload['_gymspot'] ?? []) + [
                'thread_id' => $thread_id,
            ];
            return $payload;
        }

        return $this->retrieveRun($thread_id, $run_id);
    }

    private function openaiRequest(string $method, string $url, ?array $payload = null, array $extraHeaders = []): array
    {
        $curl = curl_init();

        $headers = array_merge($extraHeaders, [
            self::OPENAI_BETA_HEADER,
            'Authorization: Bearer ' . env('OPENAI_API_KEY'),
        ]);

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => self::OPENAI_TIMEOUT_SECONDS,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ];

        if (!is_null($payload)) {
            $options[CURLOPT_POSTFIELDS] = json_encode($payload);
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $curlError = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $decoded = null;
        if (is_string($response) && $response !== '') {
            $decoded = json_decode($response, true);
        }

        $apiError = null;
        if (is_array($decoded) && isset($decoded['error']) && is_array($decoded['error'])) {
            $apiError = $decoded['error'];
        }

        $ok = $httpCode >= 200 && $httpCode < 300 && !$apiError && ($curlError === '' || $curlError === null);

        return [
            'ok' => $ok,
            'http_code' => $httpCode,
            'body' => is_array($decoded) ? $decoded : ['raw' => $response],
            'error' => $apiError,
            'curl_error' => $curlError ?: null,
        ];
    }

    private function openaiErrorPayload(array $result): array
    {
        $error = $result['error'] ?? null;
        if (!$error && !empty($result['curl_error'])) {
            $error = [
                'code' => 'curl_error',
                'message' => $result['curl_error'],
            ];
        }
        if (!$error) {
            $error = [
                'code' => 'unknown_error',
                'message' => 'OpenAI request failed.',
            ];
        }

        return [
            'object' => 'error',
            'error' => $error,
            '_gymspot' => [
                'openai_http_status' => $result['http_code'] ?? null,
            ],
        ];
    }
}

