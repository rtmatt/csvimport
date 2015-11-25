<?php

namespace RTMatt\CSVImport;

abstract class CSVImporter
{

    protected $csv;

    protected $resource_name;

    protected $table_name;

    protected $field_string;


    function __construct($csv)
    {
        $this->resource_name = $this->setResourceName();
        $this->table_name    = $this->setTableName();
        $this->field_string  = $this->setFieldString();
        $this->csv           = $csv;
        $this->sql_data_path = config('csvimport.sql_directory') . '/';
    }


    public function import($message)
    {

        $this->processCSVFile();

        $this->resetTable();

        $this->runImportCommand();

        $this->postSQLImport();

        return $this->prepareMessage($message);
    }


    protected function deletePreExistingCsv()
    {
        if (\File::exists($this->sql_data_path . $this->resource_name . '.csv')) {
            \File::delete($this->sql_data_path . $this->resource_name . '.csv');
        }
    }


    protected function resetTable()
    {
        \DB::table($this->table_name)->delete();
    }


    protected function processCSVFile()
    {
        $this->deletePreExistingCsv();
        $this->csv->move($this->sql_data_path, '' . $this->resource_name . '.csv');
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
    protected function prepareMessage($message)
    {
        return $message . ' ' . ucwords(str_ireplace('_', ' ', $this->resource_name)) . ' Imported.';
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


}