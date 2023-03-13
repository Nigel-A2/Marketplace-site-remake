<?php namespace Enormail;
/**
* The Enormail API wrapper base class
* 
* @author Enormail
* @version 1.0
* @docs http://developers.enormail.eu/
*/
abstract class Base {
    
    /**
	* Default response format
	*/
    public $format = 'json';
    
    /**
    * Constructor
    *
    * @access public
    * @param object $rest em_rest object
    * @return nill
    */
    public function __construct(Rest $rest)
    {
        $this->rest = $rest;
    }
    
}