<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeMateriel;
use App\Form\CommandeMaterielType;
use App\Repository\CommandeMaterielRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/commande/materiel')]
class CommandeMaterielController extends AbstractController
{
    #[Route('/', name: 'app_commande_materiel_index', methods: ['GET'])]
    public function index(CommandeMaterielRepository $commandeMaterielRepository): Response
    {
        return $this->render('commande_materiel/index.html.twig', [
            'commande_materiels' => $commandeMaterielRepository->findAll(),
        ]);
    }

    #[Route('/commande/{commandeId}/ajouter-materiel', name: 'app_commande_materiel_new', methods: ['GET', 'POST'])]
    public function new(int $commandeId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = $entityManager->getRepository(Commande::class)->find($commandeId);
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }

        $commandeMateriel = new CommandeMateriel();
        $commandeMateriel->setCommande($commande);

        $form = $this->createForm(CommandeMaterielType::class, $commandeMateriel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $materiel = $commandeMateriel->getMateriel();
            $quantite = $commandeMateriel->getQuantite();

            if ($materiel->getQuantite() < $quantite) {
                $this->addFlash('error', 'Quantité demandée dépasse le stock disponible.');
                return $this->render('commande_materiel/new.html.twig', [
                    'commande_materiel' => $commandeMateriel,
                    'form' => $form,
                ]);
            }

            $commande->addCommandeMateriel($commandeMateriel);
            $materiel->setQuantite($materiel->getQuantite() - $quantite);
            $entityManager->persist($materiel);

            $this->recalculerPrixTotal($commande);

            $entityManager->persist($commandeMateriel);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_materiel_new', [
                'commandeId' => $commande->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande_materiel/new.html.twig', [
            'commande_materiel' => $commandeMateriel,
            'form' => $form,
        ]);
    }

    private function recalculerPrixTotal(Commande $commande): void
    {
        $total = 0;

        foreach ($commande->getCommandeMateriels() as $commandeMateriel) {
            $materiel = $commandeMateriel->getMateriel();
            $total += $materiel->getPrixLocation() * $commandeMateriel->getQuantite();
        }

        $commande->setTotalPrix($total);
    }

    #[Route('/{id}', name: 'app_commande_materiel_show', methods: ['GET'])]
    public function show(CommandeMateriel $commandeMateriel): Response
    {
        return $this->render('commande_materiel/show.html.twig', [
            'commande_materiel' => $commandeMateriel,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_materiel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CommandeMateriel $commandeMateriel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeMaterielType::class, $commandeMateriel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_materiel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande_materiel/edit.html.twig', [
            'commande_materiel' => $commandeMateriel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_materiel_delete', methods: ['POST'])]
    public function delete(Request $request, CommandeMateriel $commandeMateriel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commandeMateriel->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($commandeMateriel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_materiel_index', [], Response::HTTP_SEE_OTHER);
    }
}
