<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Form\GroupeType;
use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/groupe')]
final class GroupeController extends AbstractController
{
    #[Route('/mesGroupes', name: 'mes_groupes')]
    public function index(GroupeRepository $groupeRepository): Response
    {
        $groupesOuJeSuis = $groupeRepository->getGroupesApparus($this->getUser());
        $mesgroupes = $groupeRepository->getGroupesPossedes($this->getUser());

        return $this->render('groupe/index.html.twig', [
            'controller_name' => 'GroupeController',
            'mesGroupes' => $mesgroupes,
            'groupesOuJeSuis' => $groupesOuJeSuis,
        ]);
    }

    #[Route('/new', name: 'app_groupe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $groupe = new Groupe();
        $groupe->addParticipant($this->getUser());
        $form = $this->createForm(GroupeType::class, $groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupe->setCreateur($this->getUser());
            $entityManager->persist($groupe);
            $entityManager->flush();

            return $this->redirectToRoute('mes_groupes', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupe/new.html.twig', [
            'groupe' => $groupe,
            'form' => $form,
            'userPseudo' => $this->getUser()->getPseudo(),
        ]);
    }

    #[Route('/{id}', name: 'app_groupe_show', methods: ['GET'])]
    public function show(Groupe $groupe): Response
    {
        return $this->render('groupe/show.html.twig', [
            'groupe' => $groupe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_groupe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Groupe $groupe, EntityManagerInterface $entityManager): Response
    {
        if ($groupe->getCreateur()->getId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(GroupeType::class, $groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('mes_groupes', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupe/edit.html.twig', [
            'groupe' => $groupe,
            'form' => $form,
            'userPseudo' => $this->getUser()->getPseudo(),
        ]);
    }

    #[Route('/{id}', name: 'app_groupe_delete', methods: ['POST'])]
    public function delete(Request $request, Groupe $groupe, EntityManagerInterface $entityManager): Response
    {
        if ($groupe->getCreateur()->getId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$groupe->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($groupe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('mes_groupes', [], Response::HTTP_SEE_OTHER);
    }
}
