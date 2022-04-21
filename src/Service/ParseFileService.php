<?php

namespace App\Service;

use App\Tools\Encoding;

class ParseFileService{
    public function parseCsv ($pathCsv)
    {
        //$this->log4Php->hclog(self::LOGGER, 'inf', '-- ENTER parseFileCsv --');

        $taille = 2048;
        $delimiteur = ";";
        $allContacts = [];
        $arrayPreparation = [];

        if(file_exists($pathCsv)){

            if($fp = fopen($pathCsv, 'rb')){

                $ligne = fgetcsv($fp, $taille);
                $labels = str_getcsv($ligne[0], $delimiteur);

                foreach($labels as $arrayKey => $value){
                    $value = Encoding::toUTF8($value);
                    $value = Encoding::fixUTF8($value);

                    $labels[$arrayKey] = $value;
                }

                while ($ligne = fgetcsv($fp, $taille)) {

                    foreach($ligne as $elem) {
                        $contact = str_getcsv($elem, $delimiteur);

                        foreach ($contact as $key => $value){
                            $value = Encoding::toUTF8($value);
                            $value = Encoding::fixUTF8($value);

                            $arrayPreparation[$labels[$key]] = $value;
                        }

                        $allContacts[] = $arrayPreparation;
                    }
                }

                fclose ($fp);
                return $allContacts;
            }

            $error = "Ouverture du fichier ".$pathCsv." impossible";
            print_r($error."\n\r");
            //$this->log4Php->hclog(self::LOGGER, 'err',  $error);
            return false;
        }

        $error = "Fichier ".$pathCsv." inexistant.";
        print_r($error."\n\r");
        //$this->log4Php->hclog(self::LOGGER, 'err',  $error);

        return false;
    }

    public function parseGz($file_name): array
    {
        $taille = 2048;
        $delimiteur = ";";
        $allContacts = [];
        $arrayPreparation = [];

        $gz = gzopen ( $file_name, 'r' );
        $buffer_size = 4096; // read 4kb at a time

        $labels = fgetcsv($gz, $taille);
        foreach($labels as $arrayKey => $value){
            $value = Encoding::toUTF8($value);
            $value = Encoding::fixUTF8($value);

            $labels[$arrayKey] = $value;
        }

        while ($line = fgetcsv($gz, $taille)) {

            foreach($line as $key => $value) {
                $value = Encoding::toUTF8($value);
                $value = Encoding::fixUTF8($value);
                $arrayPreparation[$labels[$key]] = $value;
            }

            $allContacts[] = $arrayPreparation;
        }

        gzclose($gz);

        return $allContacts;
    }
}