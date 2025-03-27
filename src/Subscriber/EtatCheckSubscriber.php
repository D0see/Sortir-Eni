<?php

namespace App\Subscriber;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EtatCheckSubscriber implements EventSubscriberInterface
{


    public function __construct(EntityManagerInterface $entityManager)
    {
     $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST =>'checkEtat'];
    }

    public function checkEtat(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $controller = $request->attributes->get('_controller');

        if (str_contains($controller, 'SortieController')) {
            $date = new \DateTime();
            $sorties = $this->entityManager->getRepository(Sortie::class)->findAll();
            foreach ($sorties as $sortie) {
                $interval = $sortie->getDureeEnHeures();
                $dateDebut = $sortie->getDateHeureDebut();
                $trueDateDebut = clone $dateDebut;
                $dateFin = $trueDateDebut->modify('+' . $interval . ' hours');
                if ($date < $sortie->getDateOuverture()) {
                    $etat = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Créée']);
                } elseif ($date > $sortie->getDateOuverture() && $date < $sortie->getDateLimiteInscription()) {
                    $etat = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
                } elseif ($date > $sortie->getDateLimiteInscription() && $date < $sortie->getDateHeureDebut()
                    || $sortie->getParticipants()->count() >= $sortie->getNbInscriptionsMax()) {
                    $etat = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Clôturée']);
                } elseif ($trueDateDebut >= $date && $trueDateDebut <= $dateFin) {
                    $etat = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Activité en cours']);
                } else {
                    $etat = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Passée']);

                }
                $sortie->setEtat($etat);
                $this->entityManager->persist($sortie);
            }
            $this->entityManager->flush();
        }
    }
}