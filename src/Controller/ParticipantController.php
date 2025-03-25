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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ParticipantController extends AbstractController
{
    #[Route('/inscription', name: 'participant_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security, EntityManagerInterface $entityManager,
        SiteRepository $siteRepository)
    : Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

            // do anything else you need here, like send an email

            return $security->login($participant, ParticipantAuthenticator::class, 'main');
        }

        return $this->render('registration/participant_register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

}