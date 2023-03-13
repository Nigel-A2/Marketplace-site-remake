<?php namespace Enormail;

/**
 * The API Wrapper class
 *
 * This class mainly acts as wrapper for the different subclasses
 * in the Enormail API
 * 
 * @package Enormail API
 * @version 1.0
 * @author Enormail
 */
class ApiClient {
    
    protected $key = '';
    
    protected $transport = null;
    
    protected $format = 'json';
    
    /**
	 * The constructor
	 * 
	 * @access  public
	 * @return  nill
	 */
    public function __construct($key, $format = 'json')
    {
        // Set key
        $this->key = $key;
        
        // Set response type
        $this->format = (in_array($format, array('json', 'xml'))) ? $format : 'json';
        
        // Set transport
        $this->transport = new Rest($this->key);
    }
    
    /**
	 * Tests the API connection
	 * 
	 * @access  public
	 * @return  bool
	 */
    public function test()
    {
        return $this->transport->get('/ping.'.$this->format);
    }
    
    /**
	 * Get
	 * 
	 * @access  public
	 * @return  object (requested subclass of API)
	 */
    public function __get($name)
    {
        $class = 'Enormail\\'.ucfirst($name);
        
        $instance = new $class($this->transport);
        
        $instance->format = $this->format;
        
        return $instance;
    }
    
}