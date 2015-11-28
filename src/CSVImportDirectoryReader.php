<?php
/**
 * Created by PhpStorm.
 * User: mattemrick
 * Date: 11/27/15
 * Time: 1:24 PM
 */

namespace RTMatt\CSVImport;

class CSVImportDirectoryReader
{

    public static function readDirectory($directory)
    {
        $all_files      = array_diff(scandir($directory), [ '.', '..', '.gitkeep' ]);
        $importer_files = array_filter($all_files, function ($file) {
            $split = explode('Importer.php', $file);

            if (count($split) !== 2) {
                return false;
            }
            if ($split[1] !== '') {
                return false;
            }

            return true;
        });

        return $importer_files;
    }

}