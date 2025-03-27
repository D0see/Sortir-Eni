<?php

namespace App\Services;

use App\Entity\Participant;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CsvImporter
{
    private EntityManagerInterface $entityManager;
    private SiteRepository $siteRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, SiteRepository $siteRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->siteRepository = $siteRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function import(string $filePath): array
    {
        // Load CSV file
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);
        $records = Statement::create()->process($csv);

        $data = [];
        foreach ($records as $record) {
            $participant = new Participant();
            $participant->setPseudo($record['pseudo']);
            $participant->setPassword($this->passwordHasher->hashPassword($participant, $record['password']));
            $participant->setNom($record['nom']);
            $participant->setPrenom($record['prenom']);
            $participant->setTelephone($record['telephone']);
            $participant->setActif($record['actif'] == 1);
            $participant->setSite($this->siteRepository->findOneBy(["nom" => $record['site(name)']]));
            $participant->setMail($record['mail']);
            $participant->setAdministrateur($record['admin']);
            $participant->setRoles(["ROLE_USER"]);
            if ($record['admin'] == 1) {
                $participant->addRole("ROLE_ADMIN");
            }


            $this->entityManager->persist($participant);
            $data[] = $record;
        }

        $this->entityManager->flush();

        return $data;
    }
}