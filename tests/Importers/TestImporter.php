<?php

namespace RTMatt\CSVImport\Tests\Importers;

class TestImporter extends \RTMatt\CSVImport\CSVImporter
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
