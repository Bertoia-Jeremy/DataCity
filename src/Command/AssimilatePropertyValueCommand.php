<?php

namespace App\Command;

use App\Entity\City;
use App\Entity\PropertyValue;
use App\Repository\CityRepository;
use App\Repository\DepartmentRepository;
use App\Repository\PropertyValueRepository;
use App\Service\ChangeFileExtensionService;
use App\Tools\GZTempFile;
use Doctrine\ORM\EntityManagerInterface;
use PharData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\ParseFileService;
use ZipArchive;

class AssimilatePropertyValueCommand extends Command
{
    protected static $defaultName = 'cron:assimilationPropertyValue';
    protected static $defaultDescription = "Assimilation d'un fichier csv pour intégrer des valeurs foncières en BDD";

    private $em;
    private $parseFile;
    private $cityRepository;
    private $propertyRepository;
    private $departmentRepository;
    private $changeFileExtensionService;

    public function __construct(
        EntityManagerInterface  $entityManager,
        ParseFileService        $parseFile,
        CityRepository          $cityRepository,
        PropertyValueRepository $propertyRepository,
        DepartmentRepository    $departmentRepository,
        ChangeFileExtensionService $changeFileExtension
    )
    {
        parent::__construct();
        $this->em = $entityManager;
        $this->parseFile = $parseFile;
        $this->cityRepository = $cityRepository;
        $this->propertyRepository = $propertyRepository;
        $this->departmentRepository = $departmentRepository;
        $this->changeFileExtensionService = $changeFileExtension;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->em->getConnection()->getConfiguration()->setSQLLogger();

        $year = "2016";
        //$departments = $this->departmentRepository->findAll();

        /*foreach ($departments as $department){
            $code_department = $department->getCode();

            if(!in_array($code_department, $alReadyTreated, true)){
                $urlFile = "https://files.data.gouv.fr/geo-dvf/latest/csv/$year/departements/$code_department.csv.gz";

                $directorySave = '/var/www/sites/projetperso/DataCity/public/csv/PropertyValue/';
                $fileName = $this->downloadFileGz($urlFile, $directorySave);

                if($fileName){
                    print_r("Download of $urlFile");
                    print_r($this->treatment($fileName));
                }else{
                    print_r("Error with $urlFile");
                }
            }
            $this->em->clear();
        }*/

        $urlFile = "https://files.data.gouv.fr/geo-dvf/latest/csv/$year/departements/05.csv.gz";
        $directorySave = '/var/www/sites/projetperso/DataCity/public/csv/PropertyValue/';
        $fileName = $this->downloadFileGz($urlFile, $directorySave);

        if($fileName){
            print_r("Download of $urlFile \n\r");

            $csvFileName = $this->changeFileExtensionService->changeGzToCsv($fileName);
            print_r($this->treatment($csvFileName));
        }else{
            print_r("Error with $urlFile");
        }

        return 1;
    }

    private function treatment($fileName): string
    {
        $dataFile = $this->parseFile->parseCsv($fileName);

        if($this->assimilation($dataFile)){
            return "Tout s'est bien passé !\n\r";
        }

        return "Il y a eu un problème.\n\r";

    }

    private function assimilation($dataFile): bool
    {
        $batch = 0;
        $cityNotFound = $this->cityRepository->findOneBy(["name" => "cityNotFound"]);

        foreach ($dataFile as $key => $line){
            $batch++;
            $propertyValue = $this->propertyRepository->findOneBy(["mutation_id" => $line["id_mutation"]]);

            if(!$propertyValue) {
                $propertyValue = new PropertyValue();
            }

            $propertyValue->setMutationId($line["id_mutation"])
                ->setMutationDate(new \DateTime('@'.strtotime($line["date_mutation"])))
                ->setPrice((int)$line["valeur_fonciere"])
                ->setPostalCode($line["code_postal"])
                ->setCityName($line["nom_commune"])
                ->setSaleType($line["nature_mutation"])
                ->setParcelleId($line["id_parcelle"])
                ->setBuildingType($line["type_local"])
                ->setSurfaceBuilding((int)$line["surface_reelle_bati"])
                ->setSurfaceField((int)$line["surface_terrain"])
                ->setPieceNumber((int)$line["nombre_pieces_principales"])
                ->setLatitude($line["latitude"])
                ->setLongitude($line["longitude"])
                ->setNumberLot((int)$line["nombre_lots"])
                ->setCodeNatureCulture($line["code_nature_culture"])
                ->setNatureCulture($line["nature_culture"])
                ->setCodeNatureCultureSpeciale($line["code_nature_culture_speciale"])
                ->setNatureCultureSpeciale($line["nature_culture_speciale"])
                ->setCity($this->cityRepository->findOneBy(["postal_code" => $line["code_postal"], "code" => (int) substr($line["code_commune"], -3)]) ?? $cityNotFound);
            $this->em->persist($propertyValue);

            if(($batch % 100) === 0) {
                print("\n\r".$key);
                $this->em->flush();
                $this->em->clear();
                $cityNotFound = $this->cityRepository->findOneBy(["name" => "cityNotFound"]);
                $batch = 0;
            }
        }

        $this->em->flush();
        $this->em->clear();
        return true;
    }

    private function downloadFileGz($urlFile, $directorySave): string
    {
        $ch = curl_init($urlFile);

        // Use basename() function to return
        // the base name of file
        $fileName = basename($urlFile);

        // Save file into file location
        $save_file_loc = $directorySave . $fileName;

        // Open file
        $fp = fopen($save_file_loc, 'wb');
        // It set an option for a cURL transfer
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // Perform a cURL session
        $error = curl_error($ch);
        curl_exec($ch);

        // Closes a cURL session and frees all resources
        curl_close($ch);
        // Close file
        fclose($fp);

        if($error){
            return false;
        }
        return $save_file_loc;
    }
}