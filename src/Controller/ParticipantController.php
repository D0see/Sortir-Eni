<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\User;
use App\Form\ParticipantType;
use App\Form\RegistrationFormType;
use App\Repository\SiteRepository;
use App\Security\ParticipantAuthenticator;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ParticipantController extends AbstractController
{
    #[Route('participant/inscription', name: 'inscription_participant')]
    public function inscription(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security, EntityManagerInterface $entityManager,
        SiteRepository $siteRepository)
    : Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        //aq davamate chemi kodi
        if ($form->isSubmitted() && $form->isValid()) {
            $photo=$form->get('maPhoto')->getData();
            if ($photo){
                $newFilename=uniqid().'.'.$photo->guessExtension();
                try {
                    // Déplacer le fichier dans le dossier défini
                    $photo->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gestion d'erreur lors de l'upload
                    $this->addFlash('error', 'Erreur lors de l\'upload de la photo.');
                    return $this->render('registration/participant_register.html.twig', [
                        'registrationForm' => $form->createView(),
                    ]);
                }
                $participant->setMaPhoto($newFilename);
            }
            //aq mtavrdeba chamatebuli kodi
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $participant->setPassword(
                $userPasswordHasher->hashPassword($participant, $plainPassword)
            );

            $participant->setRoles(['ROLE_USER']);
            $site = $siteRepository->find(1);
            $participant->setSite($site);

            $entityManager->persist($participant);
            $entityManager->flush();


            return $security->login($participant, ParticipantAuthenticator::class, 'main');
        }

        return $this->render('registration/participant_register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('participant/monProfil', name: 'mon_profil')]
    public function monProfile(): Response
    {
        $participant = $this->getUser();

        return $this->render('profile/profile.html.twig', [
            'participant' => $participant,
        ]);
    }

    #[Route('/participant/modifierMonProfil', name: 'modifier_mon_profil', methods: ['GET','POST'])]
    public function editProfile(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        // 1) Récupérer l'utilisateur connecté
        $participant = $this->getUser();

        // 2) Créer le formulaire
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        // 3) Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('maPhoto')->getData();
            if ($photoFile){
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                try {
                    $photoFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de la photo.');
                    return $this->render('profile/edit.html.twig', [
                        'editForm' => $form->createView(),
                        'participant' => $participant,
                    ]);
                }

                $participant->setMaPhoto($newFilename);
            }
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');
            // Rediriger vers la page de profil
            return $this->redirectToRoute('mon_profil');
        }

        // 4) Afficher le formulaire
        return $this->render('profile/edit.html.twig', [
            'editForm' => $form->createView(),
            'participant' => $participant,
        ]);
    }


    #[Route('/participant/{id}', name: 'afficher_participant', requirements: ['id' => '\d+'])]
    public function show(Participant $participant): Response {
        if ($this->getUser()->getId() == $participant->getId()) {
            return $this->redirectToRoute("mon_profil");
        };
        return $this->render('profile/profile.html.twig', [
            'participant' => $participant,
        ]);
    }
}