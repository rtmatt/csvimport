<?php

namespace tests\RTMatt\CSVImport;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CSVImporterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('RTMatt\CSVImport\CSVImporter');
    }
}
