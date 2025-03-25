<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SortieController extends AbstractController
{

    #[Route('sortie/list', name: 'sortie_list', methods: ['GET'])]
    public function list(SortieRepository $sortieRepository)
    {
        $sorties = $sortieRepository->findAll();

        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties,
        ]);
    }

    #[Route('sortie/create', name: 'sortie_create', methods: ['GET', 'POST'])]
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
        return $this->render('sortie/show.html.twig',['sortie'=>$sortie]);
    }

    #[Route('/show/{id}/add-participant', name: 'add_participant',requirements: ['id' => '\d+'],methods: ['GET'])]
    public function addParticipant(Sortie $sortie, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser()->getUserIdentifier();
        $participant = $em->getRepository(Participant::class)->findOneBy(['pseudo' => $user]);
        $sortie->addParticipant($participant);
        $em->flush();
        return $this->redirectToRoute('show_sortie', ['id' => $sortie->getId()]);

    }

}
