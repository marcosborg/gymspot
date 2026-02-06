<?php

namespace App\Http\Controllers\Traits;

trait OpenaiApi
{
    private const OPENAI_TIMEOUT_SECONDS = 30;

    public function createThreadAndRun($message)
    {
        $instructions = $this->buildGuiaFitnessInstructions(
            (string) env('OPENAI_GUIDA_FITNESS_INSTRUCTIONS', '')
        );
        $conversation = $this->createConversation($instructions);
        if (!$conversation['ok']) {
            return $this->formatErrorAsMessageList(
                null,
                (string) $message,
                $this->openaiErrorPayload($conversation),
            );
        }

        $conversationId = $conversation['body']['id'] ?? null;
        if (!$conversationId) {
            return $this->formatErrorAsMessageList(
                null,
                (string) $message,
                $this->openaiErrorPayload([
                'ok' => false,
                'http_code' => $conversation['http_code'],
                'body' => $conversation['body'],
                'curl_error' => $conversation['curl_error'],
                'error' => [
                    'code' => 'unexpected_response',
                    'message' => 'OpenAI response did not include conversation id.',
                ],
                ]),
            );
        }

        $response = $this->createResponse($conversationId, (string) $message);
        if (!$response['ok']) {
            $payload = $this->openaiErrorPayload($response);
            return $this->formatErrorAsMessageList($conversationId, (string) $message, $payload);
        }

        return $this->formatResponseAsMessageList($conversationId, (string) $message, $response['body']);
    }

    public function createMessage($thread_id, $message)
    {
        $conversationId = (string) $thread_id;
        $response = $this->createResponse($conversationId, (string) $message);
        if (!$response['ok']) {
            $payload = $this->openaiErrorPayload($response);
            return $this->formatErrorAsMessageList($conversationId, (string) $message, $payload);
        }

        return $this->formatResponseAsMessageList($conversationId, (string) $message, $response['body']);
    }

    private function createConversation(string $instructions): array
    {
        $items = [];
        if (trim($instructions) !== '') {
            $items[] = [
                'type' => 'message',
                'role' => 'developer',
                'content' => $instructions,
            ];
        }

        $data = [];
        if (!empty($items)) {
            $data['items'] = $items;
        }

        return $this->openaiRequest(
            'POST',
            'https://api.openai.com/v1/conversations',
            $data,
            ['Content-Type: application/json'],
        );
    }

    private function buildGuiaFitnessInstructions(string $baseInstructions): string
    {
        $baseInstructions = trim($baseInstructions);

        $formatting = trim(implode("\n", [
            'Formato obrigatório das respostas:',
            '- Responde sempre em Português (PT-PT) e em Markdown.',
            '- Usa títulos curtos, bullets e espaçamento para leitura rápida.',
            '- Usa ícones/emoji com moderação (ex.: ✅ ⚡️ 🧠 💪 📌) no início de secções ou bullets importantes.',
            '- Quando deres passos, usa listas numeradas; quando deres opções, usa bullets.',
            '- Se fizer sentido, termina com uma secção curta "📌 Próximos passos" (1–3 bullets).',
            '- Evita texto corrido longo; prefere blocos curtos e objetivos.',
        ]));

        if ($baseInstructions === '') {
            return $formatting;
        }

        return $baseInstructions . "\n\n" . $formatting;
    }

    private function createResponse(string $conversationId, string $message): array
    {
        $model = (string) env('OPENAI_MODEL', 'gpt-4.1-mini');
        $data = [
            'model' => $model,
            'conversation' => $conversationId,
            'input' => $message,
            'store' => true,
        ];

        return $this->openaiRequest(
            'POST',
            'https://api.openai.com/v1/responses',
            $data,
            ['Content-Type: application/json'],
        );
    }

