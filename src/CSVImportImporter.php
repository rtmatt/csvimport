<?php

namespace RTMatt\CSVImport;

use RTMatt\CSVImport\Exceptions\CSVIncompleteImporterException;

abstract class CSVImportImporter
{

    protected $csv;

    protected $resource_name;

    protected $table_name;

    protected $field_string;

    private $succeeded;

    private $message;

    private $errors;


    function __construct($csv)
    {
        $this->errors = [ ];
        try {
            $this->resource_name = $this->setResourceName();
            $this->table_name    = $this->setTableName();
            $this->field_string  = $this->setFieldString();
        } catch (CSVIncompleteImporterException $e) {
            $this->succeeded = false;
            $this->errors[]  = $e->getMessage();
        }

        $this->csv           = $csv;
        $this->sql_data_path = config('csvimport.sql_directory') . '/';


    }


    public function import()
    {
        if ($this->succeeded !== false) {

            try {
                $this->processCSVFile();

                $this->resetTable();

                $this->runImportCommand();

                $this->postSQLImport();

                $this->deleteSQLPathCSV();
                $this->succeeded = true;
            } catch (\Exception $e) {
                if ($e instanceof \PDOException) {
                    $this->handleErrors($e);
                } else {
                    throw $e;
                }
            }

            $this->prepareMessage();
        }
    }


    public function message()
    {
        return $this->message;
    }


    protected function deleteSQLPathCSV()
    {
        if (\File::exists($this->sql_data_path . $this->resource_name . '.csv')) {
            \File::delete($this->sql_data_path . $this->resource_name . '.csv');
        }
    }


    protected function resetTable()
    {
        \DB::statement("SET FOREIGN_KEY_CHECKS = 0;");
        \DB::table($this->table_name)->truncate();
        \DB::statement("SET FOREIGN_KEY_CHECKS = 1;");
    }


    protected function processCSVFile()
    {
        $this->deleteSQLPathCSV();
        \File::copy($this->csv, $this->sql_data_path . '' . $this->resource_name . '.csv');
    }


    protected function runImportCommand()
    {
        \DB::connection()->getpdo()->exec($this->prepareImportCommand());
    }


    /**
     * @return string
     */
    protected function prepareImportCommand()
    {
        if ($command = $this->overrideImportCommand()) {
            return $command;
        }

        return "load data infile '" . $this->sql_data_path . $this->resource_name . ".csv' replace into table " . $this->table_name . "
            CHARACTER SET 'utf8'
            FIELDS TERMINATED BY ','
            optionally  ENCLOSED BY '\"'
            LINES TERMINATED BY '\r\n'
            IGNORE 2 LINES
            (" . $this->field_string . ");";
    }


    /**
     * @param $message
     *
     * @return string
     */
    protected function prepareMessage()
    {
        $this->message = $this->getImporterName() . ' Imported.';
    }


    public function succeeds()
    {
        return $this->succeeded;
    }


    public function fails()
    {
        return ! $this->succeeds();
    }


    public function errors()
    {
        $message = $this->getImporterName() . ' not imported:';
        foreach ($this->errors as $error) {
            $message .= ' ' . $error;
        }

        return $message;
    }


    abstract protected function setResourceName();


    /**
     * @return string
     */
    abstract protected function setTableName();


    /**
     * @return string
     */
    abstract protected function setFieldString();


    protected function postSQLImport()
    {
        return;
    }


    protected function overrideImportCommand()
    {
        return false;
    }


    protected function handleErrors($e)
    {
        $this->errors[] = $e->getMessage();
    }


    /**
     * @return string
     */
    protected function getImporterName()
    {

        $class = new \ReflectionClass(get_class($this));
        $name  = str_replace('Importer', '', $class->getShortName());
        $name = snake_case($name);
        $name = str_replace('_',' ',$name);
        $name = ucwords($name);
        return str_plural($name);
    }

}