<?php
/**
 * Created by PhpStorm.
 * User: mattemrick
 * Date: 11/28/15
 * Time: 7:59 PM
 */

namespace RTMatt\CSVImport\Tests;

class CSVCreateImporterTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        \Config::set('csvimport.importer_directory', __DIR__ . '/CommandImporters');

    }

    /** @test */
    public function it_can_be_called(){
        $this->clearDirectory();
        $test = $this->callCommand('test');
        $this->assertNotNull($test);

    }

    /** @test
     * @expectedException \RTMatt\CSVImport\Exceptions\CSVDirectoryNotWritableException
     */
    public function it_throws_exception_when_directory_is_not_writable(){
        \Config::set('csvimport.importer_directory', __DIR__ . '/CommandImportersLocked');
        $result = $this->callCommand('test');
        $this->assertFalse($result);

    }

    /** @test */
    public function it_adds_a_file_to_imports_directory(){
        $this->clearDirectory();
        \Config::set('csvimport.importer_directory', __DIR__ . '/CommandImporters');
        $precount =  count(scandir(config('csvimport.importer_directory')));
        $this->callCommand('test');
        $postcount = count(scandir(config('csvimport.importer_directory')));
        $this->assertEquals($postcount-1,$precount);
    }

    /** @test
     * @expectedException \RTMatt\CSVImport\Exceptions\CSVImporterExistsError
     */
    public function it_returns_error_when_file_exists(){
        $this->callCommand('test');
        $this->callCommand('test');
    }

    /** @test
     */
    public function it_creates_file_with_supplied_argument_as_part_of_name(){
        $this->clearDirectory();
        $fileName = $this->callCommandAndReturnFileName();
        $this->assertFileExists(config('csvimport.importer_directory') .'/'. $fileName);
    }



    /** @test */
    public function it_creates_a_php_class(){
        $this->clearDirectory();
        $fileName = $this->callCommandAndReturnFileName();
        $contents = file_get_contents(config('csvimport.importer_directory') .'/'. $fileName);
        $this->assertContains("<?php",$contents);
    }

    /** @test */
    public function it_creates_file_with_proper_namespace(){
        $this->clearDirectory();
        $fileName = $this->callCommandAndReturnFileName();
        $contents = file_get_contents(config('csvimport.importer_directory') .'/'. $fileName);
        $file_namespace_string = trim(config('csvimport.importer_namespace'),'\\');
        $this->assertContains("namespace ".$file_namespace_string,$contents);
    }

    /** @test */
    public function it_creates_stub_of_necessary_class(){
        $this->clearDirectory();
        $fileName = $this->callCommandAndReturnFileName('spaghetti_noodles');
        $contents = file_get_contents(config('csvimport.importer_directory') . '/'.$fileName);
        $this->assertContains("class SpaghettiNoodlesImporter extends CSVImporter",$contents);
        $this->assertContains("use RTMatt\\CSVImport\\CSVImporter;",$contents);
        $this->assertContains("protected function setResourceName()",$contents);
        $this->assertContains("protected function setTableName()",$contents);
        $this->assertContains("protected function setFieldString()",$contents);
    }

    //THIS WORKS IN PRODUCTION, BUT I DON'T KNOW HOW TO MAKE A LEGIT TEST FOR IT
    ///** @test */
    //public function it_creates_a_callable_class()
    //{
    //    $this->clearDirectory();
    //    \Config::set('csvimport.importer_namespace', "\\RTMatt\\CSVImport\\Tests\\");
    //    $fileName  = $this->callCommandAndReturnFileName('spaghetti_noodles');
    //    $namespace = trim(config('csvimport.importer_namespace') . 'SpaghettiNoodlesImporter', '\\');
    //    $instance  = new $namespace();
    //    $this->assertTrue(method_exists($instance, 'setResourceName'));
    //}


    /**
     * @return mixed
     */
    protected function callCommand($argument)
    {
        $test = \Artisan::call('csvimport:create', [
            'importer' => $argument
        ]);

        return $test;
    }


    /**
     * @return string
     */
    protected function callCommandAndReturnFileName($arg = 'test')
    {
        $argument = $arg;
        $this->callCommand($argument);
        $fileName = studly_case($argument) . 'Importer.php';

        return $fileName;
    }


    protected function clearDirectory()
    {
        $testFiles = scandir(config('csvimport.importer_directory'));
        foreach ($testFiles as $file) {
            if ( ! in_array($file, [ '.', '..' ])) {
                $file = config('csvimport.importer_directory') . '/' . $file;
                unlink($file);
            }
        }
    }


}