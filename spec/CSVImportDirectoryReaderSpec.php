<?php

namespace spec\RTMatt\CSVImport;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CSVImportDirectoryReaderSpec extends ObjectBehavior
{



    function it_is_initializable()
    {
        $this->shouldHaveType('RTMatt\CSVImport\CSVImportDirectoryReader');
    }


    public function it_throws_exception_when_directory_does_not_exist(){
        $this->shouldThrow('\RTMatt\CSVImport\Exceptions\CSVDirectoryNotFoundExcepton')->duringReadDirectory('nonsense');
    }

    public function it_gets_correct_set_of_files_from_directory()
    {
        CSVImportDirectoryTestHelper::clearDirectory(__DIR__ . '/testImportDirectory');
        $goodFiles   = [
            'FileInformationImporter.php',
            'UserImporter.php',
            'PoopImporter.php',
        ];
        $junk_files = [
            'nah.txt',
            'composer.json',
            'asdfasdfasdf',
            'FileInformationImporter.phpAndMoreTestAtTheEnd.php'

        ];
        $testFileSet = array_merge($goodFiles,$junk_files);
        CSVImportDirectoryTestHelper::buildDirectory(__DIR__ . '/testImportDirectory', $testFileSet);
        $read_files = $this::readDirectory(__DIR__ . '/testImportDirectory');
        $read_files->shouldBeArray();
        $read_files->shouldHaveCount(count($goodFiles));
        foreach($goodFiles as $good_file){
            $read_files->shouldContain($good_file);
        }
    }


    public function it_ignores_dot_files(){
        CSVImportDirectoryTestHelper::clearDirectory(__DIR__ . '/testImportDirectory');
        CSVImportDirectoryTestHelper::clearDirectory(__DIR__ . '/testImportDirectory');
        $goodFiles   = [
            'FileInformationImporter.php',
            'UserImporter.php',
            'PoopImporter.php'
        ];
        $junk_files = [
            '.FileInformationImporter.php',
            '.UserImporter.php',
            '.PoopImporter.php'

        ];
        $testFileSet = array_merge($goodFiles,$junk_files);
        CSVImportDirectoryTestHelper::buildDirectory(__DIR__ . '/testImportDirectory', $testFileSet);
        $read_files = $this::readDirectory(__DIR__ . '/testImportDirectory');
        $read_files->shouldBeArray();
        $read_files->shouldHaveCount(count($goodFiles));
        foreach($goodFiles as $good_file){
            $read_files->shouldContain($good_file);
        }
    }

}

class CSVImportDirectoryTestHelper
{

    public static function clearDirectory($directory)
    {

        $testFiles = scandir($directory);
        foreach ($testFiles as $file) {
            if ( ! in_array($file, [ '.', '..' ])) {
                $file = $directory . '/' . $file;
                unlink($file);
            }
        }
    }


    public static function buildDirectory($directory, $fileNames)
    {
        foreach ($fileNames as $file) {
            $fullName = $directory . '/' . $file;
            touch($fullName);
        }
    }
}
