<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SortieController extends AbstractController
{

    #[Route('/list', name: 'sortie_list', methods: ['GET'])]
    public function list(SortieRepository $sortieRepository)
    {
        $sorties = $sortieRepository->findAll();

        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties,
        ]);
    }

    #[Route('/sortie/create', name: 'sortie_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $sortie = new Sortie();

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', 'La sortie a bien été ajoutée !');

            // Redirection (à adapter) :
            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('sortie/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/show/{id}', name: 'show_sortie',requirements: ['id' => '\d+'],methods: ['GET'])]
    public function index(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig',['sortie'=>$sortie]);
    }
}
