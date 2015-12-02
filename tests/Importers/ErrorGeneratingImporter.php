<?php

namespace RTMatt\CSVImport\Tests\Importers;

class ErrorGeneratingImporter extends \RTMatt\CSVImport\CSVImportImporter
{

    protected function setResourceName()
    {
        return "Error Generating";
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
        return "field_not_existing,error_generating,not_real";
    }
}
