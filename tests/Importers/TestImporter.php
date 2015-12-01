<?php

namespace RTMatt\CSVImport\Tests\Importers;

use RTMatt\CSVImport\CSVImportImporter;

class TestImporter extends CSVImportImporter
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