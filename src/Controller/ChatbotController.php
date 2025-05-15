<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // ✅ Ajouté
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChatbotController extends AbstractController
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/chatbot', name: 'chatbot', methods: ['GET'])]
public function chatbot(Request $request): Response
{
    $userMessage = $request->query->get('message', 'Bonjour');

    try {
        $response = $this->httpClient->request('GET', 'http://127.0.0.1:8001/chatbot', [ // ✅ Correction du port
            'query' => ['message' => $userMessage]
        ]);

        return $this->json($response->toArray());
    } catch (\Exception $e) {
        return $this->json(['error' => '❌ Erreur de connexion à FastAPI : ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

}
