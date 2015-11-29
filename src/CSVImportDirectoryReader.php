<?php
/**
 * Created by PhpStorm.
 * User: mattemrick
 * Date: 11/27/15
 * Time: 1:24 PM
 */

namespace RTMatt\CSVImport;

use RTMatt\CSVImport\Exceptions\CSVDirectoryNotFoundExcepton;

class CSVImportDirectoryReader
{

    public static function readDirectory($directory)
    {
        if ( ! realpath($directory)) {
            throw new CSVDirectoryNotFoundExcepton ('Import Directory Does Not Exist at ' . $directory);
        }

        $all_files = glob($directory . '/*.php');

        $matches = [ ];
        foreach ($all_files as $file) {
            if (preg_match("/\A.*\/{1}([a-zA-Z]*Importer.php\z)/", $file, $catch)) {
                $matches[] = $catch[1];
            }
        }

        return $matches;
    }

}