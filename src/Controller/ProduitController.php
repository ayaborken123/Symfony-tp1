<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/panier', name: 'app_panier')]
public function panier(Request $request): Response
{
    $session = $request->getSession();
    $panier = $session->get('panier', []);

    // Calcul du total
    $total = 0;
    foreach ($panier as $item) {
        $total += $item['produit']->getPrix() * $item['quantite'];
    }

    return $this->render('panier/index.html.twig', [
        'panier' => $panier,
        'total' => $total
    ]);
}

#[Route('/panier/ajouter/{id}', name: 'app_panier_ajouter')]
public function ajouterAuPanier(Produit $produit, Request $request): Response
{
    $session = $request->getSession();
    $panier = $session->get('panier', []);

    $id = $produit->getId();

    if (!isset($panier[$id])) {
        $panier[$id] = [
            'produit' => $produit,
            'quantite' => 1
        ];
    } else {
        $panier[$id]['quantite']++;
    }

    $session->set('panier', $panier);

    $this->addFlash('success', 'Produit ajouté au panier');
    return $this->redirectToRoute('app_panier');
}

#[Route('/panier/retirer/{id}', name: 'app_panier_retirer')]
public function retirerDuPanier(Produit $produit, Request $request): Response
{
    $session = $request->getSession();
    $panier = $session->get('panier', []);

    $id = $produit->getId();

    if (isset($panier[$id])) {
        if ($panier[$id]['quantite'] > 1) {
            $panier[$id]['quantite']--;
        } else {
            unset($panier[$id]);
        }
    }

    $session->set('panier', $panier);

    $this->addFlash('warning', 'Produit retiré du panier');
    return $this->redirectToRoute('app_panier');
}

#[Route('/panier/vider', name: 'app_panier_vider')]
public function viderPanier(Request $request): Response
{
    $session = $request->getSession();
    $session->remove('panier');

    $this->addFlash('danger', 'Panier vidé');
    return $this->redirectToRoute('app_panier');
}
}