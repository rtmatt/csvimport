<?php
/**
 * Created by PhpStorm.
 * User: mattemrick
 * Date: 11/27/15
 * Time: 6:07 PM
 */

namespace RTMatt\CSVImport\Tests;

class CSVImporterTest extends TestCase
{

    protected $importer;

    protected $csv;


    public function setUp()
    {
        parent::setUp();
        $csv            = new \Symfony\Component\HttpFoundation\File\UploadedFile(__DIR__ . '/files/basic.csv',
            'basic.csv', null, null, null, true);
        $importer       = new ConcreteCSVImportImport($csv);
        $this->importer = $importer;
        $this->csv      = $csv;

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
        $this->assertEquals(' Tests Imported.', $message);
    }


    /** @test */
    public function it_runs_post_sql_commands()
    {
        $importer = new ConcreteCSVImportPostImport($this->csv);
        $importer->import();
        $records = \DB::table('tests')->get();
        foreach($records as $record){
            $this->assertEquals($record->name_time,$record->name.$record->time);
        }
    }
    
    /** @test */
    public function it_can_have_overridden_import_command(){
        $importer = new ConcreteCSVImportOverrideImport($this->csv);
        \DB::table('tests')->delete();
        $importer->import();
        $record = \DB::table('tests')->first();
        $this->assertEquals($record->name,'acceptance');
        $this->assertEquals($record->type,'30');
        $this->assertEquals($record->time,'J. Jeffery');
    }


}

class ConcreteCSVImportImport extends \RTMatt\CSVImport\CSVImportImporter
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
        $first     = \DB::table('tests')->where('id','=',1)->first();
        $records = \DB::table('tests')->get();
        foreach($records as $record){
            $name_time = $record->name . $record->time;
            \DB::table('tests')->where('id','=',$record->id)->update(['name_time'=>$name_time]);
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

class ConcreteCSVImportOverrideImport extends \RTMatt\CSVImport\CSVImportImporter
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

