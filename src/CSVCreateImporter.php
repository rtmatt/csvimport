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

        $this->info('Attempting to create importer stub in ' . config('csvimport.importer_directory'));

        $fileName = studly_case($import_name) . "Importer.php";

        $path = config('csvimport.importer_directory') . '/';

        $full_file_path = $path . $fileName;

        $tempfile = tempnam($path, 'tmp');
        if (strpos($tempfile, '/tmp/') === 0) {
            throw new CSVDirectoryNotWritableException("Importer Directory is not writable");

            return false;
        }
        if (\File::exists($full_file_path)) {
            throw new CSVImporterExistsError('Importer File Already Exists');

            return false;
        }
        unlink($tempfile);
        $contents = "<?php\n\n";
        $contents .= $this->getFileContents($import_name)->render();

        $result = file_put_contents($full_file_path, $contents);
        $this->info($fileName . ' created.');
    }


    /**
     * @return string
     */
    protected function getFileContents($importer_name)
    {
        $namespace = trim(config('csvimport.importer_namespace'),'\\');
        return view("csvimport::importer_stub", compact('importer_name','namespace'));
    }


}