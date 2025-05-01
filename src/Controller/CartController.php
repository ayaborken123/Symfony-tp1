<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class CartController extends AbstractController
{
    #[Route('/cart/view', name: 'cart_view_page')]
    public function viewCartPage(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
    
        return $this->render('pages/cart.html.twig', [
            'cartItems' => $cart
        ]);
    }
    

    #[Route('/cart/add', name: 'cart_add', methods: ['POST'])]
    public function addToCart(Request $request, SessionInterface $session): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $cart = $session->get('cart', []);

        $cart[] = [
            'productId' => $data['productId'],
            'productName' => $data['productName'],
            'price' => $data['price'],
            'quantity' => 1
        ];

        $session->set('cart', $cart);
        return $this->json(['message' => 'Produit ajouté au panier']);
    }

    #[Route('/cart/delete/{index}', name: 'cart_delete', methods: ['DELETE'])]
    public function removeFromCart(int $index, SessionInterface $session): JsonResponse
    {
        $cart = $session->get('cart', []);
        if (isset($cart[$index])) {
            unset($cart[$index]);
            $session->set('cart', array_values($cart));
            return $this->json(['message' => 'Produit supprimé du panier']);
        }

        return $this->json(['error' => 'Produit introuvable'], 404);
    }

    #[Route('/cart/clear', name: 'cart_clear', methods: ['DELETE'])]
    public function clearCart(SessionInterface $session): JsonResponse
    {
        $session->set('cart', []);
        return $this->json(['message' => 'Panier vidé']);
    }
    #[Route('/cart/confirm', name: 'cart_confirm_page')]
public function confirmPurchase(SessionInterface $session): Response
{
    $cart = $session->get('cart', []);
    return $this->render('pages/confirm.html.twig', [
        'cartItems' => $cart,
        'totalItems' => count($cart)
    ]);
}

#[Route('/cart/checkout', name: 'cart_checkout', methods: ['POST'])]
public function checkout(Request $request, SessionInterface $session, EntityManagerInterface $entityManager): Response
{
    $cart = $session->get('cart', []);
    if (empty($cart)) {
        return new Response('Panier vide, impossible de passer commande', Response::HTTP_BAD_REQUEST);
    }

    $email = $request->request->get('email');
    if (!$email) {
        return new Response('Email requis pour valider la commande', Response::HTTP_BAD_REQUEST);
    }

    $order = new Order();
    $order->setCustomerEmail($email);
    $order->setProducts($cart);

    $entityManager->persist($order);
    $entityManager->flush();

    $session->set('cart', []);

    return $this->redirectToRoute('app_home', ['message' => 'Commande confirmée et enregistrée']);
}
}