<?php

namespace App\Service;

use App\Tools\Encoding;

class ChangeFileExtensionService{

    public function changeGzToCsv(string $fileName): string
    {
        // Raising this value may increase performance
        $bufferSize = 4096; // read 4kb at a time
        $outFileName = str_replace('.gz', '', $fileName);

        // Open our files (in binary mode)
        $file = gzopen($fileName, 'rb');
        $outFile = fopen($outFileName, 'wb');

        // Keep repeating until the end of the input file
        while(!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            fwrite($outFile, gzread($file, $bufferSize));
        }

        // Files are done, close files
        fclose($outFile);
        gzclose($file);

        return $outFileName;
    }
}