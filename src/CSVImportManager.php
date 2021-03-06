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

    public $queue_order;

    protected $message = "";

    public $ordered_imports;

    public $unordered_imports;

    public $queue = [ ];

    private $errors;

    private $messages;


    function __construct(array $queue_order = [])
    {
        $this->queue_order = $queue_order;
        $this->ordered_imports = [];
        $this->unordered_imports = [];
        $this->errors = [];
        $this->messages = [];

    }


    public function queue(CSVImportImporter $importer, $key=null)
    {
        $queue_key = $this->getImporterOrder($key);

        if ($queue_key!==null) {
            return $this->ordered_imports[$queue_key] = $importer;
        }
        return $this->unordered_imports[] = $importer;

    }


    public function run()
    {
        ksort($this->ordered_imports);
        foreach ($this->ordered_imports as $importer) {
            $this->runImporter($importer);
        }
        foreach ($this->unordered_imports as $importer2) {
            $this->runImporter($importer2);
        }
        return trim($this->message);
    }


    private function getImporterOrder($key)
    {

        if (array_key_exists($key, $this->queue_order)) {
             return $this->queue_order[$key];
        }

        return null;

    }

    public function messages()
    {
        if(!count($this->messages)){
            return null;
        }
        return implode(' ',$this->messages);
    }


    /**
     * @param $importer
     */
    protected function runImporter($importer)
    {
        $importer->import();
        if ($importer->fails()) {
            $this->errors[] = $importer->errors();
        } else {
            $this->messages[] = $importer->message();
        }
    }

    public function errors()
    {
        if(!count($this->errors)){
            return null;
        }
        return implode(' ',$this->errors);
    }


}