<?php

/**
 * Abstract class for reading(fetching) a resource
 *
 * @package The-Datatank/model/resources/read
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */
abstract class AReader{

    public static $BASICPARAMS = array("callback", "filterBy","filterValue","filterOp","page");

    // package and resource are always the two minimum parameters
    protected $parameters = array();
    protected $requiredParameters = array();
    protected $package;
    protected $resource;
    protected $RESTparameters;

    public function __construct($package, $resource, $RESTparameters) {
        $this->package = $package;
        $this->resource = $resource;
        $this->RESTparameters = $RESTparameters;
        $this->getOntology();
    }

    public function getRESTParameters() {
        return $this->RESTparameters;
    }

    /**
     * execution method
     */
    public function execute(){
        if($this->isPagedResource() == 0){
            return $this->readNonPaged();
        }else{
            return $this->readPaged();
        }
    }

    /**
     * returns boolean wheter or not the resource is a paged one
     */
    abstract protected function isPagedResource();

    /**
     * read method of a non-paged resource
     */
    abstract public function readNonPaged();

    /**
     * read method of a paged resource
     */
    abstract public function readPaged();

    public function processParameters($parameters){
        /*
         * set the parameters
         */
        foreach ($parameters as $key => $value) {
            $this->setParameter($key, $value);
        }
    }

    abstract protected function setParameter($key, $value);

    protected function getOntology() {
        if (!OntologyProcessor::getInstance()->hasOntology($this->package)) {
             $filename = "custom/packages/" . $this->package . "/" . $this->package . ".ttl";
            if (file_exists($filename))
                OntologyProcessor::getInstance()->readOntologyFile($this->package, $filename);
        } 
    }
}
?>