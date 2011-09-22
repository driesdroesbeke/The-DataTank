<?php

/**
 * This class generates RDF output for the retrieved data using the stored mapping.
 * 
 * @package The-Datatank/model
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
 */
class RDFOutput {

    private static $uniqueinstance;
    private $model;
    private $resource;
    private $container;

    private function __construct() {
        
    }

    public static function getInstance() {
        if (!isset(self::$uniqueinstance)) {
            self::$uniqueinstance = new RDFOutput();
        }
        return self::$uniqueinstance;
    }

    /**
     * Removes a mapping between TDT Resource and a class.
     *
     * @param	object $object
     * @return	Model returns an onthology of $object
     * @access	public
     */
    public function buildRdfOutput($object) {
        $this->model = ModelFactory::getResModel(MEMMODEL);

        $this->analyzeVariable($object);

        return $this->model;
    }

    /**
     * Recursive function for analyzing an object and building its path
     *
     * @param	Mixed $var
     * @param	string OPTIONAL $path
     * @access	private
     */
    private function analyzeVariable($var, $path='') {
        if (is_array($var)) {
            $temp = $path;
            $this->addToModel($path, $var);
            for ($i = 0; $i < count($var); $i++) {
                $path = $temp;
                $path .= '/' . $i;
                $this->analyzeVariable($var[$i], $path);
            }
        } else if (is_object($var)) {
            $obj_prop = get_object_vars($var);
            $temp = $path;
            $this->addToModel($path);
            foreach ($obj_prop as $prop => $value) {
                $path = $temp;
                $path .= '/' . $prop;
                $this->analyzeVariable($value, $path);
            }
        } else {
            $this->addToModel($path, $var);
            $path = '';
        }
    }

    /**
     *
     * @param	string $path
     * @param	string OPTIONAL $value
     * @access	private
     */
    private function addToModel($path, $value=null) {
        //Miel: need full path for adding semantics!!
        $uri = RequestURI::getInstance()->getURI();
        echo $uri . '<br>';
        $uri = $uri[0] . $path;

        //If no value is given, the $path 
        if (is_null($value)) {
            //Create a resource for this object
            $this->resource = $this->model->createResource($uri);
            //Get the right mapping class
            $rdfmapper = new RDFMapper();
            $mapping_resource = $rdfmapper->getResourceMapping(RequestURI::getInstance()->getPackage(), $uri);
            //Define the type of this resource in RDF
            $this->resource->addProperty(RDF_RES::TYPE(), $mapping_resource);

            //If a container exists, this resource is part of it, so add.
            if (!is_null($this->container))
                $this->container->add($this->resource);
        } else {
            if (is_array($value)) {
                $this->container = $this->model->createSeq($uri);
            } else {
                $property = $this->model->createProperty($uri);
                $literal = $this->model->createTypedLiteral($value, $this->mapLiteral($value));
                $this->resource->addProperty($property, $literal);
            }
        }
    }

    /**
     *  Map the datatype of a primitive type to the right indication string for RAP API
     * 
     * @param	string $var
     * @return string 
     * @access	private
     */
    private function mapLiteral($var) {

        $type = '';
        if (is_int($var))
            $type = 'INT';
        else if (is_bool($var))
            $type = 'BOOLEAN';
        else if (is_float($var))
            $type = 'DECIMAL';
        else
            $type = 'STRING';
        return DATATYPE_SHORTCUT_PREFIX . $type;
    }

}

?>