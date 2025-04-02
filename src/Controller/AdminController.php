<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin/users', name: 'admin_users', methods: ['GET', 'POST'])]
    public function manageUsers(
        Request $request,
        ParticipantRepository $participantRepository,
        EntityManagerInterface $em
    ): Response {
        // Récupérer tous les utilisateurs, par exemple triés par pseudo
        $users = $participantRepository->findBy([], ['pseudo' => 'ASC']);

        if ($request->isMethod('POST')) {
            // Récupérer les IDs des utilisateurs sélectionnés
            $selectedIds = $request->request->all('selected_users');

            if (!is_array($selectedIds)) {
                $selectedIds = [];
            }

            if ($request->request->has('disable')) {
                // Désactiver les utilisateurs sélectionnés qui ne sont pas admin
                foreach ($selectedIds as $id) {
                    $user = $participantRepository->find($id);
                    if ($user && !in_array('ROLE_ADMIN', $user->getRoles())) {
                        $user->setActif(false);
                    }
                }
                $em->flush();
                $this->addFlash('success', 'Les utilisateurs sélectionnés ont été désactivés.');
            } elseif ($request->request->has('enable')){
                foreach ($selectedIds as $id){
                    $user=$participantRepository->find($id);
                    if ($user && !in_array('ROLE_ADMIN',$user->getRoles())){
                        $user->setActif(true);
                    }
                }
                $em->flush();
                $this->addFlash('success', 'Les utilisateurs sélectionnés ont été activés.');
            }

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/manage_users.html.twig', [
            'users' => $users,
        ]);
    }
}
