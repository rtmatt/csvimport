<?php

namespace RTMatt\CSVImport\Tests\Importers;

class MultipleWordImporter extends \RTMatt\CSVImport\CSVImportImporter
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
