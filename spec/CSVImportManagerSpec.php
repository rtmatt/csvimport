<?php

namespace spec\RTMatt\CSVImport;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CSVImportManagerSpec extends ObjectBehavior
{

    function let($one, $two, $three, $four, $failure,$failure2)
    {
        $one->beADoubleOf('\RTMatt\CSVImport\CSVImportImporter');
        $two->beADoubleOf('\RTMatt\CSVImport\CSVImportImporter');
        $three->beADoubleOf('\RTMatt\CSVImport\CSVImportImporter');
        $four->beADoubleOf('\RTMatt\CSVImport\CSVImportImporter');
        $failure->beADoubleOf('\RTMatt\CSVImport\CSVImportImporter');
        $failure2->beADoubleOf('\RTMatt\CSVImport\CSVImportImporter');

        $one->message()->willReturn('One Imported.');
        $two->message()->willReturn('Two Imported.');
        $three->message()->willReturn('Three Imported.');
        $four->message()->willReturn('Four Imported.');
        $failure->message()->willReturn('Failure Imported.');
        $failure2->message()->willReturn('Failure Imported.');

        $one->import()->willReturn(null);
        $two->import()->willReturn(null);
        $three->import()->willReturn(null);
        $four->import()->willReturn(null);
        $failure->import()->willReturn(null);
        $failure2->import()->willReturn(null);

        $one->succeeds()->willReturn(true);
        $two->succeeds()->willReturn(true);
        $three->succeeds()->willReturn(true);
        $four->succeeds()->willReturn(true);
        $failure->succeeds()->willReturn(false);
        $failure2->succeeds()->willReturn(false);
        $one->fails()->willReturn(false);
        $two->fails()->willReturn(false);
        $three->fails()->willReturn(false);
        $four->fails()->willReturn(false);
        $failure->fails()->willReturn(true);
        $failure2->fails()->willReturn(true);

        $failure->errors()->willReturn('Failures not imported: 503 Fake Error.');
        $failure2->errors()->willReturn('Failure2 not imported: 501 Faake Errors.');

    }


    function it_is_initializable()
    {
        $this->shouldHaveType('RTMatt\CSVImport\CSVImportManager');
    }


    function it_queues_Importers($one)
    {
        $this->beConstructedWith([ 'foo' => 0 ]);
        $this->queue($one, 'foo');
        $this->ordered_imports->shouldHaveCount(1);

    }


    function it_runs_queued_importers($two)
    {

        $this->beConstructedWith([ 'foo' => 0 ]);
        $this->queue($two, 'foo');
        $this->run();

    }


    //
    function it_queues_importers_in_correct_order($one, $two)
    {
        $this->beConstructedWith([ 'foo' => 0, 'bar' => 1 ]);

        $this->queue($one, 'bar');
        $this->queue($two, 'foo');
        $this->run();
        $this->messages()->shouldReturn('Two Imported. One Imported.');


    }


    function it_runs_queues_with_no_order_set($one, $two)
    {
        $this->beConstructedWith();

        $this->queue($one, 'bar');
        $this->queue($two, 'foo');
        $this->run();
        $this->messages()->shouldReturn('One Imported. Two Imported.');

    }


    function it_runs_ordered_queues_first_then_others($one, $two, $three, $four)
    {

        $this->beConstructedWith([ 'foo' => 0, 'bar' => 1 ]);
        $this->queue($one, 'foo');
        $this->queue($two);
        $this->queue($three);
        $this->queue($four, 'bar');
        $this->run();
        $this->messages()->shouldReturn('One Imported. Four Imported. Two Imported. Three Imported.');
    }


    function it_runs_ordered_non_sequential_queues($one, $two, $three, $four)
    {
        $this->beConstructedWith([ 'foo' => 0, 'bar' => 5 ]);
        $this->queue($one, 'foo');
        $this->queue($two);
        $this->queue($three);
        $this->queue($four, 'bar');
        $this->run();
        $this->messages()->shouldReturn('One Imported. Four Imported. Two Imported. Three Imported.');
    }


    function it_reports_errors($failure,$failure2)
    {
        $this->queue($failure);
        $this->queue($failure2);
        $this->run();
        $this->errors()->shouldReturn('Failures not imported: 503 Fake Error. Failure2 not imported: 501 Faake Errors.');
    }

    function it_can_report_success_and_error_messages($one, $two, $three, $four,$failure,$failure2){
        $this->beConstructedWith();
        foreach(func_get_args() as $importer){
            $this->queue($importer);
        }
        $this->run();
        $this->errors()->shouldReturn('Failures not imported: 503 Fake Error. Failure2 not imported: 501 Faake Errors.');
        $this->messages()->shouldReturn('One Imported. Two Imported. Three Imported. Four Imported.');

    }

}
