<?php

namespace App\Command;

use App\Entity\City;
use App\Repository\DepartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\ParseFileService;

class AssimilateCityCommand extends Command
{
    protected static $defaultName = 'cron:assimilateCity';
    protected static $defaultDescription = "Assimilation d'un fichier csv pour intégrer des villes en BDD";

    private $em;
    private $parseFile;
    private $departmentRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ParseFileService       $parseFile,
        DepartmentRepository   $departmentRepository
    )
    {
        parent::__construct();
        $this->em = $entityManager;
        $this->parseFile = $parseFile;
        $this->departmentRepository = $departmentRepository;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = "/var/www/sites/projetperso/DataCity/public/csv/commune_2.csv";

        print_r($this->treatment($file));

        return 1;
    }

    private function treatment($file): string
    {
        $dataFile = $this->parseFile->parseCsv($file);

        if($this->assimilation($dataFile)){
            return "Tout s'est bien passé !\n\r";
        }

        return "Il y a eu un problème.\n\r";

    }

    private function assimilation($dataFile): bool
    {
        $batch = 0;
        foreach ($dataFile as $line){
            $batch++;
            $city = new City();
            $city->setName($line["nom_commune"])
                ->setCode($line["code_commune"])
                ->setLatitude($line["latitude"])
                ->setLongitude($line["longitude"])
                ->setFullName($line["nom_commune_complet"])
                ->setCodeInsee($line["code_commune_INSEE"])
                ->setPostalCode($line["code_postal"])
                ->setDepartment($this->departmentRepository->findOneBy(["code" => $line["code_departement"]]));
            $this->em->persist($city);

            if(($batch % 100) === 0) {
                $this->em->flush();
                $batch = 0;
            }
        }

        $this->em->flush();
        return true;
    }
}