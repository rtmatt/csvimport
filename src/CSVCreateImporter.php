<?php
/**
 * Created by PhpStorm.
 * User: mattemrick
 * Date: 11/28/15
 * Time: 7:42 PM
 */

namespace RTMatt\CSVImport;

use Illuminate\Console\Command;
use RTMatt\CSVImport\Exceptions\CSVDirectoryNotWritableException;
use RTMatt\CSVImport\Exceptions\CSVImporterExistsError;

class CSVCreateImporter extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csvimport:create {importer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Importer Class';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $import_name = $this->argument('importer');

        $this->info('Attempting to create importer stub in '.config('csvimport.importer_directory'));

        $fileName = studly_case($import_name) . "Importer.php";

        if (\File::exists(config('csvimport.importer_directory') . $fileName)) {
            throw new CSVImporterExistsError('Importer File Already Exists');
        }

        if ( ! \File::isWritable(config('csvimport.importer_directory'))) {
            throw new CSVDirectoryNotWritableException("Importer Directory is not writable");
        }
        $contents = "<?php\n";
        $contents .= $this->getFileContents($import_name)->render();

        \File::put(config('csvimport.importer_directory') . $fileName, $contents);

        $this->info($fileName.' created.');
    }


    /**
     * @return string
     */
    protected function getFileContents($importer_name)
    {
        return view("csvimport::importer_stub", compact('importer_name'));
    }


}