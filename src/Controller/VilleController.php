<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\SearchVilleType;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VilleController extends AbstractController
{
    #[Route('/ville', name: 'ville_list', methods: ['GET'])]
    public function list(Request $request, VilleRepository $villeRepository): Response
    {
        $form = $this->createForm(SearchVilleType::class);
        $form->handleRequest($request);

        $villes = $villeRepository->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $searchValue = $form->get('search')->getData();
            if ($searchValue) {
                $villes = $villeRepository->createQueryBuilder('v')
                    ->where('v.nom LIKE :search')
                    ->setParameter('search', '%'.$searchValue.'%')
                    ->getQuery()
                    ->getResult();
            }
        }

        return $this->render('ville/liste.html.twig', [
            'form' => $form->createView(),
            'villes' => $villes,
        ]);
    }


    #[Route('/ville/add', name: 'add_ville')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();
            return $this->redirectToRoute('ville_list');
        }

        return $this->render('ville/add.html.twig', [
            'villeForm' => $form,
        ]);
    }

//    #[Route('/ville/{id}/modifier', name: 'ville_edit', methods: ['GET','POST'])]
//    public function edit(
//        Ville $ville,
//        Request $request,
//        EntityManagerInterface $em
//    ): Response {
//        $form = $this->createForm(VilleType::class, $ville);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em->flush();
//            $this->addFlash('success', 'Ville modifiée avec succès.');
//            return $this->redirectToRoute('ville_list');
//        }
//
//        return $this->render('ville/edit.html.twig', [
//            'villeForm' => $form->createView(),
//            'ville' => $ville,
//        ]);
//    }
//
//
//    #[Route('ville/{id}/supprimer', name: 'ville_delete', methods: ['GET','POST'])]
//    public function delete(
//        Ville $ville,
//        EntityManagerInterface $em
//    ): Response {
//        $em->remove($ville);
//        $em->flush();
//
//        $this->addFlash('success', 'Ville supprimée avec succès.');
//        return $this->redirectToRoute('ville_list');
//    }

}
