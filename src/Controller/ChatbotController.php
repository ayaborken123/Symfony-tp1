<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ChatbotController extends AbstractController
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/chatbot', name: 'chatbot')]
    public function chatbot(Request $request): Response
    {
        $userMessage = $request->query->get('message');

        try {
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_ENV['OPENAI_API_KEY'],
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'model' => 'gpt-4o',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Tu es un assistant IA.'],
                        ['role' => 'user', 'content' => $userMessage]
                    ],
                    'max_tokens' => 100,
                    'temperature' => 0.7,
                    'top_p' => 1.0,
                    'frequency_penalty' => 0.5,
                    'presence_penalty' => 0.5
                ]
            ]);
        
            $responseData = $response->toArray();
        
            if ($response->getStatusCode() !== 200) {
                return new Response(json_encode([
                    'error' => '❌ Erreur API OpenAI : ' . $response->getStatusCode(),
                    'details' => $responseData
                ]), 500);
            }
        
            return new Response(json_encode($responseData));
        } catch (\Exception $e) {
            return new Response(json_encode([
                'error' => '⚠️ Erreur interne : ' . $e->getMessage()
            ]), 500);
        }
        
    }
}
