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

class SortieController extends AbstractController
{

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
                $sortie->setEtat($em->getRepository(Etat::class)->findOneBy(['id'=>2]));
            }
            else{
                $sortie->setEtat( $em->getRepository(Etat::class)->findOneBy(['id'=>1]));
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
    public function edit(
        Sortie $sortie,
        Request $request,
        EntityManagerInterface $em,
        EtatRepository $etatRepository
    ): Response {
        // 1) Vérification de l'utilisateur
        //    Exemple : seul l'organisateur ou un admin peut modifier
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour modifier une sortie.');
            return $this->redirectToRoute('security_login');
        }

        // Vérifier si l'utilisateur est l'organisateur OU s'il a le rôle admin
        $estOrganisateur = ($sortie->getOrganisateur() === $user);
        $estAdmin = in_array('ROLE_ADMIN', $user->getRoles());
        if (!$estOrganisateur && !$estAdmin) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier cette sortie.');
            return $this->redirectToRoute('sortie_list');
        }

        // 2) Vérifier si la sortie est déjà passée
        //    Si dateHeureDebut < maintenant, on empêche la modification
        $now = new \DateTime();
        if ($sortie->getDateHeureDebut() < $now) {
            $this->addFlash('error', 'Impossible de modifier une sortie déjà commencée ou passée.');
            return $this->redirectToRoute('sortie_list');
        }

        // 3) Création du formulaire (avec l'entité existante)
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        // 4) Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifications supplémentaires : par exemple,
            // s'assurer que dateLimiteInscription < dateHeureDebut
            if ($sortie->getDateLimiteInscription() >= $sortie->getDateHeureDebut()) {
                $this->addFlash('error', 'La date limite d\'inscription doit être avant la date de début.');
                return $this->render('sortie/edit.html.twig', [
                    'form' => $form->createView(),
                    'sortie' => $sortie,
                ]);
            }

            // 5) Distinguer les différents boutons
            if ($request->request->has('publish')) {
                // Bouton "Publier la sortie"
                // Exemple : passer l'état à "Ouverte"
                $etatOuverte = $etatRepository->findOneBy(['libelle' => 'Ouverte']);
                if ($etatOuverte) {
                    $sortie->setEtat($etatOuverte);
                }
                $this->addFlash('success', 'La sortie a été publiée !');
            }
            elseif ($request->request->has('cancel')) {
                // Bouton "Annuler la sortie"
                // Exemple : passer l'état à "Annulée" et exiger un motif
                $etatAnnulee = $etatRepository->findOneBy(['libelle' => 'Annulée']);
                if ($etatAnnulee) {
                    // On peut exiger que le champ motifAnnulation ne soit pas vide
                    if (!$sortie->getMotifAnnulation()) {
                        $this->addFlash('error', 'Merci de renseigner un motif d\'annulation avant de valider.');
                        return $this->render('sortie/edit.html.twig', [
                            'form' => $form->createView(),
                            'sortie' => $sortie,
                        ]);
                    }
                    $sortie->setEtat($etatAnnulee);
                    $this->addFlash('success', 'La sortie a été annulée.');
                }
            }
            else {
                // Bouton "Enregistrer" (ou aucun bouton spécifique)
                // Exemple : laisser la sortie en état "Créée" ou garder l'état actuel
                $etatCree = $etatRepository->findOneBy(['libelle' => 'Créée']);
                if ($etatCree && $sortie->getEtat()->getLibelle() !== 'Ouverte') {
                    // On ne repasse pas une sortie "Ouverte" en "Créée", par exemple
                    $sortie->setEtat($etatCree);
                }
                $this->addFlash('success', 'Modifications enregistrées avec succès.');
            }

            // 6) Sauvegarder en base
            $em->flush();

            // 7) Redirection vers la liste ou autre
            return $this->redirectToRoute('sortie_list');
        }

        // 8) Afficher le formulaire
        return $this->render('sortie/edit.html.twig', [
            'form' => $form->createView(),
            'sortie' => $sortie,
        ]);
    }
    #[Route('/show/{id}', name: 'show_sortie',requirements: ['id' => '\d+'],methods: ['GET'])]
    public function index(Sortie $sortie): Response
    {
        $date = new \DateTime();
        return $this->render('sortie/show.html.twig',['sortie'=>$sortie, 'date'=>$date]);
    }

    #[Route('/show/{id}/add-participant', name: 'add_participant',requirements: ['id' => '\d+'],methods: ['GET'])]
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

    #[Route('/show/{id}/remove-participant', name: 'remove_participant',requirements: ['id' => '\d+'],methods: ['GET'])]
    public function removeparticipant(Sortie $sortie, EntityManagerInterface $em): Response
    {
        $user = $this->getUser()->getUserIdentifier();
        $participant = $em->getRepository(Participant::class)->findOneBy(['pseudo' => $user]);
        $sortie->removeParticipant($participant);
        $em->flush();
        return $this->redirectToRoute('show_sortie', ['id' => $sortie->getId()]);

    }

    //es amis qvemot rac weria me davwere 26/03

    #[Route('/sortie/{id}/annuler', name: 'sortie_annuler', methods: ['GET','POST'])]
    public function annuler(
        Sortie $sortie,
        Request $request,
        EntityManagerInterface $em,
        EtatRepository $etatRepository
    ): Response {
        // 1) Vérifier l'utilisateur (organisateur ou admin)
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour annuler une sortie.');
            return $this->redirectToRoute('security_login');
        }

        $estOrganisateur = ($sortie->getOrganisateur() === $user);
        $estAdmin = in_array('ROLE_ADMIN', $user->getRoles());
        if (!$estOrganisateur && !$estAdmin) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à annuler cette sortie.');
            return $this->redirectToRoute('sortie_list');
        }

        // 2) Vérifier que la sortie n'a pas commencé
        $now = new \DateTime();
        if ($sortie->getDateHeureDebut() <= $now) {
            $this->addFlash('error', 'Impossible d\'annuler une sortie déjà commencée.');
            return $this->redirectToRoute('sortie_list');
        }

        // 3) Créer le formulaire "AnnulerSortieType"
        $form = $this->createForm(AnnulerSortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 4) Récupérer l'état "Annulée"
            $etatAnnulee = $etatRepository->findOneBy(['libelle' => 'Annulée']);
            if (!$etatAnnulee) {
                $this->addFlash('error', 'État "Annulée" introuvable en base.');
                return $this->redirectToRoute('sortie_list');
            }

            // 5) Mettre la sortie en état "Annulée"
            $sortie->setEtat($etatAnnulee);
            // Le champ "motifAnnulation" est déjà hydraté via le formulaire

            // 6) Sauvegarde
            $em->flush();

            $this->addFlash('success', 'La sortie a été annulée avec succès.');
            return $this->redirectToRoute('sortie_list');
        }

        // 7) Affichage du formulaire
        return $this->render('sortie/annule.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

//    #[Route('/sortie/{id}/annuler', name: 'sortie_annuler', methods: ['GET','POST'])]
//    public function annuler(Sortie $sortie, Request $request, EntityManagerInterface $em, EtatRepository $etatRepository): Response
//    {
//        // On crée le formulaire basé sur SortieType
//        $form = $this->createForm(SortieType::class, $sortie);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            // Par ex. changer l’état en "Annulée"
//            $etatAnnulee = $etatRepository->findOneBy(['libelle' => 'Annulée']);
//            $sortie->setEtat($etatAnnulee);
//
//            // $sortie->setMotifAnnulation(...) est déjà hydraté par le formulaire
//            $em->flush();
//
//            $this->addFlash('success', 'La sortie a été annulée avec motif.');
//            return $this->redirectToRoute('sortie_list');
//        }
//
//        return $this->render('sortie/annule.html.twig', [
//            'sortie' => $sortie,
//            'form' => $form->createView(),
//        ]);
//    }

}
