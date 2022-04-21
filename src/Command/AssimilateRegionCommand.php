<?php

namespace App\Command;

use App\Entity\Region;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\ParseFileService;

class AssimilateRegionCommand extends Command
{
    protected static $defaultName = 'cron:assimilateRegion';
    protected static $defaultDescription = "Assimilation d'un fichier csv pour int�grer des r�gions en BDD";

    private $em;
    private $parseFile;

    public function __construct(
        EntityManagerInterface $entityManager,
        ParseFileService $parseFile
    )
    {
        parent::__construct();
        $this->em = $entityManager;
        $this->parseFile = $parseFile;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = "/var/www/sites/projetperso/DataCity/public/csv/regions_france2.csv";

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
            $region = new Region();
            $region->setCode($line["code_region"])
                ->setName($line["nom_region"]);
            $this->em->persist($region);

            if(($batch % 100) === 0) {
                $this->em->flush();
                $batch = 0;
            }
        }

        $this->em->flush();
        return true;
    }
}