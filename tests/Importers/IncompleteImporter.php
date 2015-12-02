<?php

namespace RTMatt\CSVImport\Tests\Importers;

use RTMatt\CSVImport\CSVImportImporter;

class IncompleteImporter extends CSVImportImporter {

    protected function setResourceName()
    {
        //TODO: Implement this method
        throw new \RTMatt\CSVImport\Exceptions\CSVIncompleteImporterException("setResourceName method needs to be implemented");
    }

    protected function setTableName()
    {
        //TODO: Implement this method
        throw new \RTMatt\CSVImport\Exceptions\CSVIncompleteImporterException("setTableName method needs to be implemented");
    }


    protected function setFieldString()
    {
        //TODO: Implement this method
        throw new \RTMatt\CSVImport\Exceptions\CSVIncompleteImporterException("setFieldString method needs to be implemented");
    }


}