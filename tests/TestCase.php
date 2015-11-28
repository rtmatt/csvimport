<?php
/**
 * Created by PhpStorm.
 * User: mattemrick
 * Date: 11/27/15
 * Time: 6:05 PM
 */

namespace RTMatt\CSVImport\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{

    protected function getPackageProviders($app)
    {
        return [ 'RTMatt\CSVImport\CSVImportServiceProvider' ];
    }


    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        \DB::beginTransaction();
        $this->artisan('migrate', [
            '--realpath' => realpath(__DIR__ . '/migrations'),
        ]);


    }


    public function tearDown()
    {

        \DB::statement("SET FOREIGN_KEY_CHECKS = 0;");
        \DB::statement("drop table if exists tests;");
        \DB::statement("drop table if exists migrations;");
        \DB::statement("SET FOREIGN_KEY_CHECKS = 1;");

        parent::tearDown();
    }


    protected function getPackageAliases($app)
    {
        return [
            'File' => 'Illuminate\Support\Facades\File'
        ];
    }


    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        putenv('DB_DATABASE=testing');
        putenv('DB_USERNAME=testing');
        putenv('DB_PASSWORD=testing');
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'testing',
            'username'  => 'testing',
            'password'  => 'testing',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ]);
    }


    public function testRunningMigration()
    {

        $tests = \DB::table('tests')->where('id', '=', 1)->first();
        $this->assertEquals('666', $tests->time);
        $this->assertEquals('integration', $tests->type);
        $this->assertEquals('Migration Testing', $tests->name);
    }
}