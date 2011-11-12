<?php
/**
 * This is a class which will return all the packages in The DataTank
 * 
 * @package The-Datatank/packages/TDTInfo
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

class TDTInfoPackages extends AReader{

    public static function getParameters(){
	return array();
    }

    public static function getRequiredParameters(){
	return array();
    }

    public function setParameter($key,$val){
    }

    public function readPaged(){
	$resmod = ResourcesModel::getInstance();
        $doc = $resmod->getAllDoc();
        $packages = array();
	$packagenames = array_keys(get_object_vars($doc));
        foreach($packagenames as $packagename){
            $package = new stdClass();
            $package->name = $packagename;
            $package->creation_date = $doc->$packagename->creation_date;
            $packages[] = $package;
        }
        
	return $packages;
    }
    
    public function readNonPaged(){
        return $this->readPaged();
    }

    protected function isPagedResource(){
        return false;
    }
    
    public static function getDoc(){
	return "This resource contains every package installed on this DataTank instance.";
    }
    

}

?>
