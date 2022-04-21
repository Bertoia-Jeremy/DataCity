<?php

namespace App\Command;

use App\Entity\Department;
use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\ParseFileService;

class AssimilateDepartmentCommand extends Command
{
    protected static $defaultName = 'cron:assimilateDepartment';
    protected static $defaultDescription = "Assimilation d'un fichier csv pour intégrer des département en BDD";

    private $em;
    private $parseFile;
    private $regionRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ParseFileService       $parseFile,
        RegionRepository       $regionRepository
    )
    {
        parent::__construct();
        $this->em = $entityManager;
        $this->parseFile = $parseFile;
        $this->regionRepository = $regionRepository;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = "/var/www/sites/projetperso/DataCity/public/csv/departements_france.csv";

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
            $department = new Department();
            $department
                ->setCode($line["code_departement"])
                ->setName($line["nom_departement"])
                ->setRegion($this->regionRepository->findOneBy(["code" => $line["code_region"]]));
            $this->em->persist($department);

            if(($batch % 100) === 0) {
                $this->em->flush();
                $batch = 0;
            }
        }

        $this->em->flush();
        return true;
    }
}