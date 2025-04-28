<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PagesController extends AbstractController
{
    private $products = [
        1 => [
            'id' => 1,
            'name' => 'Tunisian Olive Oil',
            'description' => 'Huile d\'olive extra vierge de qualité supérieure, pressée à froid.',
            'price' => 25.99,
            'image' => 'https://b2b.tn/files/2023/02/Huile-d%E2%80%99olive-Hausse-de-plus-de-50-des-exportations.png',
            'rating' => 4
        ],
        2 => [
            'id' => 2,
            'name' => 'Dates Deglet Nour',
            'description' => 'Dattes fraîches de qualité premium, récoltées dans les oasis du sud tunisien.',
            'price' => 18.50,
            'image' => 'https://www.thehouseofdates.com/site/wp-content/uploads/2017/04/DEGLET-NOUR-PROCESSED-DATES1_max.jpg',
            'rating' => 5
        ],
        3 => [
            'id' => 3,
            'name' => 'Poterie de Nabeul',
            'description' => 'Poteries artisanales traditionnelles fabriquées à Nabeul.',
            'price' => 45.00,
            'image' => 'https://c8.alamy.com/compfr/dwcj2w/l-afrique-du-nord-tunisie-cap-bon-nabeul-des-ceramiques-traditionnelles-dwcj2w.jpg',
            'rating' => 3
        ]
    ];

    #[Route('/home', name: 'app_home')]
    public function index(SessionInterface $session): Response
    {
        return $this->render('pages/index.html.twig', [
            'products' => $this->products,
            'cartCount' => count($session->get('cart', []))
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('pages/about.html.twig');
    }

    #[Route('/', name: 'app_pages')]
    public function login(): Response
    {
        return $this->render('pages/Connexion.html.twig');
    }

    #[Route('/register', name: 'app_register')]
    public function register(): Response
    {
        return $this->render('pages/Inscription.html.twig');
    }

    #[Route('/cart', name: 'app_cart')]
    public function cart(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            if (isset($this->products[$productId])) {
                $product = $this->products[$productId];
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'total' => $product['price'] * $quantity
                ];
                $total += $product['price'] * $quantity;
            }
        }

        return $this->render('pages/cart.html.twig', [
            'cartItems' => $cartItems,
            'total' => $total
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function addToCart(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        $cart[$id] = ($cart[$id] ?? 0) + 1;
        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function removeFromCart(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        
        if (isset($cart[$id])) {
            if ($cart[$id] > 1) {
                $cart[$id]--;
            } else {
                unset($cart[$id]);
            }
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clearCart(SessionInterface $session): Response
    {
        $session->remove('cart');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/checkout', name: 'app_checkout')]
    public function checkout(Request $request, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        
        if (empty($cart)) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createFormBuilder()
            ->add('nom', TextType::class, [
                'label' => 'Nom complet',
                'attr' => ['class' => 'form-control']
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse de livraison',
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Confirmer la commande',
                'attr' => ['class' => 'btn btn-success']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->remove('cart');
            return $this->redirectToRoute('app_order_confirmation', [
                'orderId' => uniqid('CMD-')
            ]);
        }

        return $this->render('pages/checkout.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/order/confirmation/{orderId}', name: 'app_order_confirmation')]
    public function orderConfirmation(string $orderId): Response
    {
        return $this->render('pages/order_confirmation.html.twig', [
            'orderId' => $orderId
        ]);
    }
}