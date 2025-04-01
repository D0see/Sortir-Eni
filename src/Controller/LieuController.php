<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\FiltrerLieuType;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LieuController extends AbstractController
{

    #[Route('/lieu',name: 'lieu_list',methods: ['GET'])]
    public  function  list(Request $request, LieuRepository $lieuRepository):Response
    {
        $form = $this->createForm(FiltrerLieuType::class);
        $form->handleRequest($request);

        $lieu = $lieuRepository->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $searchValue = $form->get('search')->getData();
            if ($searchValue) {
                $lieu = $lieuRepository->createQueryBuilder('l')
                    ->where('l.nom LIKE :search')
                    ->setParameter('search', '%'.$searchValue.'%')
                    ->getQuery()
                    ->getResult();
            }
        }

        return $this->render('lieu/liste.html.twig', [
            'form' => $form->createView(),
            'lieus' => $lieu,
        ]);
    }


    #[Route('/lieu/add', name: 'add_lieu')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();
            return $this->redirectToRoute('lieu_list');
        }

        return $this->render('lieu/add.html.twig', [
            'lieuForm' => $form,
        ]);
    }


//    #[Route('/lieu/{id}/modifier', name: 'lieu_edit', methods: ['GET','POST'])]
//    public function edit(
//        Lieu $lieu,
//        Request $request,
//        EntityManagerInterface $em
//    ): Response {
//        $form = $this->createForm(LieuType::class, $lieu);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em->flush();
//            $this->addFlash('success', 'lieu modifiée avec succès.');
//            return $this->redirectToRoute('lieu_list');
//        }
//
//        return $this->render('lieu/edit.html.twig', [
//            'lieuForm' => $form->createView(),
//            'lieu' => $lieu,
//        ]);
//    }
//
//    #[Route('/lieu/{id}/annuler', name: 'lieu_delete', methods: ['GET','POST'])]
//    public function delete(
//        Lieu $lieu,
//        EntityManagerInterface $em
//    ): Response {
//        $em->remove($lieu);
//        $em->flush();
//
//        $this->addFlash('success', 'lieu supprimée avec succès.');
//        return $this->redirectToRoute('lieu_list');
//    }

}
