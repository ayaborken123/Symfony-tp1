<?php
// src/Service/OpenRouterService.php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenRouterService
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiKey = $_ENV['OPENROUTER_API_KEY'];
    }

    public function getResponse(string $message): string
    {
        try {
            $response = $this->client->request('POST', 'https://openrouter.ai/api/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'mistralai/mixtral-8x7b',
                    'messages' => [
                        ['role' => 'user', 'content' => $message]
                    ]
                ],
            ]);

            $data = $response->toArray();

            return $data['choices'][0]['message']['content'] ?? 'Pas de réponse.';
        } catch (\Exception $e) {
            return "⚠️ Erreur OpenRouter : " . $e->getMessage();
        }
    }
}
