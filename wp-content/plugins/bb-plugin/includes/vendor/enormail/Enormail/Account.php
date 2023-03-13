<?php namespace Enormail;
/**
* Communicates with the Enormail Account API
* 
* @author Enormail
* @version 1.0
* @docs http://developers.enormail.eu/#api-account
*/
class Account extends Base {
    
    /**
    * Retrieves info about the connected account
    *
    * @access public
    * @return string (json|xml)
    */
    public function info()
    {
        return $this->rest->get("/account.{$this->format}");
    }
    
    /**
    * Retrieves the senders who are allowed to send 
    * on the connected account's behalf
    *
    * @access public
    * @return string (json|xml)
    */
    public function senders()
    {
        return $this->rest->get("/account/senders.{$this->format}");
    }
    
}