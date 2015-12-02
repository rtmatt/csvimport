<?php

namespace RTMatt\CSVImport\Tests\Importers;

use RTMatt\CSVImport\CSVImportImporter;

class IncompleteFieldImporter extends CSVImportImporter {

    protected function setResourceName()
    {
        return 'neat';
    }

    protected function setTableName()
    {
        return 'neat';
    }


    protected function setFieldString()
    {
        //TODO: Implement this method
        throw new \RTMatt\CSVImport\Exceptions\CSVIncompleteImporterException("setFieldString method needs to be implemented");
    }


}