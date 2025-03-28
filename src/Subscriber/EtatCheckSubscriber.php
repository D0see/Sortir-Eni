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

            $date = $_SERVER['REQUEST_TIME'];
            $sorties = $this->entityManager->getRepository(Sortie::class)->findAll();

            foreach ($sorties as $sortie) {

                $dateOuverture = $sortie->getDateOuverture()->getTimestamp();
                $dateLimiteInscription = $sortie->getDateLimiteInscription()->getTimestamp();
                $dateHeureDebut = $sortie->getDateHeureDebut()->getTimestamp()-3600;
                $interval = $sortie->getDureeEnHeures()*3600;
                $dateFin = $dateHeureDebut+$interval;

                if ($date < $dateOuverture) {
                    $etat = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Créée']);
                } elseif ($date > $dateOuverture && $date < $dateLimiteInscription) {
                    $etat = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
                } elseif ($date >  $dateLimiteInscription && $date < $dateHeureDebut || $sortie->getParticipants()->count() >= $sortie->getNbInscriptionsMax()) {
                    $etat = $this->entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Clôturée']);
                } elseif ($date >= $dateHeureDebut && $date <= $dateFin) {
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