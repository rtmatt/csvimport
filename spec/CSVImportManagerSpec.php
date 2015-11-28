<?php

namespace spec\RTMatt\CSVImport;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RTMatt\CSVImport\CSVImporter;

class CSVImportManagerSpec extends ObjectBehavior
{

    function let(
        CSVImporter $one,
        CSVImporter $two,
        CSVImporter $three,
        CSVImporter $four
    ) {
        $one->import()->willReturn(' one imported.');
        $two->import()->willReturn(' two imported.');
        $three->import()->willReturn(' three imported.');
        $four->import()->willReturn(' four imported.');

    }


    function it_is_initializable()
    {
        $this->shouldHaveType('RTMatt\CSVImport\CSVImportManager');
    }


    function it_queues_Importers(CSVImporter $importer)
    {
        $this->beConstructedWith([ 'foo' => 0 ]);
        $this->queue($importer, 'foo');
        $this->ordered_imports->shouldHaveCount(1);

    }


    function it_runs_queued_importers(CSVImporter $importer)
    {
        $this->beConstructedWith([ 'foo' => 0 ]);
        $this->queue($importer, 'foo');
        $this->run();
        $importer->import()->shouldHaveBeenCalled();
    }


    //
    function it_queues_importers_in_correct_order(CSVImporter $importerone, CSVImporter $importertwo)
    {
        $this->beConstructedWith([ 'foo' => 0, 'bar' => 1 ]);
        $importerone->name = 'One';
        $importertwo->name = 'Two';
        $importerone->import()->willReturn(' One Imported.');
        $importertwo->import()->willReturn(' Two Imported.');
        $this->queue($importerone, 'bar');
        $this->queue($importertwo, 'foo');
        $this->run()->shouldReturn('Two Imported. One Imported.');
    }


    function it_runs_queues_with_no_order_set(CSVImporter $importerone, CSVImporter $importertwo)
    {
        $this->beConstructedWith([ ]);
        $importerone->name = 'One';
        $importertwo->name = 'Two';
        $importerone->import()->willReturn(' One Imported.');
        $importertwo->import()->willReturn(' Two Imported.');
        $this->queue($importerone, 'bar');
        $this->queue($importertwo, 'foo');
        $this->run()->shouldReturn('One Imported. Two Imported.');

    }


    function it_runs_ordered_queues_first_then_others(
        CSVImporter $one,
        CSVImporter $two,
        CSVImporter $three,
        CSVImporter $four
    ) {

        $this->beConstructedWith([ 'foo' => 0, 'bar' => 1 ]);
        $this->queue($one, 'foo');
        $this->queue($two);
        $this->queue($three);
        $this->queue($four, 'bar');
        $this->run()->shouldReturn('one imported. four imported. two imported. three imported.');
    }


    function it_runs_ordered_non_sequential_queues(
        CSVImporter $one,
        CSVImporter $two,
        CSVImporter $three,
        CSVImporter $four
    )
    {
        $this->beConstructedWith([ 'foo' => 0, 'bar' => 5 ]);
        $this->queue($one, 'foo');
        $this->queue($two);
        $this->queue($three);
        $this->queue($four, 'bar');
        $this->run()->shouldReturn('one imported. four imported. two imported. three imported.');
    }

}
