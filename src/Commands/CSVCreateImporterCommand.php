<?php
/**
 * Created by PhpStorm.
 * User: mattemrick
 * Date: 11/28/15
 * Time: 7:42 PM
 */

namespace RTMatt\CSVImport\Commands;

use Illuminate\Console\Command;
use RTMatt\CSVImport\Exceptions\CSVDirectoryNotWritableException;
use RTMatt\CSVImport\Exceptions\CSVImporterExistsError;

class CSVCreateImporterCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csvimport:make {importer}';

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
        $this->info('Attempting to make importer stub in ' . config('csvimport.importer_directory')."\n");

        list( $import_name, $fileName, $path, $full_file_path ) = $this->prepareDependentVariables();

        $this->validateFileState($path, $full_file_path);

        $contents = $this->getStubContents($import_name);

        file_put_contents($full_file_path, $contents);
        $this->info($fileName . " created.\n");
    }


    /**
     * @return string
     */
    protected function getFileContents($importer_name)
    {
        $namespace = trim(config('csvimport.importer_namespace'),'\\');
        return view("csvimport::importer_stub", compact('importer_name','namespace'));
    }


    /**
     * @return array|string
     */
    protected function getImporterName()
    {
        $import_name = $this->argument('importer');

        return $import_name;
    }


    /**
     * @param $import_name
     *
     * @return string
     */
    protected function getFileName($import_name)
    {
        $fileName = studly_case($import_name) . "Importer.php";

        return $fileName;
    }


    /**
     * @return string
     */
    protected function getImporterPath()
    {
        $path = config('csvimport.importer_directory') . '/';

        return $path;
    }


    /**
     * @return array
     */
    protected function prepareDependentVariables()
    {
        $import_name = $this->getImporterName();
        $fileName    = $this->getFileName($import_name);

        $path = $this->getImporterPath();

        $full_file_path = $path . $fileName;

        return [ $import_name, $fileName, $path, $full_file_path ];
    }


    /**
     * @param $path
     * @param $full_file_path
     *
     * @throws CSVDirectoryNotWritableException
     * @throws CSVImporterExistsError
     */
    protected function validateFileState($path, $full_file_path)
    {
        $tempfile = tempnam($path, 'tmp');
        if (strpos($tempfile, '/tmp/') === 0) {
            throw new CSVDirectoryNotWritableException("Importer Directory is not writable");
        }
        if (\File::exists($full_file_path)) {
            throw new CSVImporterExistsError('Importer File Already Exists');
        }
        unlink($tempfile);
    }


    /**
     * @param $import_name
     *
     * @return string
     */
    protected function getStubContents($import_name)
    {
        $contents = "<?php\n\n";
        $contents .= $this->getFileContents($import_name)->render();

        return $contents;
    }


}