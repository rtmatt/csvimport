<?php
/**
 * Created by PhpStorm.
 * User: mattemrick
 * Date: 11/27/15
 * Time: 12:14 PM
 */
namespace RTMatt\CSVImport\Tests;

use RTMatt\CSVImport\CSVImportController;

class CSVImportControllerTest extends \RTMatt\CSVImport\Tests\TestCase
{

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();



    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public function tearDown()
    {

        parent::tearDown();
    }

    /** @test */
    public function it_can_be_initialized()
    {\Config::set('csvimport.auth',false);
        $controller = new CSVImportController();
        $this->assertInstanceOf('\RTMatt\CSVImport\CSVImportController', $controller);
    }


    /** @test */
    public function it_provides_inputs_based_on_files_in_dedicated_folder()
    {
        \Config::set('csvimport.auth',false);
        \Route::controller('csv-import', '\RTMatt\CSVImport\Tests\ControllerStub');
        $this->call('GET', 'csv-import');
        $this->assertViewHas('fields',['user','file','poop','test']);

        return false;
    }

    /** @test */
    public function it_(){
        
    }
    



}

class ControllerStub extends \RTMatt\CSVImport\CSVImportController
{




    protected function getAvailableImporters()
    {
        return ['UserImporter.php','FileImporter.php','PoopImporter.php','TestImporter.php' ];
    }
}