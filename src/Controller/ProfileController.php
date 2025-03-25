<?php

namespace App\Controller;

use App\Entity\Participant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}