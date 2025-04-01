<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\AnnulerSortieType;
use App\Form\SortieFiltersType;
use App\Form\SortieType;
use App\Model\SortieFiltersModel;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\filterService;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SortieController extends AbstractController
{

    #[Route('/')]
    #[Route('sortie/list', name: 'sortie_list', methods: ['GET'])]
    public function list(Request $request,EtatRepository $etatRepository , SortieRepository $sortieRepository, SortieFiltersModel $sortieFiltersModel, filterService $filterService): Response
    {
        $filter = new SortieFiltersModel();

        $form = $this->createForm(SortieFiltersType::class, $filter);
        $form->handleRequest($request);

        $sorties = $filterService->filterArchivedSorties($sortieRepository->findAll());
        if ($form->isSubmitted() && $form->isValid()) {
            $sorties = $filterService->filterSorties($sorties, $filter, $this->getUser());
            return $this->render('sortie/list.html.twig', [
                'sorties' => $sorties,
                'form' => $form->createView(),
            ]);
        }

        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties,
            'form' => $form->createView(),
        ]);
    }

    #[Route('sortie/create', name: 'sortie_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $sortie = new Sortie();
        $date = new \DateTime();

        $organisteur = $this->getUser();
        $sortie->setOrganisateur($organisteur);

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($date >= $sortie->getDateOuverture()){
                $sortie->setEtat($em->getRepository(Etat::class)->findOneBy(['libelle'=>'Ouverte']));
            }
            else{
                $sortie->setEtat( $em->getRepository(Etat::class)->findOneBy(['libelle'=>'Créée']));
            }

            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', 'La sortie a bien été ajoutée !');

            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('sortie/create.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/sortie/{id}/edit', name: 'sortie_edit', methods: ['GET','POST'])]
    #[IsGranted('modifier','sortie')]
    #[IsGranted('annuler','sortie')]
    public function edit(
        Sortie $sortie,
        Request $request,
        EntityManagerInterface $em,
        EtatRepository $etatRepository
    ): Response {

        if ($sortie->getEtat() === $etatRepository->findOneBy(['libelle' => 'Activité en cours'])
            || $sortie->getEtat() === $etatRepository->findOneBy(['libelle' => 'Passée']))
        {
            $this->addFlash('error', 'Impossible d\'annuler une sortie déjà commencée.');
            return $this->redirectToRoute('sortie_list');
        }

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->addFlash('success', 'Modifications enregistrées avec succès.');
            $em->flush();

            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('sortie/edit.html.twig', [
            'form' => $form->createView(),
            'sortie' => $sortie,
        ]);
    }


    #[Route('/sortie/{id}', name: 'show_sortie',requirements: ['id' => '\d+'],methods: ['GET'])]
    public function index(Sortie $sortie): Response
    {
        $date = new \DateTime();
        return $this->render('sortie/show.html.twig',['sortie'=>$sortie, 'date'=>$date]);
    }

    #[Route('/sortie/{id}/add-participant', name: 'add_participant',requirements: ['id' => '\d+'],methods: ['GET'])]
    public function addParticipant(Sortie $sortie, EntityManagerInterface $em): Response
    {
        $date = new \DateTime();
        if($this->getUser()!==$sortie->getOrganisateur() && $date < $sortie->getDateLimiteInscription()
            &&$sortie->getParticipants()->count() < $sortie->getNbInscriptionsMax()){
            $user = $this->getUser()->getUserIdentifier();
            $participant = $em->getRepository(Participant::class)->findOneBy(['pseudo' => $user]);
            $sortie->addParticipant($participant);
            $em->flush();
            return $this->redirectToRoute('show_sortie', ['id' => $sortie->getId()]);
        }

        return $this->redirectToRoute('show_sortie', ['id' => $sortie->getId()]);
    }

    #[Route('/sortie/{id}/remove-participant', name: 'remove_participant',requirements: ['id' => '\d+'],methods: ['GET'])]
    public function removeparticipant(Sortie $sortie, EntityManagerInterface $em): Response
    {
        $participant = $em->getRepository(Participant::class)->findOneBy(['pseudo' => $this->getUser()->getUserIdentifier()]);
        $sortie->removeParticipant($participant);
        $em->flush();
        return $this->redirectToRoute('show_sortie', ['id' => $sortie->getId()]);
    }


    #[Route('/sortie/{id}/annuler', name: 'sortie_annuler', methods: ['GET','POST'])]
    #[IsGranted('annuler','sortie')]
    public function annuler(
        Sortie $sortie,
        Request $request,
        EntityManagerInterface $em,
        EtatRepository $etatRepository
    ): Response {

        if ($sortie->getEtat() === $etatRepository->findOneBy(['libelle' => 'Activité en cours'])
            || $sortie->getEtat() === $etatRepository->findOneBy(['libelle' => 'Passée']))
        {
            $this->addFlash('error', 'Impossible d\'annuler une sortie déjà commencée.');
            return $this->redirectToRoute('sortie_list');
        }

        $form = $this->createForm(AnnulerSortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Annulée']));
            $em->flush();
            $this->addFlash('success', 'La sortie a été annulée avec succès.');
            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('sortie/annule.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

}
