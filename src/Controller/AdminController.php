<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{

    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_home');
        }
    
        $users = $entityManager->getRepository(User::class)->findAll();
        $orders = $entityManager->getRepository(Order::class)->findAll(); // ✅ Vérification
    
        return $this->render('admin/dashboard.html.twig', [
            'users' => $users,
            'orders' => $orders // ✅ Assurez-vous que `orders` est bien passé à Twig
        ]);
    }
    
    #[Route('/delete/user/{id}', name: 'admin_delete_user')]
    public function deleteUser($id, EntityManagerInterface $entityManager): Response
    {
        $idInt = (int) $id; // 🔹 Conversion explicite en `int`
        dump($idInt); // ✅ Vérification de l'ID reçu avant suppression
    
        $user = $entityManager->getRepository(User::class)->find($idInt);
    
        if (!$user) {
            return new Response('⚠️ Erreur : Aucun utilisateur trouvé avec ID ' . $idInt);
        }
    
        $entityManager->remove($user);
        $entityManager->flush();
    
        return $this->redirectToRoute('admin_dashboard');
    }
  
    #[Route('/delete/order/{id}', name: 'admin_delete_order')]
    public function deleteOrder(int $id, EntityManagerInterface $entityManager): Response
    {
        $order = $entityManager->getRepository(Order::class)->find($id);
        if ($order) {
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/test-user', name: 'test_user')]
    public function testUser(EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@gmail.com']);

        if (!$user) {
            return new Response('L\'utilisateur admin@gmail.com est introuvable.');
        }

        return new Response('Utilisateur trouvé : ' . $user->getEmail() . ' avec rôle : ' . json_encode($user->getRoles()));
    }
}
