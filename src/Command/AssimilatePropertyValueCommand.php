<?php

namespace App\Command;

use App\Entity\City;
use App\Entity\PropertyValue;
use App\Repository\CityRepository;
use App\Repository\DepartmentRepository;
use App\Repository\PropertyValueRepository;
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

    public function __construct(
        EntityManagerInterface  $entityManager,
        ParseFileService        $parseFile,
        CityRepository          $cityRepository,
        PropertyValueRepository $propertyRepository,
        DepartmentRepository    $departmentRepository
    )
    {
        parent::__construct();
        $this->em = $entityManager;
        $this->parseFile = $parseFile;
        $this->cityRepository = $cityRepository;
        $this->propertyRepository = $propertyRepository;
        $this->departmentRepository = $departmentRepository;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->em->getConnection()->getConfiguration()->setSQLLogger();

        $year = "2021";
        $departments = $this->departmentRepository->findAll();
        $alReadyTreated = [
            "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24",
            "25", "26", "27", "28", "29", "30", "2A", "2B", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40", "41", "42", "43", "44", "45", "46",
            "47", "48", "49", "50", "51", "52", "53", "54", "55", "56", "57", "58", "59", "60", "61", "62", "63", "64", "65", "66", "67", "68", "69", "70",
            "71", "72", "73", "74", "75", "76", "77", "78", "79", "80", "81", "82", "83", "84", "85", "86", "87", "88", "89", "90", "91", "92", "93", "94",
        ];

        foreach ($departments as $department){
            $code_department = $department->getCode();

            if(!in_array($code_department, $alReadyTreated, true)){
                $url_file = "https://files.data.gouv.fr/geo-dvf/latest/csv/$year/departements/$code_department.csv.gz";

                $directory_save = '/var/www/sites/projetperso/DataCity/public/csv/PropertyValue/';
                $file_name = $this->downloadFileGz($url_file, $directory_save);

                if($file_name){
                    print_r("Download of $url_file");
                    print_r($this->treatment($file_name));
                }else{
                    print_r("Error with $url_file");
                }
            }
            $this->em->clear();
        }

        return 1;
    }

    private function treatment($file_name): string
    {
        $dataFile = $this->parseFile->parseGz($file_name);

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
                ->setCity($this->cityRepository->findOneBy(["postal_code" => $line["code_postal"], "code" => substr($line["code_commune"], -3)]) ?? $cityNotFound);
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

    private function downloadFileGz($url_file, $directory_save): string
    {
        $ch = curl_init($url_file);

        // Use basename() function to return
        // the base name of file
        $file_name = basename($url_file);

        // Save file into file location
        $save_file_loc = $directory_save . $file_name;

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