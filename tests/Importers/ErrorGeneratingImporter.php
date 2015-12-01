<?php

namespace RTMatt\CSVImport\Tests\Importers;

class ErrorGeneratingImporter extends \RTMatt\CSVImport\CSVImportImporter
{

    protected function setResourceName()
    {
        return "multiple words";
    }

    /**
     * @return string
     */
    protected function setTableName()
    {
        return "field_that_does_not_exist";
    }


    /**
     * @return string
     */
    protected function setFieldString()
    {
        return "name,type,time";
    }
}
