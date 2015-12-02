<?php
/**
 * Created by PhpStorm.
 * User: mattemrick
 * Date: 11/27/15
 * Time: 6:07 PM
 */

namespace RTMatt\CSVImport\Tests;

use RTMatt\CSVImport\Tests\Importers\ErrorGeneratingImporter;


class CSVImporterTest extends TestCase
{
    /**
     * TESTS
     * it_can_be_initialized
     * it_does_not_cause_change_to_sql_directory
     * it_resets_database
     * it_imports_all_lines_in_basic_csv
     * it_imports_all_lines_in_basic_csv
     * it_returns_message_on_success
     * it_runs_post_sql_commands
     * it_can_have_overridden_import_command
     * //it_resets_db_state_upon_error
     *  it_has_message_on_success
     */


    protected $importer;

    protected $csv;


    public function setUp()
    {
        parent::setUp();
        $csv            = new \Symfony\Component\HttpFoundation\File\UploadedFile(__DIR__ . '/files/basic.csv',
            'basic.csv', null, null, null, true);
        $csv2  =  new \Symfony\Component\HttpFoundation\File\UploadedFile(__DIR__ . '/files/basic2.csv',
            'basic2.csv', null, null, null, true);
        $importer       = new ConcreteCSVImportImporter($csv);
        $this->importer = $importer;
        $this->csv      = $csv;
        $this->csv2 = $csv2;

    }


    /** @test */
    public function it_can_be_initialized()
    {
        $this->assertInstanceOf('\RTMatt\CSVImport\CSVImportImporter', $this->importer);
    }


    /** @test */
    public function it_does_not_cause_change_to_sql_directory()
    {
        $pre_contents = \File::files(config('csvimport.sql_directory'));
        $this->importer->import();
        $post_contents = \File::files(config('csvimport.sql_directory'));
        $this->assertEquals($pre_contents, $post_contents);
    }


    /** @test */
    public function it_resets_database()
    {
        $this->importer->import();
        $first_id = \DB::table('tests')->first()->id;
        $this->AssertEquals($first_id, 1);
    }


    /** @test */
    public function it_imports_all_lines_in_basic_csv()
    {
        $this->importer->import();
        $all_test_entries = \DB::table('tests')->get();
        $count            = count($all_test_entries);
        $this->assertEquals($count, 12);

        $tests = \DB::table('tests')->where('id', '=', 7)->first();

        $this->assertEquals('556', $tests->time);
        $this->assertEquals('acceptance', $tests->type);
        $this->assertEquals('Dan', $tests->name);
    }


    /** @test */
    public function it_returns_message_on_success()
    {
        $message = $this->importer->import();
        if($this->importer->succeeds()){
            $this->assertEquals('Concrete C S V Imports Imported.', $this->importer->message());
        }
    }


    /** @test */
    public function it_runs_post_sql_commands()
    {
        $importer = new ConcreteCSVImportPostImport($this->csv);
        $importer->import();
        $records = \DB::table('tests')->get();
        foreach ($records as $record) {
            $this->assertEquals($record->name_time, $record->name . $record->time);
        }
    }


    /** @test */
    public function it_can_have_overridden_import_command()
    {
        $importer = new ConcreteCSVImportOverrideImporter($this->csv);
        \DB::table('tests')->delete();
        $importer->import();
        $record = \DB::table('tests')->first();
        $this->assertEquals($record->name, 'acceptance');
        $this->assertEquals($record->type, '30');
        $this->assertEquals($record->time, 'J. Jeffery');
    }

    ///** @test */
    //public function it_resets_db_state_upon_error(){
    //    $importer = new ConcreteCSVImportOverrideImporter($this->csv);
    //    \DB::table('tests')->delete();
    //    $importer->import();
    //    $count = \DB::table('tests')->count();
    //    $this->assertTrue($count>0);
    //    var_dump($count);
    //    $failure = new \RTMatt\CSVImport\Tests\Importers\ErrorGeneratingImporter($this->csv);
    //    $failure->import();
    //    $count2 = \DB::table('tests')->count();
    //    var_dump($count2);
    //    $this->assertEquals($count,$count2);
    //}

    /** @test */
    public function it_has_state_and_message_on_success(){
        $importer = new ConcreteCSVImportOverrideImporter($this->csv);
        $importer->import();
        $this->assertTrue($importer->succeeds());
        $this->assertEquals($importer->message(),'Concrete C S V Import Overrides Imported.');
    }

    /** @test */
    public function it_has_state_and_errors_on_failure(){
        $failure = $this->runFailingImporter();
        $this->assertTrue($failure->fails());
        $this->assertNotNull($failure->errors());
    }

    /** @test */
    public function it_returns_importer_name_plus_errors_upon_error(){
        $failure = $this->runFailingImporter();
        $this->assertContains('Error Generatings not imported:',$failure->errors());
    }


    /**
     * @return ErrorGeneratingImporter
     * @throws \Exception
     */
    protected function runFailingImporter()
    {
        $failure = new ErrorGeneratingImporter($this->csv);
        $failure->import();

        return $failure;
    }


}

class ConcreteCSVImportImporter extends \RTMatt\CSVImport\CSVImportImporter
{

    protected function setResourceName()
    {
        return "tests";
    }


    /**
     * @return string
     */
    protected function setTableName()
    {
        return "tests";
    }


    /**
     * @return string
     */
    protected function setFieldString()
    {
        return "name,type,time";
    }
}

class ConcreteCSVImportPostImport extends \RTMatt\CSVImport\CSVImportImporter
{

    protected function setResourceName()
    {
        return "tests";
    }


    protected function postSQLImport()
    {
        $first   = \DB::table('tests')->where('id', '=', 1)->first();
        $records = \DB::table('tests')->get();
        foreach ($records as $record) {
            $name_time = $record->name . $record->time;
            \DB::table('tests')->where('id', '=', $record->id)->update([ 'name_time' => $name_time ]);
        }
    }


    /**
     * @return string
     */
    protected function setTableName()
    {
        return "tests";
    }


    /**
     * @return string
     */
    protected function setFieldString()
    {
        return "name,type,time";
    }
}

class ConcreteCSVImportOverrideImporter extends \RTMatt\CSVImport\CSVImportImporter
{

    protected function setResourceName()
    {
        return "tests";
    }


    protected function overrideImportCommand()
    {
        return "load data infile '" . $this->sql_data_path . $this->resource_name . ".csv' replace into table " . $this->table_name . "
            CHARACTER SET 'utf8'
            FIELDS TERMINATED BY ','
            optionally  ENCLOSED BY '\"'
            LINES TERMINATED BY '\r\n'
            IGNORE 2 LINES
            (@name,@type,@time)
            set
            name =@type,
            type=@time,
            time=@name;";
    }


    /**
     * @return string
     */
    protected function setTableName()
    {
        return "tests";
    }


    /**
     * @return string
     */
    protected function setFieldString()
    {
        return "name,type,time";
    }
}