    private function formatResponseAsMessageList(string $threadId, string $userMessage, array $responseBody): array
    {
        $assistantText = $this->extractAssistantTextFromResponse($responseBody);
        $now = time();

        $assistantMsg = [
            'id' => $responseBody['id'] ?? ('msg_assistant_' . $now),
            'object' => 'thread.message',
            'created_at' => $now,
            'assistant_id' => null,
            'thread_id' => $threadId,
            'run_id' => null,
            'role' => 'assistant',
            'content' => [
                [
                    'type' => 'text',
                    'text' => [
                        'value' => $assistantText,
                        'annotations' => [],
                    ],
                ],
            ],
            'attachments' => [],
            'metadata' => [],
        ];

        $userMsg = [
            'id' => 'msg_user_' . $now,
            'object' => 'thread.message',
            'created_at' => $now,
            'assistant_id' => null,
            'thread_id' => $threadId,
            'run_id' => null,
            'role' => 'user',
            'content' => [
                [
                    'type' => 'text',
                    'text' => [
                        'value' => $userMessage,
                        'annotations' => [],
                    ],
                ],
            ],
            'attachments' => [],
            'metadata' => [],
        ];

        return [
            'object' => 'list',
            'data' => [$assistantMsg, $userMsg],
            'first_id' => $assistantMsg['id'],
            'last_id' => $userMsg['id'],
            'has_more' => false,
            '_gymspot' => [
                'thread_id' => $threadId,
                'response_id' => $responseBody['id'] ?? null,
                'response_status' => $responseBody['status'] ?? null,
                'openai_model' => $responseBody['model'] ?? null,
            ],
        ];
    }

    private function formatErrorAsMessageList(?string $threadId, string $userMessage, array $errorPayload): array
    {
        $now = time();
        $assistantText = 'O Guia Fitness está indisponível neste momento. Tenta novamente mais tarde.';
        $error = $errorPayload['error'] ?? null;
        if (is_array($error) && isset($error['code']) && is_string($error['code'])) {
            $assistantText .= ' (' . $error['code'] . ')';
        }

        $assistantMsg = [
            'id' => 'msg_error_' . $now,
            'object' => 'thread.message',
            'created_at' => $now,
            'assistant_id' => null,
            'thread_id' => $threadId,
            'run_id' => null,
            'role' => 'assistant',
            'content' => [
                [
                    'type' => 'text',
                    'text' => [
                        'value' => $assistantText,
                        'annotations' => [],
                    ],
                ],
            ],
            'attachments' => [],
            'metadata' => [],
        ];

        $userMsg = [
            'id' => 'msg_user_' . $now,
            'object' => 'thread.message',
            'created_at' => $now,
            'assistant_id' => null,
            'thread_id' => $threadId,
            'run_id' => null,
            'role' => 'user',
            'content' => [
                [
                    'type' => 'text',
                    'text' => [
                        'value' => $userMessage,
                        'annotations' => [],
                    ],
                ],
            ],
            'attachments' => [],
            'metadata' => [],
        ];

        return [
            'object' => 'list',
            'data' => [$assistantMsg, $userMsg],
            'first_id' => $assistantMsg['id'],
            'last_id' => $userMsg['id'],
            'has_more' => false,
            '_gymspot' => [
                'thread_id' => $threadId,
                'error' => $errorPayload['error'] ?? null,
                'openai_http_status' => $errorPayload['_gymspot']['openai_http_status'] ?? null,
            ],
        ];
    }

    private function extractAssistantTextFromResponse(array $responseBody): string
    {
        $texts = [];
        $output = $responseBody['output'] ?? [];
        if (!is_array($output)) {
            return '';
        }

        foreach ($output as $item) {
            if (!is_array($item)) {
                continue;
            }
            if (($item['type'] ?? null) !== 'message') {
                continue;
            }
            if (($item['role'] ?? null) !== 'assistant') {
                continue;
            }

            $content = $item['content'] ?? [];
            if (!is_array($content)) {
                continue;
            }

            foreach ($content as $part) {
                if (!is_array($part)) {
                    continue;
                }
                if (($part['type'] ?? null) === 'output_text' && isset($part['text']) && is_string($part['text'])) {
                    $texts[] = $part['text'];
                }
            }
        }

        return trim(implode("\n\n", $texts));
    }

    private function openaiRequest(string $method, string $url, ?array $payload = null, array $extraHeaders = []): array
    {
        $curl = curl_init();

        $headers = array_merge($extraHeaders, [
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
