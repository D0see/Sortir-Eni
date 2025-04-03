<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestType;
use App\Form\ResetPasswordType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ResetPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'app_forgot_password',methods: ['GET','POST'])]
    public function request( Request $request,ParticipantRepository $participantRepository,EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ResetPasswordRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $pseudo = $form->get('pseudo')->getData();

            $participant = $participantRepository->findOneBy(['pseudo' => $pseudo]);

            if (!$participant){
                $this->addFlash('error', 'Ce user n\'existe pas.');
                return $this->redirectToRoute('app_forgot_password');
            }

            $token = bin2hex(random_bytes(32));
            $participant->setResetToken($token);
            // Définir une expiration (par exemple, 1 heure)
            $participant->setResetTokenExpiresAt((new \DateTime())->modify('+1 hour'));

            $em->flush();


            $resetLink = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);


//            $this->addFlash('success', 'Un lien de réinitialisation a été généré : ' . $resetLink);

            return $this->redirectToRoute('app_reset_password', ['token' => $token] );


        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }


    //    #[\Symfony\Component\Routing\Annotation\Route('/reset-password/{token}', name: 'app_reset_password', methods: ['GET','POST'])]
    #[Route('/reset-password/{token}', name: 'app_reset_password', methods: ['GET','POST'])]
    public function reset(
        string $token,
        Request $request,
        ParticipantRepository $participantRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $participant = $participantRepository->findOneBy(['resetToken' => $token]);
        if (!$participant) {
            throw $this->createNotFoundException('Token invalide.');
        }

        // Vérifier si le token a expiré
        if ($participant->getResetTokenExpiresAt() < new \DateTime()) {
            throw $this->createNotFoundException('Le token a expiré.');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($participant, $plainPassword);
            $participant->setPassword($hashedPassword);

            // Supprimer le token pour empêcher une réutilisation
            $participant->setResetToken(null);
            $participant->setResetTokenExpiresAt(null);

            $em->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }
}
