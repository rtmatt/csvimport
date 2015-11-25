<?php
/**
 * Created by PhpStorm.
 * User: mattemrick
 * Date: 11/24/15
 * Time: 2:16 PM
 */

namespace RTMatt\CSVImport;

class CSVImportManager
{

    protected $message = "";

    protected $queue = [ ];

    /**
     * @var array
     */
    private $queue_order;


    function __construct(array $queue_order)
    {
        $this->queue_order = $queue_order;
    }


    public function queue(CSVImporter $importer, $key)
    {
        $this->queue[$this->getImporterOrder($key)] = $importer;
    }


    public function run()
    {
        ksort($this->queue);
        foreach ($this->queue as $importer) {
            $this->message = $importer->import($this->message);
        }
        return $this->message;
    }


    private function getImporterOrder($key)
    {
        return $this->queue_order[$key];
    }


}