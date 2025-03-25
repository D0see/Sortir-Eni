<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profil', name: 'participant_profile')]
    public function profile(): Response
    {
        $participant = $this->getUser();

        if (!$participant instanceof Participant) {
            $this->addFlash('error', 'Vous devez être connecté pour voir votre profil.');
            return $this->redirectToRoute('app_login');
        }

        // Sinon, on affiche le profil
        return $this->render('profile/profile.html.twig', [
            'participant' => $participant,
        ]);
    }

    #[Route('/profil/{id}', name: 'participant_show', requirements: ['id' => '\d+'])]
    public function show(Participant $participant): Response {
        return $this->render('profile/profile.html.twig', [
            'participant' => $participant,
        ]);
    }


    #[Route('/profil/edit', name: 'participant_edit', methods: ['GET','POST'])]
    public function editProfile(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        // 1) Récupérer l'utilisateur connecté
        $participant = $this->getUser();
        if (!$participant instanceof Participant) {
            $this->addFlash('error', 'Vous devez être connecté pour modifier votre profil.');
            return $this->redirectToRoute('app_login');
        }

        // 2) Créer le formulaire
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        // 3) Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder en base
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');
            // Rediriger vers la page de profil
            return $this->redirectToRoute('participant_profile');
        }

        // 4) Afficher le formulaire
        return $this->render('profile/edit.html.twig', [
            'editForm' => $form->createView(),
        ]);
    }
}