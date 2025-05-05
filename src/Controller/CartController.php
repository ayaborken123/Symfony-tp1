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
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

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

    #[Route('/cart/delete/{productId}', name: 'cart_delete', methods: ['DELETE'])]
public function removeFromCart(int $productId, SessionInterface $session): JsonResponse
{
    $cart = $session->get('cart', []);

    foreach ($cart as $key => $item) {
        if ($item['productId'] == $productId) {
            unset($cart[$key]);
            $cart = array_values($cart); // Réindexe les clés du tableau
            $session->set('cart', $cart);
            return $this->json(['message' => 'Produit supprimé du panier']);
        }
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

    // Récupérer les quantités envoyées depuis le formulaire
    $quantities = $request->request->all()['quantities'];

    // Vérifier que toutes les quantités sont bien renseignées
    foreach ($cart as $key => &$item) {
        $item['quantity'] = $quantities[$key] ?? 1; // Valeur par défaut = 1
    }

    // Enregistrer la commande en base
    $order = new Order();
    $order->setCustomerEmail($email);
    $order->setProducts($cart);
    $order->setQuantities(array_column($cart, 'quantity')); // Sauvegarde des quantités

    $entityManager->persist($order);
    $entityManager->flush();

    $session->set('cart', []);

    // ✅ Ajout du message flash via `$session->getFlashBag()`
    $session->getFlashBag()->add('success', 'Votre commande a été passée avec succès ! Merci pour votre confiance.');

    return $this->redirectToRoute('order_confirmation_page');
}
#[Route('/order/confirmation', name: 'order_confirmation_page')]
public function confirmation(): Response
{
    return $this->render('pages/order_confirmation.html.twig');
}


}