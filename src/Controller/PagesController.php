<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class PagesController extends AbstractController
{
    

    #[Route('/home', name: 'app_home')]
    public function index(SessionInterface $session): Response
    {
        return $this->render('pages/index.html.twig', []);
    }

    #[Route('/', name: 'app_pages')]
    public function login(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_dashboard');
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/register', name: 'app_register')]
    public function register(): Response
    {
        return $this->render('pages/Inscription.html.twig');
    }

    #[Route('/order', name: 'app_order', methods: ['POST'])]
    public function processOrder(SessionInterface $session, EntityManagerInterface $entityManager, Request $request): Response
    {
        $cart = $session->get('cart', []);
        if (empty($cart)) {
            return new Response('Panier vide', Response::HTTP_BAD_REQUEST);
        }

        $email = $request->request->get('email');
        if (!$email) {
            return new Response('Email requis', Response::HTTP_BAD_REQUEST);
        }

        $order = new Order();
        $order->setCustomerEmail($email);
        $order->setProducts($cart);

        $entityManager->persist($order);
        $entityManager->flush();

        $session->set('cart', []);

        return $this->redirectToRoute('app_home', ['message' => 'Commande enregistrée']);
    }

   

}
