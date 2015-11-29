<?php
/**
 * Created by PhpStorm.
 * User: mattemrick
 * Date: 11/27/15
 * Time: 12:14 PM
 */
namespace RTMatt\CSVImport\Tests;

use RTMatt\CSVImport\CSVImportController;
use RTMatt\CSVImport\Tests\TestCase;

class CSVImportControllerTest extends TestCase
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
    {
        \Config::set('csvimport.auth', false);
        $controller = new CSVImportController();
        $this->assertInstanceOf('\RTMatt\CSVImport\CSVImportController', $controller);
    }


    /** @test */
    public function it_optionally_authorizes_users()
    {
        \Config::set('csvimport.auth', true);
        $controller = new ControllerStub();
        $response   = $controller->getIndex()->render();
        $this->assertContains('Import CSV Content', $response);
    }


    /** @test */
    public function it_loads_empty_page_and_displays_error_when_directory_does_not_exist()
    {
        \Config::set('csvimport.importer_directory', __DIR__ . '/importeasdfasdfrs/');
        $this->call('GET', 'csv-import');
        $this->assertResponseOk();
        $this->assertViewHas('fields', [ ]);
       $this->assertContains('Import Directory Does Not Exist',$this->response->original->render());
    }


    /** @test
     * @expectedException \RTMatt\CSVImport\Exceptions\CSVIncompatibleUserException
     */
    public function it_throws_an_exception_when_user_is_lacking_method()
    {
        \Config::set('csvimport.auth', true);
        $controller = new ControllerStubIncompatibleUser();
        $controller->getIndex();
    }


    /** @test */
    public function it_provides_inputs_for_found_importers()
    {
        \Config::set('csvimport.importer_directory', __DIR__ . '/Importers');
        $this->call('GET', 'csv-import');
        $this->assertViewHas('fields', [ 'multiple_word', 'test' ]);
    }


    /** @test */
    public function it_loads_packaged_view_by_default()
    {
        $controller        = $this->getDefaultTestController();
        $view              = $controller->getIndex();
        $layout_file       = $view->getData()['layout'];
        $layout_view       = view($layout_file);
        $layout_path       = $layout_view->getPath();
        $pre_packaged_view = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..') . '/src/views/layout.blade.php';
        $this->assertEquals($layout_path, $pre_packaged_view);
    }


    /** @test */
    public function it_can_load_overridden_view()
    {
        $layout_override = 'welcome'; //view packaged with orchestra
        \Config::set('csvimport.override_layout_view', $layout_override);
        $controller  = $this->getDefaultTestController();
        $view        = $controller->getIndex();
        $layout_file = $view->getData()['layout'];
        $this->assertEquals($layout_file, $layout_override);

    }


    /** @test
     * @expectedException \RTMatt\CSVImport\Exceptions\CSVImportInvalidLayoutException
     */
    public function it_throws_exception_if_overridden_view_does_not_exist()
    {
        $layout_override = 'layouts.admin';
        \Config::set('csvimport.override_layout_view', $layout_override);
        $this->call('GET', 'csv-import');
    }


    /** @test */
    public function it_notifies_users_when_no_file_is_uploaded()
    {
        $this->call('POST', 'csv-import', [ ]);
        $this->assertResponseStatus(302);
        $this->assertSessionHasErrors();
    }


    /** @test
     * @expectedException \RTMatt\CSVImport\Exceptions\CSVImporterNotFoundException
     */
    public function it_throws_exception_when_no_importer_match_for_input()
    {
        $csv = new \Symfony\Component\HttpFoundation\File\UploadedFile(__DIR__ . '/files/basic.csv', 'basic.csv', null,
            null, null, true);
        $this->call('POST', 'csv-import', [ ], [ ], [ 'jhhkjhkasdf' => $csv ]);


    }


    /** @test */
    public function it_displays_a_message_on_successful_completion()
    {
        \Config::set('csvimport.importer_namespace', '\\RTMatt\\CSVImport\\Tests\\Importers\\');
        $csv = new \Symfony\Component\HttpFoundation\File\UploadedFile(__DIR__ . '/files/basic.csv', 'basic.csv', null,
            null, null, true);
        $this->call('POST', 'csv-import', [ ], [ ], [ 'test' => $csv ]);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('flash_message', 'Tests Imported.');
    }


    /** @test */
    public function it_instantiates_an_importer_for_each_submitted_file()
    {

        \Config::set('csvimport.importer_namespace', '\\RTMatt\\CSVImport\\Tests\\Importers\\');
        $csv = new \Symfony\Component\HttpFoundation\File\UploadedFile(__DIR__ . '/files/basic.csv', 'basic.csv', null,
            null, null, true);
        $this->call('POST', 'csv-import', [ ], [ ], [ 'test' => $csv, 'multiple_word' => $csv ]);
        $this->assertResponseStatus(302);
        $this->assertSessionHas('flash_message', 'Tests Imported. Multiple Words Imported.');


    }


    /**
     * @return ControllerStub
     */
    protected function getDefaultTestController()
    {
        \Config::set('csvimport.auth', false);
        $controller = new ControllerStub();

        return $controller;
    }


}

class ControllerStub extends \RTMatt\CSVImport\CSVImportController
{

    protected function getCurrentUser()
    {
        return new AuthorizableUser();
    }
}

class ControllerStubIncompatibleUser extends ControllerStub
{

    protected function getCurrentUser()
    {
        return new \stdClass();
    }
}

class AuthorizableUser
{

    public function can_import()
    {
        return true;
    }
}



